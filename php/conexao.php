<?php
// php/conexao.php

// Configurações do USBWebServer
$servidor = "localhost";
$usuario = "root"; 
$senha = "usbw"; // IMPORTANTE: O USBWebServer geralmente usa a senha "usbw"
$banco = "iffed";

// Tenta conectar
$conn = new mysqli($servidor, $usuario, $senha, $banco);

// Verifica se deu erro
if ($conn->connect_error) {
    // Se der erro de senha, tenta sem senha (algumas versões antigas)
    $conn = new mysqli($servidor, "root", "", $banco);
    
    if ($conn->connect_error) {
        die("Falha na conexão: " . $conn->connect_error);
    }
}
?>