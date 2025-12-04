<?php
/*
minha ideia é pra funcionar mais ou menos assim
 
 1. o cara acessa cadastro.php
 2. bota nome, email, senha, data, bio e foto
 3. clica em "Cadastrar"
 4. ai o php envia dados para cadastrar.php (com a foto)
 5. se der certo → vai pro login com ?sucesso=cadastrado
 6. se der errado → volta pra cá com ?status=erro
 */

// nicializa $alert como null (sem mensagem), pois pode existir post sem mensagens
$alert = null;

/*
 * 
 * 
 * isset() = se existir variavel, pega o get status 
 * $_GET['status'] = pega o valor 'status' na URL
 * === = comparação estrita (valor E tipo devem ser iguais)
 */
if (isset($_GET['status']) && $_GET['status'] === 'ok') {
    // URL: cadastro.php?status=ok
    $alert = [
        'type' => 'success',
        'message' => 'Cadastro realizado com sucesso! Faça login para começar a usar o IFeed.'  // cria array com mensagem de sucesso
    ];
} elseif (isset($_GET['status']) && $_GET['status'] === 'erro') {
    // URL: cadastro.php?status=erro

    $alert = [
        'type' => 'error',
        'message' => 'Não foi possível concluir o cadastro. Revise os dados e tente novamente.'// Cria array com mensagem de erro
    ];
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro - IFeed</title>
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

            <?php 
            /*
             banner de alerta APENAS se $alert não for null
            */
            if ($alert): ?>
                <div class="alert-banner <?php 
                    echo $alert['type'] === 'error' ? 'alert-error' : 'alert-success'; 
                ?>">
                    <?php 
                    /*
                    basicamente é uma proteção contra ataques xss (usado em sites mais avançados)
                     */
                    echo htmlspecialchars($alert['message'], ENT_QUOTES, 'UTF-8'); 
                    ?>
                </div>
            <?php endif; ?>
            <form action="../php/cadastrar.php" method="POST" enctype="multipart/form-data" class="form-stack form-stack--loose">
                
                <!-- CAMPO DE FOTO DE PERFIL -->
                <div class="form-field" style="align-items: center;">
                    <label class="form-label">Foto de perfil</label>
                    <div class="avatar-upload-container">
                        <div class="avatar-preview">
                            <!-- Imagem de preview (começa com a padrão) -->
                            <img id="avatar-preview-img" src="../assets/img/padrao.svg" alt="Avatar">
                        </div>
                        <label for="foto-perfil" class="avatar-upload-btn" title="Alterar foto">
                            <!-- Ícone de câmera em SVG -->
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg>
                        </label>
                        <!--
                            
                            tipo assim;
                            type="file" → permite escolher arquivo do computador
                            name="foto_perfil" → no PHP será $_FILES['foto_perfil']
                            accept="image/*" → só aceita imagens (jpg, png, etc)
                            
                            onchange = quando o usuário escolhe uma foto,
                            atualiza o preview (isso é o único JS visual aqui)
                        -->
                        <input type="file" id="foto-perfil" name="foto_perfil" accept="image/*" style="display: none;" onchange="document.getElementById('avatar-preview-img').src = window.URL.createObjectURL(this.files[0])">
                    </div>
                    <p class="field-hint">Toque no ícone para escolher</p>
                </div>

                <!-- CAMPO NOME -->
                <div class="form-field">
                    <label class="form-label" for="nome">Nome completo</label>
                    <input type="text" id="nome" name="nome" class="form-input" placeholder="Seu nome" required>
                </div>

                <!-- CAMPO EMAIL -->
                <div class="form-field">
                    <label class="form-label" for="email">E-mail institucional</label>
                    <!-- type="email" → navegador valida se é email válido -->
                    <input type="email" id="email" name="email" class="form-input" placeholder="usuario@if.com" required>
                </div>

                <!-- LINHA COM 2 CAMPOS (DATA E SENHA) -->
                <div class="form-grid form-grid--two">
                    <div class="form-field">
                        <label class="form-label" for="data_nascimento">Data de nascimento</label>
                        <!-- type="date" → mostra um calendário no navegador -->
                        <input type="date" id="data_nascimento" name="data_nascimento" class="form-input" required>
                    </div>
                    <div class="form-field">
                        <label class="form-label" for="senha">Senha</label>
                        <!-- type="password" → esconde o texto digitado -->
                        <input type="password" id="senha" name="senha" class="form-input" placeholder="Crie uma senha" required>
                    </div>
                </div>

                <!-- CAMPO BIOGRAFIA -->
                <div class="form-field">
                    <label class="form-label" for="bio">Biografia</label>
                    <!-- textarea = campo de texto com múltiplas linhas -->
                    <textarea id="bio" name="bio" class="form-input form-input--textarea" placeholder="Conte um pouco sobre você"></textarea>
                    <p class="field-hint">Este texto aparece no seu perfil público.</p>
                </div>

                <!-- BOTÃO DE ENVIAR -->
                <button type="submit" class="button-primary">Cadastrar</button>
            </form>

            <footer class="auth-footer">
                Já possui acesso? <a href="login.php" class="link-inline">Faça login</a>
            </footer>
        </section>
    </main>
</body>
</html>