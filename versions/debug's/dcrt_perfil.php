<?php
// ITEM 5 DA LOUSA: Segurança
session_start();

// Se NÃO existir a variável 'nome_usuario' na sessão...
if(!isset($_SESSION['nome_usuario'])) {
    // ...expulsa o usuário para o login
    header("Location: login.php?erro=acesso_negado");
    exit; // Mata o código aqui
}

include('../php/conexao.php'); // Importante conectar

// Buscar a foto e a bio do usuário logado
$id_usuario = $_SESSION['id'];
$sql = "SELECT * FROM perfil WHERE id = $id_usuario";
$resultado = $conn->query($sql);
$dados_usuario = $resultado->fetch_assoc();

// Agora a variável $dados_usuario tem tudo (foto, bio, data...)
?>