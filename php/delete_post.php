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
    // Deleta registro
    $del = $conn->prepare('DELETE FROM postagens WHERE id = ? AND id_usuario = ?');
    $del->bind_param('ii', $post_id, $user_id);
    $ok = $del->execute();
    $del->close();

    if (!$ok) throw new Exception('Falha ao deletar post');

    // Remove arquivo de imagem associado, se houver
    if (!empty($row['imagem'])) {
        $f = __DIR__ . '/../assets_front/img/uploads/' . $row['imagem'];
        if (file_exists($f)) @unlink($f);
    }

    $conn->commit();
    $_SESSION['flash_success'] = 'Publicação apagada.';
} catch (Exception $e) {
    $conn->rollback();
    $_SESSION['flash_error'] = 'Erro ao apagar publicação.';
}

header('Location: ../pages/perfil.php');
exit;
