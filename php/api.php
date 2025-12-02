<?php
// API removida: este arquivo foi desativado conforme solicitação do mantenedor.
// Retorna 410 Gone para indicar que a API central foi removida.
header('Content-Type: application/json; charset=utf-8');
http_response_code(410);
echo json_encode(['success' => false, 'error' => 'API removida pelo administrador']);
exit;

