<?php
// Stub: retorna dados mínimos do usuário logado, sem depender da API central
header('Content-Type: application/json; charset=utf-8');
session_start();
if (!isset($_SESSION['id'])) {
	echo json_encode(['success' => false, 'error' => 'não autenticado']);
	exit;
}
require_once __DIR__ . '/conexao.php';
$user_id = intval($_SESSION['id']);
$stmt = $conn->prepare('SELECT id, nome, foto FROM perfil WHERE id = ? LIMIT 1');
$stmt->bind_param('i', $user_id);
$stmt->execute();
$stmt->bind_result($rid, $rnome, $rfoto);
if ($stmt->fetch()) {
	$foto = $rfoto ?? '';
	if (empty($foto)) {
		$foto_url = '../assets/img/padrao.jpg';
	} elseif (strpos($foto, 'uploads/') === 0) {
		$foto_url = '../assets/' . $foto;
	} elseif (strpos($foto, 'assets_front') !== false || strpos($foto, 'http') === 0) {
		$foto_url = $foto;
	} else {
		$foto_url = '../assets/uploads/' . $foto;
	}
	echo json_encode(['success' => true, 'data' => [
		'id' => $rid,
		'nome' => $rnome,
		'foto' => $rfoto,
		'foto_url' => $foto_url,
	]]);
} else {
	echo json_encode(['success' => false, 'error' => 'usuário não encontrado']);
}
$stmt->close();
exit;
