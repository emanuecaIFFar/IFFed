<?php
session_start();
require_once __DIR__ . '/conexao.php';

// Suporta dois fluxos:
// - Requisições via fetch/ajax (esperam JSON) -> mantém comportamento anterior
// - Requisições via formulário POST com campo `redirect_to` -> redireciona de volta para a página

if (!isset($_SESSION['id'])) {
    // Se veio com redirect, envia de volta com erro simples via redirect
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

$conteudo = trim($_POST['conteudo'] ?? '');
$id_postagem = intval($_POST['id_postagem'] ?? 0);
$user_id = intval($_SESSION['id']);

if ($conteudo === '' || $id_postagem <= 0) {
    if (!empty($_POST['redirect_to'])) {
        header('Location: ' . $_POST['redirect_to']);
        exit;
    }
    http_response_code(400);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['success' => false, 'error' => 'dados inválidos']);
    exit;
}

// Inserir comentário
$stmt = $conn->prepare('INSERT INTO comentarios (conteudo, id_usuario, id_postagem, data_criacao) VALUES (?, ?, ?, NOW())');
if (!$stmt) {
    if (!empty($_POST['redirect_to'])) {
        header('Location: ' . $_POST['redirect_to']);
        exit;
    }
    http_response_code(500);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['success' => false, 'error' => 'erro no banco']);
    exit;
}
$stmt->bind_param('sii', $conteudo, $user_id, $id_postagem);
$ok = $stmt->execute();
if (!$ok) {
    if (!empty($_POST['redirect_to'])) {
        header('Location: ' . $_POST['redirect_to']);
        exit;
    }
    http_response_code(500);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['success' => false, 'error' => 'falha ao salvar comentário']);
    exit;
}

$inserted_id = $stmt->insert_id;
$stmt->close();

// Atualiza contador de comentários na tabela postagens (se existir coluna)
try {
    $u = $conn->prepare('UPDATE postagens SET num_comentarios = num_comentarios + 1 WHERE id = ?');
    if ($u) {
        $u->bind_param('i', $id_postagem);
        $u->execute();
        $u->close();
    }
} catch (Throwable $e) {
    // não fatal
}

// Se foi uma submissão por formulário, redireciona de volta para a página solicitada
if (!empty($_POST['redirect_to'])) {
    $loc = $_POST['redirect_to'];
    header('Location: ' . $loc);
    exit;
}

// Caso contrário, retorna JSON para compatibilidade com chamadas AJAX existentes
header('Content-Type: application/json; charset=utf-8');
echo json_encode(['success' => true, 'data' => ['id' => $inserted_id, 'conteudo' => $conteudo, 'id_postagem' => $id_postagem, 'id_usuario' => $user_id]]);
