<?php
// php/api.php
// Endpoints unificados para AJAX/JSON: notificações e sessão do usuário
// Este arquivo centraliza ações pequenas que retornam JSON para o front (AJAX)
// Ação esperada via `?action=nome_da_acao` ou `POST action=...`.
header('Content-Type: application/json; charset=utf-8');
session_start();
require_once __DIR__ . '/conexao.php';

$action = $_REQUEST['action'] ?? '';
try {
    switch ($action) {
        // -- GET NOTIFICATIONS --
        // Retorna últimas notificações do usuário logado (limit 50).
        case 'get_notifications':
            if (!isset($_SESSION['id'])) {
                http_response_code(401);
                echo json_encode(['success' => false, 'error' => 'não autenticado']);
                exit;
            }
            $user_id = intval($_SESSION['id']);
            $limit = 50;
            $stmt = $conn->prepare(
                'SELECT n.id, n.tipo, n.id_postagem, n.lida, n.data_criacao, p.nome AS origem_nome, p.foto AS origem_foto
                 FROM notificacoes n
                 LEFT JOIN perfil p ON p.id = n.id_usuario_origem
                 WHERE n.id_usuario_destino = ?
                 ORDER BY n.data_criacao DESC
                 LIMIT ?'
            );
            if (!$stmt) throw new Exception('erro no banco');
            $stmt->bind_param('ii', $user_id, $limit);
            $stmt->execute();
            $stmt->bind_result($nid, $ntipo, $nid_postagem, $nlida, $ndata, $origem_nome, $origem_foto);
            $rows = [];
            while ($stmt->fetch()) {
                $rows[] = [
                    'id' => $nid,
                    'tipo' => $ntipo,
                    'id_postagem' => $nid_postagem,
                    'lida' => $nlida,
                    'data_criacao' => $ndata,
                    'origem_nome' => $origem_nome,
                    'origem_foto' => $origem_foto,
                ];
            }
            $stmt->close();
            echo json_encode(['success' => true, 'data' => $rows]);
            exit;

        // -- MARK NOTIFICATIONS READ --
        // Marca notificações como lidas. Aceita POST all=1 ou ids[] = [1,2,3]
        case 'mark_notifications_read':
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
            $user_id = intval($_SESSION['id']);
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
            $ids = array_map('intval', $ids);
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            $types = str_repeat('i', count($ids)+1);
            $sql = "UPDATE notificacoes SET lida = 1 WHERE id_usuario_destino = ? AND id IN ($placeholders)";
            $stmt = $conn->prepare($sql);
            if (!$stmt) throw new Exception('erro no banco');
            $params = array_merge([$user_id], $ids);
            $bind_names[] = $types;
            for ($i=0; $i<count($params); $i++) {
                $bind_names[] = &$params[$i];
            }
            call_user_func_array([$stmt, 'bind_param'], $bind_names);
            $stmt->execute();
            $affected = $stmt->affected_rows;
            $stmt->close();
            echo json_encode(['success' => true, 'updated' => $affected]);
            exit;

        // -- GET SESSION USER --
        // Retorna informações básicas do usuário logado para preencher formulários
        case 'get_session_user':
            if (!isset($_SESSION['id'])) {
                echo json_encode(['success' => false, 'error' => 'não autenticado']);
                exit;
            }
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

        default:
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'ação inválida']);
            exit;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    exit;
}

?>
