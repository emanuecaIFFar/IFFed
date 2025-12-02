<?php
// Stub de compatibilidade: marca nada (notificações desativadas)
header('Content-Type: application/json; charset=utf-8');
session_start();
// Resposta de sucesso mas sem alteração de banco
echo json_encode(['success' => true, 'updated' => 0]);
exit;
