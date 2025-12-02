<?php
$alert = null;

if (isset($_GET['erro']) && $_GET['erro'] === 'login') {
    $alert = [
        'type' => 'error',
        'message' => 'Usuário ou senha incorretos. Caso ainda não tenha conta, faça seu cadastro para continuar.'
    ];
} elseif (isset($_GET['sucesso']) && $_GET['sucesso'] === 'cadastrado') {
    $alert = [
        'type' => 'success',
        'message' => 'Cadastro confirmado! Entre com seus dados para acessar a rede.'
    ];
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - IFFed</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <div class="background-blob background-blob--left"></div>
    <div class="background-blob background-blob--right"></div>

    <main class="auth-shell">
        <section class="auth-card">
            <header class="auth-card__header">
                <h1 class="auth-title">Entrar na rede</h1>
                <p class="auth-subtitle">Informe seus dados para validar o acesso</p>
            </header>

            <?php if ($alert): ?>
                <div class="alert-banner <?php echo $alert['type'] === 'error' ? 'alert-error' : 'alert-success'; ?>">
                    <?php echo htmlspecialchars($alert['message'], ENT_QUOTES, 'UTF-8'); ?>
                </div>
            <?php endif; ?>

            <form action="../php/validar_login.php" method="POST" class="form-stack" autocomplete="off">
                <div class="form-field">
                    <label for="login-email" class="form-label">E-mail cadastrado</label>
                    <input
                        id="login-email"
                        type="email"
                        name="email"
                        required
                        class="form-input"
                        placeholder="usuario@if.com"
                        autofocus
                    />
                </div>

                <div class="form-field">
                    <label for="login-senha" class="form-label">Senha</label>
                    <input
                        id="login-senha"
                        type="password"
                        name="senha"
                        required
                        class="form-input"
                        placeholder="••••••••"
                    />
                </div>

                <button type="submit" class="button-primary">Entrar</button>
            </form>

            <footer class="auth-footer">
                Não possui acesso?
                <a href="cadastro.php" class="link-inline">Cadastre-se agora</a>
            </footer>
        </section>
    </main>
</body>
</html>