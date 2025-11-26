<?php
header('Content-Type: application/json; charset=utf-8');
session_start();
require_once __DIR__ . '/conexao.php';

$post_id = intval($_GET['post_id'] ?? 0);
if ($post_id <= 0) {
    echo json_encode(['success' => false, 'error' => 'post_id inválido']);
    exit;
}

$out = [];
$stmt = $conn->prepare('SELECT c.id, c.conteudo, c.data_criacao, u.nome AS autor_nome, u.foto AS autor_foto FROM comentarios c LEFT JOIN perfil u ON c.id_usuario = u.id WHERE c.id_postagem = ? ORDER BY c.data_criacao ASC LIMIT 300');
if (!$stmt) {
    echo json_encode(['success' => false, 'error' => 'erro no banco']);
    exit;
}
$stmt->bind_param('i', $post_id);
$stmt->execute();
$stmt->bind_result($cid, $cconteudo, $cdata, $cautor_nome, $cautor_foto);
while ($stmt->fetch()) {
    $foto = $cautor_foto ?? '';
    if (empty($foto)) {
        $fotoUrl = 'assets/img/padrao.jpg';
    } elseif (strpos($foto, 'uploads/') === 0) {
        $fotoUrl = 'assets/' . $foto;
    } elseif (strpos($foto, 'assets_front') !== false || strpos($foto, 'http') === 0) {
        $fotoUrl = $foto;
    } else {
        $fotoUrl = 'assets/uploads/' . $foto;
    }
    $out[] = [
        'id' => $cid,
        'conteudo' => $cconteudo,
        'data_criacao' => $cdata,
        'autor_nome' => $cautor_nome,
        'autor_foto' => $fotoUrl,
    ];
}
$stmt->close();

echo json_encode(['success' => true, 'data' => $out]);
