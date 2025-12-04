<?php
/*
objetivo validar login do user/caralogado
 1. Recebe email e senha via POST (do formulário)
 2. Busca no banco de dados
3. Se encontrar → cria sessão e vai pro perfil
  4. Se não encontrar → volta pro login com erro
 */


 //ini_set() = configura opções do PHP em tempo de execução
  
 // display_errors = mostra erros na tela (útil para debug)
 // error_reporting(E_ALL) = reporta TODOS os tipos de erro
  
 // ⚠️ Em produção (site no ar), desabilite isso!
 //Erros expostos podem revelar informações sensíveis
 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


/*
 * session_start() = inicia ou continua uma sessão
 * 
 * Sessão é como uma "memória" do servidor para cada usuário.
 * Permite lembrar que o usuário está logado enquanto navega.
 * Os dados ficam em $_SESSION (um array especial)
 */
session_start(); //Sessão é como uma "memória" do servidor para cada usuário. Permite lembrar que o usuário está logado enquanto navega. Os dados ficam em $_SESSION (um array especial)


include('conexao.php'); //inclui o arquivo de conexão com o banco de dados


if(isset($_POST['email']) && isset($_POST['senha'])) { // verifica se os dados foram enviados via POST

    
    $email = $conn->real_escape_string($_POST['email']); //real_escape_string = previne SQL Injection
    $senha = $conn->real_escape_string($_POST['senha']);//real_escape_string = previne SQL Injection, coisa mais avançada. Só fiz isso aq pq e login



    $sql_code = "SELECT * FROM perfil WHERE email = '$email' AND senha = '$senha'"; //consulta SQL para buscar o usuário com email e senha fornecidos
    
    
    $sql_query = $conn->query($sql_code) or die("Falha no SQL: " . $conn->error); //executa a consulta e trata erros

    
    $quantidade = $sql_query->num_rows; //num_rows = número de linhas retornadas pela consulta

    if($quantidade == 1) {
       //deu certo
        $usuario = $sql_query->fetch_assoc();//fetch_assoc() = obtém a linha como um array associativo

       
        if(!isset($_SESSION)) {
            session_start();
        }//verifica se a sessão já foi iniciada

        /*
         * $_SESSION = array especial que persiste entre páginas
         * 
         * Salvamos o ID e nome do usuário na sessão.
         * Assim, em QUALQUER outra página, podemos verificar:
         *   if (isset($_SESSION['id'])) → usuário está logado
         * 
         * A sessão fica salva no servidor (não no navegador!)
         * O navegador só guarda um "cookie" com o ID da sessão
         */
        $_SESSION['id'] = $usuario['id'];           // ID numérico do usuário
        $_SESSION['nome_usuario'] = $usuario['nome']; // Nome para exibir

        /*
         * header("Location: ...") = redireciona para outra página
         * 
         * É como se o servidor dissesse ao navegador:
         * "Ei, vai pra essa outra página agora!"
         * 
         * O navegador obedece e faz uma nova requisição
         */
        header("Location: ../pages/perfil.php");
        
        /*
         * exit = para a execução do script IMEDIATAMENTE
         * 
         * Importante após header() porque o PHP continua
         * executando o código abaixo se você não parar.
         * Isso pode causar bugs ou problemas de segurança.
         */
        exit;

    } else {
        //F pro login
        header("Location: ../pages/login.php?erro=login");
        exit;
    }

} else {
    //ACESSO sem o formulario (poost)
    header("Location: ../pages/login.php");
    exit;
}
?>