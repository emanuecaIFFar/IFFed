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
                <div class="form-field" style="align-items: center;">
                    <label class="form-label">Foto de perfil</label>
                    <div class="avatar-upload-container">
                        <div class="avatar-preview">
                            <img id="avatar-preview-img" src="../assets/img/padrao.jpg" alt="Avatar">
                        </div>
                        <label for="foto-perfil" class="avatar-upload-btn" title="Alterar foto">
                            <!-- Ícone de câmera (SVG inline para não depender de lib externa aqui) -->
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg>
                        </label>
                        <input type="file" id="foto-perfil" name="foto_perfil" accept="image/*" style="display: none;" onchange="document.getElementById('avatar-preview-img').src = window.URL.createObjectURL(this.files[0])">
                    </div>
                    <p class="field-hint">Toque no ícone para escolher</p>
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