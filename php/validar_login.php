<?php
// 1. Habilita mostragem de erros (para debug)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 2. Inicia sessão e conexão
session_start();
include('conexao.php');

// 3. Verifica se os dados chegaram
if(isset($_POST['email']) && isset($_POST['senha'])) {

    $email = $conn->real_escape_string($_POST['email']);
    $senha = $conn->real_escape_string($_POST['senha']);

    // 4. Busca no banco
    $sql_code = "SELECT * FROM perfil WHERE email = '$email' AND senha = '$senha'";
    $sql_query = $conn->query($sql_code) or die("Falha no SQL: " . $conn->error);

    // 5. Verifica quantidade encontrada
    $quantidade = $sql_query->num_rows;

    if($quantidade == 1) {
        // SUCESSO
        $usuario = $sql_query->fetch_assoc();

        if(!isset($_SESSION)) {
            session_start();
        }

        $_SESSION['id'] = $usuario['id'];
        $_SESSION['nome_usuario'] = $usuario['nome'];

        // Redireciona para o perfil
        header("Location: ../pages/perfil.php");
        exit; // Importante parar o script aqui

    } else {
        // FALHA (SENHA ERRADA)
        // Redireciona para login com erro
        header("Location: ../pages/login.php?erro=login");
        exit;
    }

} else {
    // Se tentar entrar na página direto sem postar nada
    header("Location: ../pages/login.php");
    exit;
}
?>