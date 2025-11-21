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

// Aceita POST: ids[]=1&ids[]=2  ou POST all=1 para marcar todas
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'método inválido']);
    exit;
}

if (isset($_POST['all']) && $_POST['all']) {
    $stmt = $conn->prepare('UPDATE notificacoes SET lida = 1 WHERE id_usuario_destino = ? AND lida = 0');
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $affected = $stmt->affected_rows;
    $stmt->close();
    echo json_encode(['success' => true, 'updated' => $affected]);
    exit;
}

$ids = $_POST['ids'] ?? null;
if (!is_array($ids) || count($ids) === 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'ids inválidos']);
    exit;
}

// Construir query segura para ids
$placeholders = implode(',', array_fill(0, count($ids), '?'));
$types = str_repeat('i', count($ids)+1); // +1 para user_id
$sql = "UPDATE notificacoes SET lida = 1 WHERE id_usuario_destino = ? AND id IN ($placeholders)";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'erro no banco']);
    exit;
}

$params = array_merge([$user_id], array_map('intval', $ids));
$bind_names[] = $types;
for ($i=0; $i<count($params); $i++) {
    $bind_names[] = &$params[$i];
}
call_user_func_array([$stmt, 'bind_param'], $bind_names);
$stmt->execute();
$affected = $stmt->affected_rows;
$stmt->close();

echo json_encode(['success' => true, 'updated' => $affected]);
