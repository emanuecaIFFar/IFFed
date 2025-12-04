<?php
// n vou criar objetivo aq pq é simples,
// basta destruir a sessão e redirecionar para login
session_start();

/*
 session_destroy() = destrói TODA a sessão
  
  Remove todas as variáveis:
    $_SESSION['id'] → sumiu!
  $_SESSION['nome_usuario'] → sumiu!
  
  Após isso, o usuário é considerado "deslogado"
  porque não tem mais dados de sessão.
  
 Qualquer página que verificar isset($_SESSION['id'])
 vai retornar FALSE → usuário não está logado
 */
session_destroy();

header("Location: ../pages/login.php"); //reedereciona 
exit;
?>