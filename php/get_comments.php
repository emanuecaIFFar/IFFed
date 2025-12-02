<?php
// Endpoint de comentários (GET) desativado: retornamos lista vazia
header('Content-Type: application/json; charset=utf-8');
session_start();
// Não expor comentários via GET — comportamento solicitado pelo mantenedor
echo json_encode(['success' => false, 'error' => 'comentários via GET desativados', 'data' => []]);
exit;
