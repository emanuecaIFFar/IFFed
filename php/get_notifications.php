<?php
// Stub de compatibilidade: retorna lista vazia de notificações
header('Content-Type: application/json; charset=utf-8');
session_start();
// Não retornamos notificações para manter backend de notificações desativado
echo json_encode(['success' => true, 'data' => []]);
exit;
