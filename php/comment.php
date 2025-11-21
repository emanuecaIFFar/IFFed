<?php
header('Content-Type: application/json; charset=utf-8');
session_start();
require_once __DIR__ . '/conexao.php';

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

$conteudo = trim($_POST['conteudo'] ?? '');
$id_postagem = intval($_POST['id_postagem'] ?? 0);
$user_id = intval($_SESSION['id']);

if ($conteudo === '' || $id_postagem <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'dados inválidos']);
    exit;
}

// Inserir comentário
$stmt = $conn->prepare('INSERT INTO comentarios (conteudo, id_usuario, id_postagem) VALUES (?, ?, ?)');
if (!$stmt) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'erro no banco']);
    exit;
}
$stmt->bind_param('sii', $conteudo, $user_id, $id_postagem);
$ok = $stmt->execute();
if (!$ok) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'falha ao salvar comentário']);
    exit;
}

$inserted_id = $stmt->insert_id;
$stmt->close();

// Retornar comentário recém-criado (simples)
echo json_encode(['success' => true, 'data' => ['id' => $inserted_id, 'conteudo' => $conteudo, 'id_postagem' => $id_postagem, 'id_usuario' => $user_id]]);
