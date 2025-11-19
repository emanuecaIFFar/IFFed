<?php
session_start();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>iffed - Página Inicial</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            margin: 0;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            overflow-x: hidden;
            background-color: #000000; /* Garante fundo preto se o container não cobrir tudo */
        }

        /* Principal da iffed */
        .iffed-app-container {
            display: flex; /*(sidebar + conteúdo) */
            min-height: 100vh; /* Altura mínima de 100% da viewport */
            background-color: #000000;
        }

        /* (Sidebar) */
        .iffed-sidebar {
            width: 80px; /* largura encolhida */
            background-color: #000000;
            padding-top: 20px;
            padding-bottom: 20px;
            display: flex; /* Organiza coluna */
            flex-direction: column;
            align-items: flex-start; /* alinha ícone + texto à esquerda */
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            border-right: 1px solid #262626; /*separa a área de conteúdo */
            z-index: 1030;
            transition: width 0.25s ease-in-out;
        }

        .iffed-sidebar.expanded {
            width: 260px; /* largura quando expandida */
        }

        .iffed-sidebar .nav-pills {
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: stretch;
            flex-grow: 1;
        }

        .iffed-sidebar .nav-item {
            width: 100%;
        }

        .iffed-sidebar .nav-link {
            color: #a8a8a8;
            font-size: 1.75rem;
            padding: 12px 0;
            border-radius: 0.5rem;
            width: 100%;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 10px;
        }

        .iffed-sidebar .nav-link:hover {
            background-color: #181818;
            color: #ffffff;
        }

        .iffed-sidebar .nav-link.active {
            color: #ffffff;
            background-color: #202020;
        }

        .iffed-sidebar .nav-item.iffed-menu-bottom {
            margin-top: auto; /* Empurra o item "Menu" para o final */
        }

        /* Menu lateral expansível (usa os mesmos ícones da sidebar) */
        .iffed-sidebar .nav-link span.label {
            display: none;
            margin-left: 8px;
            font-size: 0.95rem;
        }

        .iffed-sidebar.expanded .nav-link {
            justify-content: flex-start;
            padding-left: 18px;
            font-size: 1.2rem;
        }

        .iffed-sidebar.expanded .nav-link span.label {
            display: inline;
        }

        .iffed-main-view {
            flex-grow: 1;
            margin-left: 80px; /* Espaço para a sidebar fixa encolhida */
            display: flex;
            flex-direction: column;
            background-color: #000000;
            transition: margin-left 0.25s ease-in-out;
        }

        .iffed-sidebar.expanded ~ .iffed-main-view {
            margin-left: 260px; /* acompanha a sidebar expandida */
        }

        /* Barra superior */
        .iffed-top-bar {
            background-color: #000000;
            color: #ffffff;
            height: 60px;
            padding: 0 24px;
            border-bottom: 1px solid #262626;
            display: flex;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 1020;
        }

        .iffed-top-bar .page-title {
            font-size: 1.25rem;
            font-weight: 600;
            text-align: center;
            flex-grow: 1;
        }

        .iffed-top-bar .btn-entrar {
            font-size: 0.9rem;
            padding: 0.3rem 1rem;
            background-color: #efefef;
            color: #000000;
            border: none;
        }
        .iffed-top-bar .btn-entrar:hover {
            background-color: #ffffff;
        }
        .iffed-top-bar .spacer {
            min-width: 80px; /* Ajustar conforme o tamanho do botão "Entrar" para centralizar o título */
        }

        /* Área do feed de conteúdo */
        .iffed-content-feed {
            padding: 20px;
            flex-grow: 1;
            color: #e0e0e0;
        }

        /* Estilização para os Cards (Substituindo .placeholder-post) */
        .iffed-content-feed .card {
            background-color: #1c1c1c; /* Cor de fundo escura para o card */
            border: 1px solid #363636;  /* Borda sutil para o card */
            color: #e0e0e0; /* Cor do texto no card */
        }

        .iffed-content-feed .card .card-header {
            background-color: #232323;
            border-bottom: 1px solid #363636;
            padding: 0.75rem 1rem;
        }
         .iffed-content-feed .card .card-title {
            color: #e0e0e0;
        }

        .iffed-content-feed .card .card-img-top {
            border-radius: 0; /* Para remover arredondamento se o card header estiver presente */
             margin-top:0; /* Resetando margem da imagem original */
        }
        .iffed-content-feed .card .card-body{
            padding: 1rem;
        }
         .iffed-content-feed .card .card-text{
            color: #c0c0c0;
        }


        .iffed-content-feed .card .card-footer.post-actions {
            background-color: #1c1c1c;
            border-top: 1px solid #363636;
            padding: 0.75rem 1rem;
        }

        .iffed-content-feed .post-actions i {
            font-size: 1.35rem;
            color: #a8a8a8;
            margin-right: 18px;
            cursor: default; /* Indica que não é clicável */
        }

        .iffed-content-feed .post-actions i:last-child {
            margin-right: 0;
        }

    </style>
