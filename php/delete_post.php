<?php
session_start();
require_once __DIR__ . '/conexao.php';

if (!isset($_SESSION['id'])) {
    header('Location: ../pages/login.php?erro=nao_autenticado');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../pages/perfil.php');
    exit;
}

$post_id = intval($_POST['post_id'] ?? 0);
$user_id = intval($_SESSION['id']);

if ($post_id <= 0) {
    header('Location: ../pages/perfil.php');
    exit;
}

// Verifica se o post pertence ao usuário
$stmt = $conn->prepare('SELECT imagem, id_usuario FROM postagens WHERE id = ? LIMIT 1');
$stmt->bind_param('i', $post_id);
$stmt->execute();
$res = $stmt->get_result();
if (!$row = $res->fetch_assoc()) {
    $stmt->close();
    header('Location: ../pages/perfil.php');
    exit;
}
$stmt->close();

if (intval($row['id_usuario']) !== $user_id) {
    // Não é dono
    header('Location: ../pages/perfil.php');
    exit;
}

// Começa transação para garantir integridade
$conn->begin_transaction();
try {
    // Primeiro, deleta registros dependentes (Foreign Keys)
    // Deleta curtidas do post
    try {
        $delCurtidas = $conn->prepare('DELETE FROM curtidas WHERE id_postagem = ?');
        if ($delCurtidas) {
            $delCurtidas->bind_param('i', $post_id);
            $delCurtidas->execute();
            $delCurtidas->close();
        }
    } catch (Throwable $e) {
        // Ignora se a tabela não existir
    }

    // Deleta comentários do post
    try {
        $delComentarios = $conn->prepare('DELETE FROM comentarios WHERE id_postagem = ?');
        if ($delComentarios) {
            $delComentarios->bind_param('i', $post_id);
            $delComentarios->execute();
            $delComentarios->close();
        }
    } catch (Throwable $e) {
        // Ignora se a tabela não existir
    }

    // Deleta notificações relacionadas ao post (se existir a tabela)
    try {
        $delNotif = $conn->prepare('DELETE FROM notificacoes WHERE id_postagem = ?');
        if ($delNotif) {
            $delNotif->bind_param('i', $post_id);
            $delNotif->execute();
            $delNotif->close();
        }
    } catch (Throwable $e) {
        // Ignora se a tabela não existir
    }

    // Agora deleta o post
    $del = $conn->prepare('DELETE FROM postagens WHERE id = ? AND id_usuario = ?');
    $del->bind_param('ii', $post_id, $user_id);
    $ok = $del->execute();
    $del->close();

    if (!$ok) throw new Exception('Falha ao deletar post');

    // Remove arquivo de imagem associado, se houver
    if (!empty($row['imagem'])) {
        $f = __DIR__ . '/../assets/uploads/' . $row['imagem'];
        if (file_exists($f)) @unlink($f);
    }

    $conn->commit();
    $_SESSION['flash_success'] = 'Publicação apagada.';
} catch (Exception $e) {
    $conn->rollback();
    $_SESSION['flash_error'] = 'Erro ao apagar publicação: ' . $e->getMessage();
    // Log para debug
    file_put_contents(__DIR__ . '/../debug_delete_error.txt', date('Y-m-d H:i:s') . " - Erro: " . $e->getMessage() . "\n", FILE_APPEND);
}

header('Location: ../pages/perfil.php');
exit;
