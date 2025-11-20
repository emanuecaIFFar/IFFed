<?php
// php/upload_foto_teste.php
session_start();
include('conexao.php');

// Verifica se usuário está logado
if(!isset($_SESSION['id'])){
    header("Location: ../pages/login.php?erro=acesso_negado");
    exit;
}

$id_usuario = intval($_SESSION['id']);

// Verifica se veio arquivo
if(!isset($_FILES['foto_perfil']) || $_FILES['foto_perfil']['error'] !== UPLOAD_ERR_OK){
    header("Location: ../pages/perfil.php?upload=erro");
    exit;
}

$arquivo = $_FILES['foto_perfil'];

// Validações básicas
$limite_bytes = 2 * 1024 * 1024; // 2 MB
if($arquivo['size'] > $limite_bytes){
    header("Location: ../pages/perfil.php?upload=tamanho_excedido");
    exit;
}

$ext = strtolower(pathinfo($arquivo['name'], PATHINFO_EXTENSION));
$ext_permitidas = array('jpg','jpeg','png','gif');
if(!in_array($ext, $ext_permitidas)){
    header("Location: ../pages/perfil.php?upload=tipo_invalido");
    exit;
}

// Pasta destino (relativa ao arquivo php/upload_foto_teste.php)
$pasta_destino = __DIR__ . '/../assets_front/img/uploads/';
if(!is_dir($pasta_destino)){
    mkdir($pasta_destino, 0755, true);
}

// Cria nome seguro
$nome_original = basename($arquivo['name']);
$nome_limpo = preg_replace('/[^A-Za-z0-9_\-.]/', '_', $nome_original);
$novo_nome = uniqid() . '_' . $nome_limpo;
$caminho_completo = $pasta_destino . $novo_nome;

if(move_uploaded_file($arquivo['tmp_name'], $caminho_completo)){
    // Salvar caminho relativo no banco (uploads/nome)
    // IMPORTANTE: aqui movemos o arquivo para o diretório físico
    // `assets_front/img/uploads/` e salvamos no banco apenas o caminho
    // relativo `uploads/<nome>`. A imagem em si fica no disco, não no DB.
    $nome_arquivo_bd = 'uploads/' . $novo_nome;

    $nome_arquivo_bd_esc = $conn->real_escape_string($nome_arquivo_bd);
    $sql = "UPDATE perfil SET foto = '$nome_arquivo_bd_esc' WHERE id = $id_usuario";
    if($conn->query($sql) === TRUE){
        header("Location: ../pages/perfil.php?upload=ok");
        exit;
    } else {
        // Se falhar update, remover arquivo enviado
        @unlink($caminho_completo);
        header("Location: ../pages/perfil.php?upload=erro_bd");
        exit;
    }
} else {
    header("Location: ../pages/perfil.php?upload=erro_move");
    exit;
}

?>