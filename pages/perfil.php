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

if(!isset($_SESSION['nome_usuario'])) {
    header("Location: login.php?erro=acesso_negado");
    exit;
}

// Buscar a foto e a bio do usuário logado
$id_usuario = $_SESSION['id'];
$sql = "SELECT * FROM perfil WHERE id = $id_usuario";
$resultado = $conn->query($sql);
$dados_usuario = $resultado->fetch_assoc();

// Agora a variável $dados_usuario tem tudo (foto, bio, data...)
?>


<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Meu Perfil</title>
    <link rel="stylesheet" href="../assets_front/css/style.css">
</head>
<body>

    <nav class="menu-lateral">
        <a href="../index.php">Início</a>
        <a href="../php/sair.php">Sair (Logout)</a>
    </nav>

    <div class="conteudo-principal">
        <h1>Olá, <?php echo $_SESSION['nome_usuario']; ?>!</h1>
        <p>Seja bem-vindo ao seu perfil exclusivo.</p>
        
        <div class="card-perfil">
            <!-- Foto e bio vindas do banco, para teste -->
            <?php if (!empty($dados_usuario['foto'])): ?>
                <img src="../assets_front/img/<?php echo $dados_usuario['foto']; ?>" alt="Foto de Perfil" width="150" style="border-radius: 50%;">
            <?php else: ?>
                <img src="../assets_front/img/karid.jpg" alt="Foto de Perfil" width="150" style="border-radius: 50%;">
            <?php endif; ?>

            <p>Bio: <?php echo !empty($dados_usuario['bio']) ? $dados_usuario['bio'] : 'Ainda não cadastrou uma bio.'; ?></p>
        </div>

        <!-- Formulário bem simples só para testar upload de foto -->
        <div class="card-perfil" style="margin-top: 20px;">
            <h3>Atualizar foto (teste)</h3>
            <form action="../php/upload_foto_teste.php" method="POST" enctype="multipart/form-data">
                <input type="file" name="foto_perfil" accept="image/*" required>
                <button type="submit">Enviar nova foto</button>
            </form>
            <p style="font-size: 0.8rem; color: #777;">Apenas para teste até o front ficar pronto.</p>
        </div>
    </div>

</body>
</html>