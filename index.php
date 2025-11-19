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
            width: 80px; /* Largura da barra lateral */
            background-color: #000000;
            padding-top: 20px;
            padding-bottom: 20px;
            display: flex; /* Organiza coluna */
            flex-direction: column;
            align-items: center; /* Centraliza*/
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            border-right: 1px solid #262626; /*separa a área de conteúdo */
            z-index: 1030;
        }

        .iffed-sidebar .nav-pills {
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            flex-grow: 1;
        }

        .iffed-sidebar .nav-item {
            width: 100%;
            display: flex;
            justify-content: center;
        }

        .iffed-sidebar .nav-link {
            color: #a8a8a8;
            font-size: 1.75rem;
            padding: 12px 0;
            text-align: center;
            border-radius: 0.5rem;
            width: 60px;
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

        .iffed-main-view {
            flex-grow: 1;
            margin-left: 80px; /* Espaço para a sidebar fixa */
            display: flex;
            flex-direction: column;
            background-color: #000000;
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

        <nav class="iffed-sidebar">
            <ul class="nav nav-pills flex-column">
                <li class="nav-item">
                    <a class="nav-link active" href="index.php" aria-current="page" title="Página Inicial">
                        <i class="bi bi-house-door-fill"></i> </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" title="Pesquisar">
                        <i class="bi bi-search"></i> </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" title="Criar Postagem">
                        <i class="bi bi-plus-square"></i> </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" title="Notificações">
                        <i class="bi bi-heart"></i> </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" title="Sugestões">
                        <i class="bi bi-people"></i> </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="pages/perfil.php" title="Perfil">
                        <i class="bi bi-person-circle"></i> </a>
                </li>
                <li class="nav-item iffed-logout-bottom"> <a class="nav-link" href="pages/login.php" title="Sair">
                        <i class="bi bi-box-arrow-right"></i>
                    </a>
                </li>
                <li class="nav-item iffed-menu-bottom">
                    <a class="nav-link" href="pages/cadastro.php" title="Menu">
                        <i class="bi bi-list"></i> </a>
                </li>
            </ul>
        </nav> <div class="iffed-main-view">
            <header class="iffed-top-bar">
                <div class="spacer"></div> <h1 class="page-title mb-0">Página Inicial</h1>
                <a href="pages/login.php" class="btn btn-sm btn-entrar ms-auto">Entrar</a>
            </header> <main class="iffed-content-feed">
                <h2>Bem-vindo ao IFFed!</h2>
                <p>O conteúdo e os posts estão aqui.</p>

                <div class="row">

                    <div class="col-sm-12 col-md-6 col-lg-4 mb-4">
                        <div class="card h-100"> <div class="card-header d-flex align-items-center">
                                <h5 class="card-title fs-6 mb-0">@emanuel_zzie:</h5>
                            </div>
                            <img src="assets_front/img/horarios2k24.jpg" class="card-img-top" alt="Horário 2024">
                            <div class="card-body">
                                <p class="card-text">Olhem só esse horarioooo novooooooooooooooooo!!!!!!!!!!! 🔥🔥🔥🔥🔥🔥🔥🔥</p>
                            </div>
                            <div class="card-footer post-actions">
                                <i class="bi bi-heart" title="Curtir"></i>
                                <i class="bi bi-chat" title="Comentar"></i>
                                <i class="bi bi-send" title="Compartilhar"></i>
                            </div>
                        </div>

                    </div> <div class="col-sm-12 col-md-6 col-lg-4 mb-4">
                        <div class="card h-100">
                             <div class="card-header d-flex align-items-center">
                                <h5 class="card-title fs-6 mb-0">@fulanode_TAL</h5>
                            </div>
                            <img src="assets_front/img/Mine Postagem.jpg" class="card-img-top" alt="Postagem Mine">
                            <div class="card-body">
                                <p class="card-text">Uou, me ajudou muito </p>
                            </div>
                            <div class="card-footer post-actions">
                                <i class="bi bi-heart" title="Curtir"></i>
                                <i class="bi bi-chat" title="Comentar"></i>
                                <i class="bi bi-send" title="Compartilhar"></i>
                            </div>
                        </div>

                    </div> <div class="col-sm-12 col-md-6 col-lg-4 mb-4">
                        <div class="card h-100">
                            <div class="card-header d-flex align-items-center">
                                <h5 class="card-title fs-6 mb-0">@silveira_silva</h5>
                            </div>
                            <img src="assets_front/img/Turma_fotoCosta.jpg" class="card-img-top" alt="Foto da Turma">
                            <div class="card-body">
                                <p class="card-text">Genial essa turma, parabéns</p>
                            </div>
                            <div class="card-footer post-actions">
                                <i class="bi bi-heart" title="Curtir"></i>
                                <i class="bi bi-chat" title="Comentar"></i>
                                <i class="bi bi-send" title="Compartilhar"></i>
                            </div>
                        </div>

                         </div> <div class="col-sm-12 col-md-6 col-lg-4 mb-4">
                        <div class="card h-100">
                            <div class="card-header d-flex align-items-center">
                                <h5 class="card-title fs-6 mb-0">@emanuel_zzie</h5>
                            </div>
                            <img src="assets_front/img/aaaa.jpg" class="card-img-top" alt="Foto da Turma">
                            <div class="card-body">
                                <p class="card-text">Vamos com tudo pra cima dessa gincana 🔥🔥</p>
                            </div>
                            <div class="card-footer post-actions">
                                <i class="bi bi-heart" title="Curtir"></i>
                                <i class="bi bi-chat" title="Comentar"></i>
                                <i class="bi bi-send" title="Compartilhar"></i>
                            </div>
                        </div>

                         </div> <div class="col-sm-12 col-md-6 col-lg-4 mb-4">
                        <div class="card h-100">
                            <div class="card-header d-flex align-items-center">
                                <h5 class="card-title fs-6 mb-0">@24_info</h5>
                            </div>
                            <img src="assets_front/img/PretoDourado.jpg" class="card-img-top" alt="Foto da Turma">
                            <div class="card-body">
                                <p class="card-text">escolhamm...????</p>
                            </div>
                            <div class="card-footer post-actions">
                                <i class="bi bi-heart" title="Curtir"></i>
                                <i class="bi bi-chat" title="Comentar"></i>
                                <i class="bi bi-send" title="Compartilhar"></i>
                            </div>
                        </div>

                          </div> <div class="col-sm-12 col-md-6 col-lg-4 mb-4">
                        <div class="card h-100">
                            <div class="card-header d-flex align-items-center">
                                <h5 class="card-title fs-6 mb-0">@24_info</h5>
                            </div>
                            <img src="assets_front/img/Preto.jpg" class="card-img-top" alt="Foto da Turma">
                            <div class="card-body">
                                <p class="card-text">escolhamm...????</p>
                            </div>
                            <div class="card-footer post-actions">
                                <i class="bi bi-heart" title="Curtir"></i>
                                <i class="bi bi-chat" title="Comentar"></i>
                                <i class="bi bi-send" title="Compartilhar"></i>
                            </div>
                        </div>


                          </div> <div class="col-sm-12 col-md-6 col-lg-4 mb-4">
                        <div class="card h-100">
                            <div class="card-header d-flex align-items-center">
                                <h5 class="card-title fs-6 mb-0">@24_info</h5>
                            </div>
                            <img src="assets_front/img/BrancoVerde.jpg" class="card-img-top" alt="Foto da Turma">
                            <div class="card-body">
                                <p class="card-text">escolhamm...????</p>
                            </div>
                            <div class="card-footer post-actions">
                                <i class="bi bi-heart" title="Curtir"></i>
                                <i class="bi bi-chat" title="Comentar"></i>
                                <i class="bi bi-send" title="Compartilhar"></i>
                            </div>
                        </div>


                          </div> <div class="col-sm-12 col-md-6 col-lg-4 mb-4">
                        <div class="card h-100">
                            <div class="card-header d-flex align-items-center">
                                <h5 class="card-title fs-6 mb-0">@24_info</h5>
                            </div>
                            <img src="assets_front/img/branco.jpg" class="card-img-top" alt="Foto da Turma">
                            <div class="card-body">
                                <p class="card-text">escolhamm...????</p>
                            </div>
                            <div class="card-footer post-actions">
                                <i class="bi bi-heart" title="Curtir"></i>
                                <i class="bi bi-chat" title="Comentar"></i>
                                <i class="bi bi-send" title="Compartilhar"></i>
                            </div>
                        </div>

                          </div> <div class="col-sm-12 col-md-6 col-lg-4 mb-4">
                        <div class="card h-100">
                            <div class="card-header d-flex align-items-center">
                                <h5 class="card-title fs-6 mb-0">@24_info</h5>
                            </div>
                            <img src="assets_front/img/AzulBranco.jpg" class="card-img-top" alt="Foto da Turma">
                            <div class="card-body">
                                <p class="card-text">escolhamm...????</p>
                            </div>
                            <div class="card-footer post-actions">
                                <i class="bi bi-heart" title="Curtir"></i>
                                <i class="bi bi-chat" title="Comentar"></i>
                                <i class="bi bi-send" title="Compartilhar"></i>
                            </div>
                        </div>


                         </div> <div class="col-sm-12 col-md-6 col-lg-4 mb-4">
                        <div class="card h-100">
                            <div class="card-header d-flex align-items-center">
                                <h5 class="card-title fs-6 mb-0">@jubiresculo_azevedo</h5>
                            </div>
                            <img src="assets_front/img/MomentoFofo.jpg" class="card-img-top" alt="Foto da Turma">
                            <div class="card-body">
                                <p class="card-text">Precisava Registrar isso😍</p>
                            </div>
                            <div class="card-footer post-actions">
                                <i class="bi bi-heart" title="Curtir"></i>
                                <i class="bi bi-chat" title="Comentar"></i>
                                <i class="bi bi-send" title="Compartilhar"></i>
                            </div>
                        </div>


                        </div> <div class="col-sm-12 col-md-6 col-lg-4 mb-4">
                        <div class="card h-100">
                            <div class="card-header d-flex align-items-center">
                                <h5 class="card-title fs-6 mb-0">@juricio_azevedo</h5>
                            </div>
                            <img src="assets_front/img/MedoTraumasentimentosdeangstia.jpg" class="card-img-top" alt="Foto da Turma">
                            <div class="card-body">
                                <p class="card-text">Traumas</p>
                            </div>
                            <div class="card-footer post-actions">
                                <i class="bi bi-heart" title="Curtir"></i>
                                <i class="bi bi-chat" title="Comentar"></i>
                                <i class="bi bi-send" title="Compartilhar"></i>
                            </div>
                        </div>

                          </div> <div class="col-sm-12 col-md-6 col-lg-4 mb-4">
                        <div class="card h-100">
                            <div class="card-header d-flex align-items-center">
                                <h5 class="card-title fs-6 mb-0">@juricio_azevedo</h5>
                            </div>
                            <img src="assets_front/img/BomDia.jpg" class="card-img-top" alt="Foto da Turma">
                            <div class="card-body">
                                <p class="card-text">Estudem pessoal!!!!!!!!!!!!!!!!!!!!</p>
                            </div>
                            <div class="card-footer post-actions">
                                <i class="bi bi-heart" title="Curtir"></i>
                                <i class="bi bi-chat" title="Comentar"></i>
                                <i class="bi bi-send" title="Compartilhar"></i>
                            </div>
                        </div>

                        
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>