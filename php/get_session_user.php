<?php
header('Content-Type: application/json; charset=utf-8');
session_start();
require_once __DIR__ . '/conexao.php';

if (!isset($_SESSION['id'])) {
    echo json_encode(['success' => false, 'error' => 'não autenticado']);
    exit;
}

$user_id = intval($_SESSION['id']);
$stmt = $conn->prepare('SELECT id, nome, foto FROM perfil WHERE id = ? LIMIT 1');
$stmt->bind_param('i', $user_id);
$stmt->execute();
$res = $stmt->get_result();
if ($row = $res->fetch_assoc()) {
    // ajustar caminho relativo para a imagem
    $foto = $row['foto'] ?? '';
    if (empty($foto)) {
        $row['foto_url'] = '../assets_front/img/padrao.jpg';
    } elseif (strpos($foto, 'uploads/') === 0) {
        // valor salvo como 'uploads/nome.jpg'
        $row['foto_url'] = '../assets_front/img/' . $foto;
    } elseif (strpos($foto, 'assets_front') !== false || strpos($foto, 'http') === 0) {
        $row['foto_url'] = $foto;
    } else {
        // valor salvo apenas com nome do arquivo
        $row['foto_url'] = '../assets_front/img/uploads/' . $foto;
    }
    echo json_encode(['success' => true, 'data' => $row]);
} else {
    echo json_encode(['success' => false, 'error' => 'usuário não encontrado']);
}
$stmt->close();
