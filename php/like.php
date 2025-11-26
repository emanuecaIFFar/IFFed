<?php
// php/like.php
header('Content-Type: application/json; charset=utf-8');
session_start();
require_once __DIR__ . '/conexao.php';

// Habilita exceptions do MySQLi para facilitar debug local
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);



if (!isset($_SESSION['id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'não autenticado']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'método inválido']);
    exit;
}

$post_id = intval($_POST['post_id'] ?? 0);
$user_id = intval($_SESSION['id']);

if ($post_id <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'post inválido']);
    exit;
}

// Verifica se já curtiu
$stmt = $conn->prepare('SELECT id FROM curtidas WHERE id_postagem = ? AND id_usuario = ? LIMIT 1');
$stmt->bind_param('ii', $post_id, $user_id);
$stmt->execute();
$stmt->bind_result($like_row_id);
$exists = $stmt->fetch();
$stmt->close();

// Descobre o dono da postagem (para gerar notificação quando for curtida)
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
    $conn->begin_transaction();
    if ($exists) {
        // Remove curtida
        $del = $conn->prepare('DELETE FROM curtidas WHERE id_postagem = ? AND id_usuario = ?');
        $del->bind_param('ii', $post_id, $user_id);
        $del->execute();
        $del->close();

        // Atualiza contador (se a coluna existir)
        $upd = $conn->prepare('UPDATE postagens SET num_curtidas = GREATEST(0, num_curtidas - 1) WHERE id = ?');
        $upd->bind_param('i', $post_id);
        $upd->execute();
        $upd->close();

        // Remove notificação associada a essa curtida (se não for curtida própria)
        if ($post_owner_id && $post_owner_id !== $user_id) {
            try {
                $tipo = 'like';
                $delNotif = $conn->prepare('DELETE FROM notificacoes WHERE tipo = ? AND id_postagem = ? AND id_usuario_destino = ? AND id_usuario_origem = ?');
                if (!$delNotif) {
                    error_log('[like.php] Falha prepare DELETE notificacoes: ' . $conn->error);
                } else {
                    $delNotif->bind_param('siii', $tipo, $post_id, $post_owner_id, $user_id);
                    $delNotif->execute();
                    $delNotif->close();
                }
            } catch (Throwable $e) {
                error_log('[like.php] Erro ao remover notificação: ' . $e->getMessage());
            }
        }

        $action = 'unliked';
    } else {
        // Insere curtida
        $ins = $conn->prepare('INSERT INTO curtidas (id_postagem, id_usuario, created_at) VALUES (?, ?, NOW())');
        $ins->bind_param('ii', $post_id, $user_id);
        $ins->execute();
        $ins->close();

        // Atualiza contador
        $upd = $conn->prepare('UPDATE postagens SET num_curtidas = num_curtidas + 1 WHERE id = ?');
        $upd->bind_param('i', $post_id);
        $upd->execute();
        $upd->close();

        // Cria notificação para o dono do post (desde que não seja o próprio)
        if ($post_owner_id && $post_owner_id !== $user_id) {
            try {
                $tipo = 'like';
                $notif = $conn->prepare('INSERT INTO notificacoes (id_usuario_destino, id_usuario_origem, tipo, id_postagem, lida, data_criacao) VALUES (?, ?, ?, ?, 0, NOW())');
                if (!$notif) {
                    error_log('[like.php] Falha prepare INSERT notificacoes: ' . $conn->error);
                } else {
                    // bind_param espera referências; tipos: int, int, string, int
                    $notif->bind_param('iisi', $post_owner_id, $user_id, $tipo, $post_id);
                    $notif->execute();
                    $notif->close();
                }
            } catch (Throwable $e) {
                // Não tornar a falha de notificação fatal para a operação de curtida
                error_log('[like.php] Erro ao inserir notificação: ' . $e->getMessage());
            }
        }

        $action = 'liked';
    }

    // Obter novo total de curtidas
    $q = $conn->prepare('SELECT num_curtidas FROM postagens WHERE id = ? LIMIT 1');
    $q->bind_param('i', $post_id);
    $q->execute();
    $q->bind_result($num_curtidas_total);
    $q->fetch();
    $total = intval($num_curtidas_total ?? 0);
    $q->close();

    $conn->commit();
    echo json_encode(['success' => true, 'action' => $action, 'total' => $total]);
    exit;
} catch (Throwable $e) {
    // Registra detalhes do erro para depuração local
    $conn->rollback();
    $msg = $e->getMessage();
    $code = $e->getCode();
    error_log('[like.php] Throwable: code=' . $code . ' msg=' . $msg . "\n" . $e->getTraceAsString());
    http_response_code(500);
    // Retorna mensagem de erro (útil apenas em ambiente de desenvolvimento local)
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
