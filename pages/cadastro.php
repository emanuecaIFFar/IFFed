<?php
// pages_front/cadastro.php

// Se quiser usar sessão (por exemplo, para mensagem de sucesso/erro), descomente:
// session_start();

// Aqui em cima você pode, no futuro, tratar mensagens vindas do cadastrar.php,
// por exemplo: ?status=ok ou ?status=erro e mostrar um aviso na tela.
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastro - IFFed</title>
    <link rel="stylesheet" href="../assets_front/css/style.css">
</head>
<body>
    <div class="container-cadastro">
        <h2>Criar Nova Conta</h2>

        <?php
        // Exemplo: mostrar mensagem de sucesso/erro baseada na URL
        if (isset($_GET['status']) && $_GET['status'] == 'ok') {
            echo "<p style='color: green; text-align: center; font-weight: bold;'>Cadastro realizado com sucesso! Faça login.</p>";
        } elseif (isset($_GET['status']) && $_GET['status'] == 'erro') {
            echo "<p style='color: red; text-align: center; font-weight: bold;'>Erro ao cadastrar. Tente novamente.</p>";
        }
        ?>

        <form action="../php/cadastrar.php" method="POST" enctype="multipart/form-data">
            
            <label>Nome Completo:</label>
            <input type="text" name="nome" required>

            <label>E-mail:</label>
            <input type="email" name="email" required>

            <label>Data de Nascimento:</label>
            <input type="date" name="data_nascimento" required>

            <label>Senha:</label>
            <input type="password" name="senha" required>

            <label>Biografia:</label>
            <textarea name="bio" placeholder="Fale sobre você..."></textarea>

            <label>Foto de Perfil:</label>
            <input type="file" name="foto_perfil" accept="image/*">
            
            <br><br>
            <button type="submit">Cadastrar</button>
        </form>
        
        <a href="login.php">Já tenho conta (Voltar)</a>
    </div>
</body>
</html>