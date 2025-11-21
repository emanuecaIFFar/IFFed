<?php
header('Content-Type: application/json; charset=utf-8');
session_start();
require_once __DIR__ . '/conexao.php';

if (!isset($_SESSION['id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'não autenticado']);
    exit;
}

$user_id = intval($_SESSION['id']);

// Buscar últimas notificações (limit 50)
$limit = 50;
$stmt = $conn->prepare(
    'SELECT n.id, n.tipo, n.id_postagem, n.lida, n.data_criacao, p.nome AS origem_nome, p.foto AS origem_foto
     FROM notificacoes n
     LEFT JOIN perfil p ON p.id = n.id_usuario_origem
     WHERE n.id_usuario_destino = ?
     ORDER BY n.data_criacao DESC
     LIMIT ?'
);
if (!$stmt) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'erro no banco']);
    exit;
}
$stmt->bind_param('ii', $user_id, $limit);
$stmt->execute();
$res = $stmt->get_result();
$rows = [];
while ($r = $res->fetch_assoc()) {
    $rows[] = $r;
}
$stmt->close();

echo json_encode(['success' => true, 'data' => $rows]);
