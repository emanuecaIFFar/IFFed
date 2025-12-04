<?php

$servidor = "localhost";
$usuario = "root"; 
$senha = "usbw"; 
$banco = "iffed";


$conn = new mysqli($servidor, $usuario, $senha, $banco); //retorna um OBJETO de conexão que guardamos em $conn

if ($conn->connect_error) { //contem conexao, propriedade acho
    $conn = new mysqli($servidor, "root", "", $banco); //para xamp que n precisa de senha
    
    if ($conn->connect_error) {
        die("Falha na conexão: " . $conn->connect_error); //se chegou aqui, não conseguimos conectar de jeito nenhum.
    }
}

//se passar, tudo ok.

/* 
 * $conn->query($sql)       → Executa uma query SQL
 * $conn->prepare($sql)     → Prepara uma query (mais seguro)
 * $conn->real_escape_string($str) → Escapa caracteres perigosos
 * $conn->error             → Último erro do MySQL
 * $conn->insert_id         → ID do último INSERT
 * $conn->close()           → Fecha a conexão
 */
?>