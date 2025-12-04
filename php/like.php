<?php
/*
 objetivo desse like
 1. Verifica autenticação
 2. Verifica se já existe curtida desse usuário nesse post
  3. Se existe → remove a curtida (descurtir)
 4. Se não existe → adiciona curtida
  5. Atualiza contador na postagem
  6. Cria/remove notificação para o dono do post
  7. Redireciona (ou retorna JSON para AJAX)
 s
  DOIS MODOS DE USO:
 tb aceita ajx e php
 */


session_start();
require_once __DIR__ . '/conexao.php';


mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT); //mysqli_report() = configura como o MySQLi reporta erros, ...STRICIT = lança exceptions (em vez de warnings)


if (!isset($_SESSION['id'])) {
    /*
     * Suporta dois fluxos:
     * - Se tem redirect_to → é formulário HTML, redireciona
     * - Se não tem → é AJAX, retorna JSON
     */
    if (!empty($_POST['redirect_to'])) {
        header('Location: ' . $_POST['redirect_to']);
        exit;
    }
    http_response_code(401); // 401 = Unauthorized
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['success' => false, 'error' => 'não autenticado']);
    exit;
}


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // 405 = Method Not Allowed
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['success' => false, 'error' => 'método inválido']);
    exit;
} //Curtidas só podem ser alteradas via POST, get le dados e nao queremos isso kakak


$post_id = intval($_POST['post_id'] ?? 0);//intval() = converte para inteiro
$user_id = intval($_SESSION['id']);//?? 0 = valor padrão se não existir

if ($post_id <= 0) {
    if (!empty($_POST['redirect_to'])) {
        header('Location: ' . $_POST['redirect_to']);
        exit;
    }
    http_response_code(400); // 400 = Bad Request
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['success' => false, 'error' => 'post inválido']);
    exit;
}


$stmt = $conn->prepare('SELECT id FROM curtidas WHERE id_postagem = ? AND id_usuario = ? LIMIT 1');
$stmt->bind_param('ii', $post_id, $user_id);
$stmt->execute();

$stmt->bind_result($like_row_id);

$exists = $stmt->fetch();
$stmt->close();


//agora é para saber o dono da postagem
$post_owner_id = null;

$owner = $conn->prepare('SELECT id_usuario FROM postagens WHERE id = ? LIMIT 1');
if ($owner) {
    $owner->bind_param('i', $post_id);
    $owner->execute();
    $owner->bind_result($owner_id_tmp);
    if ($owner->fetch()) {
        $post_owner_id = intval($owner_id_tmp);
    }
    $owner->close();
}


