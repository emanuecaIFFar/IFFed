<?php
// Wrapper para compatibilidade: encaminha para php/api.php?action=get_session_user
$_REQUEST['action'] = 'get_session_user';
require_once __DIR__ . '/api.php';
