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
        // Mensagens através da URL: erro de login ou sucesso no cadastro
        if(isset($_GET['erro']) && $_GET['erro'] == 'login'){
            echo "<p style='color: red; text-align: center; font-weight: bold;'>Usuário ou senha incorretos!</p>";
        }
        if(isset($_GET['sucesso']) && $_GET['sucesso'] == 'cadastrado'){
            echo "<p style='color: green; text-align: center; font-weight: bold;'>Cadastro realizado com sucesso! Faça login.</p>";
        }
        ?>

                <!-- Formulário real que vai para o PHP -->
                <form action="../php/validar_login.php" method="POST" class="space-y-6">
                    <div class="space-y-2">
                        <label class="text-sm font-medium text-zinc-300 block">Usuário (Seu Nome):</label>
                        <input
                            type="text"
                            name="email"
                            placeholder="emanuel@email.com"
                            required
                            class="block w-full px-3 py-3 border border-zinc-800 rounded-lg bg-zinc-900/50 text-zinc-100 placeholder-zinc-600 focus:outline-none focus:ring-2 focus:ring-white/10 focus:border-zinc-600 transition-all sm:text-sm"
                        />
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-medium text-zinc-300 block">Senha:</label>
                        <input
                            type="password"
                            name="senha"
                            placeholder="123"
                            required
                            class="block w-full px-3 py-3 border border-zinc-800 rounded-lg bg-zinc-900/50 text-zinc-100 placeholder-zinc-600 focus:outline-none focus:ring-2 focus:ring-white/10 focus:border-zinc-600 transition-all sm:text-sm"
                        />
                    </div>

                    <button
                        type="submit"
                        class="w-full flex justify-center py-3 px-4 border border-transparent text-sm font-bold rounded-lg text-black bg-white hover:bg-zinc-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-zinc-900 focus:ring-white transition-all duration-200 mt-6"
                    >
                        Entrar
                    </button>
                </form>

        <p style="text-align:center; margin-top:12px;">Não tem conta? <a href="cadastro.php">Cadastre-se aqui</a>.</p>
        
    </div>
</body>
</html>
