<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Login - IFFed</title>
    <link rel="stylesheet" href="../assets_front/css/style.css">
</head>
<body>
    <div class="container-login">
        <h2>Entrar na Rede</h2>

        <?php
        // Se na URL vier "login.php?erro=login", mostra isso:
        if(isset($_GET['erro']) && $_GET['erro'] == 'login'){
            echo "<p style='color: red; text-align: center; font-weight: bold;'>Usuário ou senha incorretos!</p>";
        }
        ?>

        <form action="../php/validar_login.php" method="POST">
            
            <label>Usuário (Seu Nome):</label>
            <input type="text" name="email" placeholder="emanuel@email.com" required>

            <label>Senha:</label>
            <input type="password" name="senha" placeholder="123" required>

            <button type="submit">Entrar</button>
        </form>

        <p style="text-align:center; margin-top:12px;">Não tem conta? <a href="cadastro.php">Cadastre-se aqui</a>.</p>
        
    </div>
</body>
</html>