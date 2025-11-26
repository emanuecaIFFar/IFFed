<?php
// Wrapper para compatibilidade: encaminha para php/api.php?action=get_notifications
$_REQUEST['action'] = 'get_notifications';
require_once __DIR__ . '/api.php';