</head>
<body>
    <div class="iffed-app-container">

        <nav class="iffed-sidebar" id="iffedSidebar">
            <ul class="nav nav-pills flex-column">
                <li class="nav-item">
                    <a class="nav-link active" href="index.php" aria-current="page" title="Página Inicial">
                        <i class="bi bi-house-door-fill"></i>
                        <span class="label">Página inicial</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" title="Pesquisar">
                        <i class="bi bi-search"></i>
                        <span class="label">Pesquisar</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" title="Criar Postagem">
                        <i class="bi bi-plus-square"></i>
                        <span class="label">Novo post</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" title="Notificações">
                        <i class="bi bi-heart"></i>
                        <span class="label">Notificações</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" title="Sugestões">
                        <i class="bi bi-people"></i>
                        <span class="label">Comunidades</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="pages/perfil.php" title="Perfil">
                        <i class="bi bi-person-circle"></i>
                        <span class="label">Perfil</span>
                    </a>
                </li>
                <li class="nav-item iffed-logout-bottom">
                    <a class="nav-link" href="pages/login.php" title="Sair">
                        <i class="bi bi-box-arrow-right"></i>
                        <span class="label">Sair</span>
                    </a>
                </li>
                <li class="nav-item iffed-menu-bottom">
                    <button class="nav-link" type="button" title="Menu" onclick="toggleSidebarMenu()" style="background:none;border:none;padding:0;">
                        <i class="bi bi-list"></i>
                    </button>
                </li>
            </ul>
        </nav>

        <div class="iffed-main-view">
            <header class="iffed-top-bar">
                <div class="spacer"></div>
                <h1 class="page-title mb-0">Página Inicial</h1>
                <?php if (!isset($_SESSION['nome_usuario'])): ?>
                    <a href="pages/login.php" class="btn btn-sm btn-entrar ms-auto">Entrar</a>
                <?php else: ?>
                    <a href="pages/perfil.php" class="btn btn-sm btn-entrar ms-auto">Meu perfil</a>
                <?php endif; ?>
            </header>

            <main class="iffed-content-feed">
                <h2>Bem-vindo ao IFeed!</h2>
                <p>Do corredor direto para a sua timeline.</p>

                <!-- Primeiro post fixo, alinhado à esquerda como um post normal -->
                <div class="row mt-4">
                    <div class="col-sm-12 col-md-6 col-lg-4 mb-4">
                        <div class="card h-100">
                            <img src="assets/img/IFFed.jpg" class="card-img-top" alt="Logo IFFed">
                            <div class="card-footer post-actions">
                                <i class="bi bi-heart" title="Curtir"></i>
                                <i class="bi bi-chat" title="Comentar"></i>
                                <i class="bi bi-send" title="Compartilhar"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Aqui futuramente entram os posts reais dos usuários (não centralizados) -->
            </main>

            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

            <script>
                function toggleSidebarMenu() {
                    var sidebar = document.getElementById('iffedSidebar');
                    if (sidebar.classList.contains('expanded')) {
                        sidebar.classList.remove('expanded');
                    } else {
                        sidebar.classList.add('expanded');
                    }
                }
            </script>
        </div>
    </div>
</body>
</html>