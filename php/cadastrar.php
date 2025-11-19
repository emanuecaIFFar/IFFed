<?php
include('conexao.php');

// Recebendo dados de texto (Superglobal $_POST)
$nome = $conn->real_escape_string($_POST['nome']);
$email = $conn->real_escape_string($_POST['email']);
$senha = $conn->real_escape_string($_POST['senha']); // Aqui idealmente usaríamos hash
$data = $_POST['data_nascimento'];
$bio = $conn->real_escape_string($_POST['bio']);

// Lógica da FOTO (Superglobal $_FILES)
$nome_arquivo = "padrao.jpg"; // Começa com a foto padrão caso a pessoa não envie nada

// Verifica se enviou arquivo e se não deu erro no envio
if(isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] == 0) {
    
    $arquivo = $_FILES['foto_perfil'];
    
    // Criar um nome único para a foto não substituir a de outro usuário
    // Exemplo: se o arquivo é "foto.jpg", vira "65a4s6d5_foto.jpg"
    $novo_nome = uniqid() . "_" . $arquivo['name'];
    
    // Define onde vamos salvar
    $pasta_destino = "../assets_front/img/uploads/";
    
    // Move o arquivo da pasta temporária do XAMPP para a nossa pasta
    if(move_uploaded_file($arquivo['tmp_name'], $pasta_destino . $novo_nome)) {
        $nome_arquivo = "uploads/" . $novo_nome; // Salva o caminho relativo
    }
}

// Inserindo no Banco de Dados
$sql = "INSERT INTO perfil (nome, email, senha, data_nasc, bio, foto) 
        VALUES ('$nome', '$email', '$senha', '$data', '$bio', '$nome_arquivo')";

if($conn->query($sql) === TRUE) {
    // Sucesso! Manda pro login
    header("Location: ../pages/login.php?sucesso=cadastrado");
} else {
    // Erro! Mostra o que aconteceu
    echo "Erro ao cadastrar: " . $conn->error;
}
?>