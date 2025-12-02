<?php
// php/like.php
session_start();
require_once __DIR__ . '/conexao.php';

// Habilita exceptions do MySQLi para facilitar debug local
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Se não autenticado, redireciona quando solicitado ou responde JSON
if (!isset($_SESSION['id'])) {
    if (!empty($_POST['redirect_to'])) {
        header('Location: ' . $_POST['redirect_to']);
        exit;
    }
    http_response_code(401);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['success' => false, 'error' => 'não autenticado']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['success' => false, 'error' => 'método inválido']);
    exit;
}

$post_id = intval($_POST['post_id'] ?? 0);
$user_id = intval($_SESSION['id']);

if ($post_id <= 0) {
    if (!empty($_POST['redirect_to'])) {
        header('Location: ' . $_POST['redirect_to']);
        exit;
    }
    http_response_code(400);
    header('Content-Type: application/json; charset=utf-8');
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
                if ($delNotif) {
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
        // Removido data_criacao para compatibilidade com tabelas sem essa coluna
        $ins = $conn->prepare('INSERT INTO curtidas (id_postagem, id_usuario) VALUES (?, ?)');
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
                if ($notif) {
                    $notif->bind_param('iisi', $post_owner_id, $user_id, $tipo, $post_id);
                    $notif->execute();
                    $notif->close();
                }
            } catch (Throwable $e) {
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

    // Se foi submetido via formulário, redireciona de volta
    if (!empty($_POST['redirect_to'])) {
        $redirect = $_POST['redirect_to'];
        // Adiciona um timestamp para evitar cache do navegador
        if (strpos($redirect, '?') === false) {
            // Se não tem query string, adiciona ?t=... antes da âncora
            if (strpos($redirect, '#') !== false) {
                $parts = explode('#', $redirect, 2);
                $redirect = $parts[0] . '?t=' . time() . '#' . $parts[1];
            } else {
                $redirect .= '?t=' . time();
            }
        } else {
            // Se já tem query string, adiciona &t=... antes da âncora
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
    $conn->rollback();
    $msg = $e->getMessage();
    $code = $e->getCode();
    
    // Log de erro em arquivo visível na raiz para debug
    file_put_contents(__DIR__ . '/../debug_like_error.txt', date('Y-m-d H:i:s') . " - Erro: " . $msg . "\n", FILE_APPEND);
    
    error_log('[like.php] Throwable: code=' . $code . ' msg=' . $msg . "\n" . $e->getTraceAsString());
    if (!empty($_POST['redirect_to'])) {
        header('Location: ' . $_POST['redirect_to']);
        exit;
    }
    http_response_code(500);
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
