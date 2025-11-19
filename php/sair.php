<?php
session_start();
// Destrói todas as variáveis da sessão (apaga o nome_usuario)
session_destroy();
// Manda de volta pro login
header("Location: ../pages/login.php");
?>