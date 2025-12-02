<?php
$alert = null;
if (isset($_GET['status']) && $_GET['status'] === 'ok') {
    $alert = [
        'type' => 'success',
        'message' => 'Cadastro realizado com sucesso! Faça login para começar a usar o IFFed.'
    ];
} elseif (isset($_GET['status']) && $_GET['status'] === 'erro') {
    $alert = [
        'type' => 'error',
        'message' => 'Não foi possível concluir o cadastro. Revise os dados e tente novamente.'
    ];
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro - IFFed</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body class="page-flow">
    <div class="background-blob background-blob--left"></div>
    <div class="background-blob background-blob--right"></div>

    <main class="auth-shell auth-shell--wide">
        <section class="auth-card auth-card--wide">
            <header class="auth-card__header">
                <h1 class="auth-title">Criar nova conta</h1>
                <p class="auth-subtitle">Complete os dados abaixo para liberar seu perfil na rede</p>
            </header>

            <?php if ($alert): ?>
                <div class="alert-banner <?php echo $alert['type'] === 'error' ? 'alert-error' : 'alert-success'; ?>">
                    <?php echo htmlspecialchars($alert['message'], ENT_QUOTES, 'UTF-8'); ?>
                </div>
            <?php endif; ?>

            <form action="../php/cadastrar.php" method="POST" enctype="multipart/form-data" class="form-stack form-stack--loose">
                <div class="form-field">
                    <label class="form-label" for="foto-perfil">Foto de perfil (opcional)</label>
                    <label class="input-file">
                        <span class="input-file__label">Selecionar imagem</span>
                        <span class="input-file__hint">PNG ou JPG até 2 MB</span>
                        <input type="file" id="foto-perfil" name="foto_perfil" accept="image/*">
                    </label>
                    <p class="field-hint">Se nenhum arquivo for enviado, manteremos o avatar padrão.</p>
                </div>

                <div class="form-field">
                    <label class="form-label" for="nome">Nome completo</label>
                    <input type="text" id="nome" name="nome" class="form-input" placeholder="Seu nome" required>
                </div>

                <div class="form-field">
                    <label class="form-label" for="email">E-mail institucional</label>
                    <input type="email" id="email" name="email" class="form-input" placeholder="usuario@if.com" required>
                </div>

                <div class="form-grid form-grid--two">
                    <div class="form-field">
                        <label class="form-label" for="data_nascimento">Data de nascimento</label>
                        <input type="date" id="data_nascimento" name="data_nascimento" class="form-input" required>
                    </div>
                    <div class="form-field">
                        <label class="form-label" for="senha">Senha</label>
                        <input type="password" id="senha" name="senha" class="form-input" placeholder="Crie uma senha" required>
                    </div>
                </div>

                <div class="form-field">
                    <label class="form-label" for="bio">Biografia</label>
                    <textarea id="bio" name="bio" class="form-input form-input--textarea" placeholder="Conte um pouco sobre você"></textarea>
                    <p class="field-hint">Este texto aparece no seu perfil público.</p>
                </div>

                <button type="submit" class="button-primary">Cadastrar</button>
            </form>

            <footer class="auth-footer">
                Já possui acesso? <a href="login.php" class="link-inline">Faça login</a>
            </footer>
        </section>
    </main>
</body>
</html>