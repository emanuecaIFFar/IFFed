<?php
// Wrapper para compatibilidade: encaminha para php/api.php?action=mark_notifications_read
$_REQUEST['action'] = 'mark_notifications_read';
require_once __DIR__ . '/api.php';
