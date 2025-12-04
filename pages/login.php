<?php
/*

 o login pensei em fazer parecido com o cadastro
  
  1. usuário acessa login.php
  2. prpereenche email e senha
  3. clica em "Entrar"
  4. formulário envia dados para validar_login.php
  5. se der certo → vai pro perfil
  6. se der errado → volta pra cá com ?erro=login na URL
 */


$alert = null; //guardar a mensagem que aparece no topo da página

/* (pra eu entender o php melhor, APAGAR ISSO DEPOIS)
 * $_GET é uma "superglobal" do PHP que contém os parâmetros da URL
 * 
 * Exemplo de URL: login.php?erro=login
 *                          ↑__________↑
 *                          $_GET['erro'] = 'login'
 * 
 * isset() = se a variável existe (evita erro se não existir)
 * === = comparação estrita (compara valor E tipo)
 * && = operador "E" (as duas condições precisam ser verdadeiras)
 */
if (isset($_GET['erro']) && $_GET['erro'] === 'login') {
    // se a URL tem ?erro=login, cria um array com a mensagem de erro
    $alert = [
        'type' => 'error',  // arrey de erro ai o
        'message' => 'Usuário ou senha incorretos. Caso ainda não tenha conta, faça seu cadastro para continuar.'
    ];
} elseif (isset($_GET['sucesso']) && $_GET['sucesso'] === 'cadastrado') {
    // se a URL tem ?sucesso=cadastrado (vindo do cadastro), mostra mensagem verde
    $alert = [
        'type' => 'success',  // arrey de sucesso ai
        'message' => 'Cadastro confirmado! Entre com seus dados para acessar a rede.'
    ];
}
// se não existir nenhum parâmetro na URL, $alert continua null e nada aparece
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - IFeed</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <div class="background-blob background-blob--left"></div>
    <div class="background-blob background-blob--right"></div>

    <main class="auth-shell">
        <section class="auth-card">
            <header class="auth-card__header">
                <h1 class="auth-title">Entrar no IFeed</h1>
                <p class="auth-subtitle">Informe seus dados para validar o acesso</p>
            </header>

            <?php 
            if ($alert): ?>
                <div class="alert-banner <?php 
                    echo $alert['type'] === 'error' ? 'alert-error' : 'alert-success'; 
                ?>">
                    <?php 
                    echo htmlspecialchars($alert['message'], ENT_QUOTES, 'UTF-8'); 
                    ?>
                </div>
            <?php endif; ?>

            <!--
                esquema do login (apagar quando eu finalizar);
                
                action = para onde os dados vão quando clicar em "Entrar"
                method = como os dados são enviados:
                    L-- POST = dados ficam "escondidos" (ideal para senhas)
                    L-- GET = dados ficam na URL (nunca use para senhas!)
                
                os dados vão para validar_login.php como $_POST['email'] e $_POST['senha']
            -->
            <form action="../php/validar_login.php" method="POST" class="form-stack" autocomplete="off">
                <div class="form-field">
                    <label for="login-email" class="form-label">E-mail cadastrado</label>
                    <!--
                        name="email" → é assim que o PHP vai acessar: $_POST['email']
                        required → o navegador não deixa enviar se estiver vazio
                        type="email" → o navegador valida se é um email válido
                    -->
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
                    <!--
                        type="password" → mostra bolinhas em vez do texto
                        name="senha" → $_POST['senha'] no PHP
                    -->
                    <input
                        id="login-senha"
                        type="password"
                        name="senha"
                        required
                        class="form-input"
                        placeholder="••••••••"
                    />
                </div>

                <!-- type="submit" → ao clicar, envia o formulário -->
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