try {
    $conn->begin_transaction(); //Usamos transação para garantir que todas as operações
    
    if ($exists) {

        $del = $conn->prepare('DELETE FROM curtidas WHERE id_postagem = ? AND id_usuario = ?');
        $del->bind_param('ii', $post_id, $user_id);
        $del->execute();
        $del->close(); //reomve o cvurtir da tabela

       
        $upd = $conn->prepare('UPDATE postagens SET num_curtidas = GREATEST(0, num_curtidas - 1) WHERE id = ?');
        $upd->bind_param('i', $post_id);
        $upd->execute();
        $upd->close(); //atualiza o contador de curtidas da tabela



        if ($post_owner_id && $post_owner_id !== $user_id) {
            try {
                $tipo = 'like';
                $delNotif = $conn->prepare('DELETE FROM notificacoes WHERE tipo = ? AND id_postagem = ? AND id_usuario_destino = ? AND id_usuario_origem = ?');
                if ($delNotif) {
                    $delNotif->bind_param('siii', $tipo, $post_id, $post_owner_id, $user_id);
                    $delNotif->execute();
                    $delNotif->close();
                }
            } catch (Throwable $e) {
                // Tabela pode não existir - log e continua
                error_log('[like.php] Erro ao remover notificação: ' . $e->getMessage());
            }
        } //isso é mais pra se eu quiser notificacoa

        $action = 'unliked'; // Para o retorno JSON

    } else {
   
        
        /*
         * Insere nova curtida
         * Nota: algumas tabelas têm coluna data_criacao, outras não.
         * Por isso removemos essa coluna do INSERT para compatibilidade.
         */
        $ins = $conn->prepare('INSERT INTO curtidas (id_postagem, id_usuario) VALUES (?, ?)');
        $ins->bind_param('ii', $post_id, $user_id);
        $ins->execute();
        $ins->close();

        // Incrementa o contador de curtidas
        $upd = $conn->prepare('UPDATE postagens SET num_curtidas = num_curtidas + 1 WHERE id = ?');
        $upd->bind_param('i', $post_id);
        $upd->execute();
        $upd->close();

        /*
         * Cria notificação para o dono do post
         * (só se não for o próprio usuário curtindo seu post)
         */
        if ($post_owner_id && $post_owner_id !== $user_id) {
            try {
                $tipo = 'like';
                $notif = $conn->prepare('INSERT INTO notificacoes (id_usuario_destino, id_usuario_origem, tipo, id_postagem, lida, data_criacao) VALUES (?, ?, ?, ?, 0, NOW())');
                if ($notif) {
                    $notif->bind_param('iisi', $post_owner_id, $user_id, $tipo, $post_id);
                    $notif->execute();
                    $notif->close();
                }
            } catch (Throwable $e) {
                // Tabela pode não existir - log e continua
                error_log('[like.php] Erro ao inserir notificação: ' . $e->getMessage());
            }
        }

        $action = 'liked'; // Para o retorno JSON
    }


    /*
     * Busca o total atualizado para retornar ao cliente
     * Útil para atualizar a UI sem recarregar a página
     */
    $q = $conn->prepare('SELECT num_curtidas FROM postagens WHERE id = ? LIMIT 1');
    $q->bind_param('i', $post_id);
    $q->execute();
    $q->bind_result($num_curtidas_total);
    $q->fetch();
    $total = intval($num_curtidas_total ?? 0);
    $q->close();

    // Confirma todas as operações
    $conn->commit();

   
    if (!empty($_POST['redirect_to'])) {
        $redirect = $_POST['redirect_to'];
        
        /*
         * Adiciona timestamp para evitar cache do navegador
         * 
         * Problema: O navegador pode cachear a página e não
         * mostrar o novo número de curtidas.
         * 
         * Solução: Adicionar ?t=timestamp ou &t=timestamp
         * para forçar uma requisição "nova"
         * 
         * strpos() = encontra posição de uma substring
         *   strpos($url, '?') === false → não tem query string
         */
        if (strpos($redirect, '?') === false) {
            // Não tem query string
            if (strpos($redirect, '#') !== false) {
                // Tem âncora (#post-123), insere antes dela
                $parts = explode('#', $redirect, 2);
                $redirect = $parts[0] . '?t=' . time() . '#' . $parts[1];
            } else {
                $redirect .= '?t=' . time();
            }
        } else {
            // Já tem query string, adiciona com &
            if (strpos($redirect, '#') !== false) {
                $parts = explode('#', $redirect, 2);
                $redirect = $parts[0] . '&t=' . time() . '#' . $parts[1];
            } else {
                $redirect .= '&t=' . time();
            }
        }
        
        header('Location: ' . $redirect);
        exit;
    }

   
    echo json_encode(['success' => true, 'action' => $action, 'total' => $total]);
    exit;

} catch (Throwable $e) {

    /*
     * Se qualquer coisa falhou, desfaz a transação
     */
    $conn->rollback();
    
    $msg = $e->getMessage();
    $code = $e->getCode();
    
    /*
     * Log de erro em arquivo para debug
     * FILE_APPEND = adiciona ao final do arquivo
     */
    file_put_contents(
        __DIR__ . '/../debug_like_error.txt', 
        date('Y-m-d H:i:s') . " - Erro: " . $msg . "\n", 
        FILE_APPEND
    );
    
    error_log('[like.php] Throwable: code=' . $code . ' msg=' . $msg . "\n" . $e->getTraceAsString());
    
    if (!empty($_POST['redirect_to'])) {
        header('Location: ' . $_POST['redirect_to']);
        exit;
    }
    
    http_response_code(500); // 500 = Internal Server Error
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'success' => false,
        'error' => 'erro ao processar curtida',
        'exception' => $msg,
        'exception_code' => $code,
        'mysql_error' => $conn->error ?? null
    ]);
    exit;
}

?>
