<?php
// Endpoint para criar postagens (form submission)
session_start();
require_once __DIR__ . '/conexao.php';

if (!isset($_SESSION['id'])) {
    header('Location: ../pages/login.php?erro=nao_autenticado');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../pages/criar_post.html');
    exit;
}

$user_id = intval($_SESSION['id']);
$conteudo = trim($_POST['conteudo'] ?? '');

// Processar upload de imagem (opcional)
$imagem_nome = NULL;
if (!empty($_FILES['imagem']['name'])) {
    $file = $_FILES['imagem'];
    if ($file['error'] === UPLOAD_ERR_OK) {
        $tmp = $file['tmp_name'];
        $info = getimagesize($tmp);
        $maxBytes = 5 * 1024 * 1024; // 5MB
        if ($info === false) {
            $_SESSION['flash_error'] = 'Arquivo não é uma imagem válida.';
            header('Location: ../pages/criar_post.html'); exit;
        }
        if ($file['size'] > $maxBytes) {
            $_SESSION['flash_error'] = 'Imagem muito grande (max 5MB).';
            header('Location: ../pages/criar_post.html'); exit;
        }
        $allowed = ['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp'];
        $mime = $info['mime'];
        if (!isset($allowed[$mime])) {
            $_SESSION['flash_error'] = 'Tipo de imagem não permitido.';
            header('Location: ../pages/criar_post.html'); exit;
        }
        $ext = $allowed[$mime];
        $uploadsDir = __DIR__ . '/../assets/uploads/';
        if (!is_dir($uploadsDir)) mkdir($uploadsDir, 0755, true);
        $imagem_nome = time() . '_' . $user_id . '.' . $ext;
        $dest = $uploadsDir . $imagem_nome;
        if (!move_uploaded_file($tmp, $dest)) {
            $_SESSION['flash_error'] = 'Falha ao mover imagem.';
            header('Location: ../pages/criar_post.html'); exit;
        }
    }
}

// Inserir no banco
$sql = 'INSERT INTO postagens (conteudo_textual, id_usuario, imagem, data_criacao, num_comentarios, num_curtidas) VALUES (?, ?, ?, NOW(), 0, 0)';
$stmt = $conn->prepare($sql);
if (!$stmt) {
    $_SESSION['flash_error'] = 'Erro no banco.';
    header('Location: ../pages/criar_post.html'); exit;
}
$imgParam = $imagem_nome;
$stmt->bind_param('sis', $conteudo, $user_id, $imgParam);
$ok = $stmt->execute();
if (!$ok) {
    $_SESSION['flash_error'] = 'Falha ao salvar postagem.';
    header('Location: ../pages/criar_post.html'); exit;
}
$stmt->close();

$_SESSION['flash_success'] = 'Post criado com sucesso.';
header('Location: ../pages/perfil.php');
exit;
