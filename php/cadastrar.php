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
    
    // PASTA DE DESTINO (sistema de arquivos):
    // As imagens são salvas fisicamente em disco dentro da pasta
    // `assets_front/img/uploads/` (relativa à raiz do projeto).
    // No banco de dados será salvo apenas o caminho relativo (string),
    // por exemplo: "uploads/63a1b2c3_imagem.jpg".
    // Isso significa que o binário da imagem NÃO é guardado no MySQL,
    // apenas o caminho para encontrá-la no servidor.
    $pasta_destino = "../assets_front/img/uploads/";
    
    // Move o arquivo da pasta temporária do XAMPP para a nossa pasta
    if(move_uploaded_file($arquivo['tmp_name'], $pasta_destino . $novo_nome)) {
        // Se o move_uploaded_file for bem-sucedido, construímos o caminho relativo
        // que será salvo na coluna `foto` da tabela `perfil`.
        // Exemplo de valor salvo no DB: "uploads/649f3a2_image.jpg"
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