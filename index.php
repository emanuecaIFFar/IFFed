<?php
// Habilita saída de erros para diagnóstico local (remova em produção)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
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
    <!-- Tailwind (para estilos rápidos da nova sidebar) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Lucide icons (usado pela nova sidebar) -->
    <script src="https://unpkg.com/lucide@latest"></script>
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

        /* Meta dos posts (handle, data) — mais contraste que o .text-muted padrão do Bootstrap */
        .iffed-content-feed .card-header small.post-meta {
            color: #646464ff; /* mais claro que #888 */
            opacity: 0.95;
            font-weight: 500;
            font-size: 0. ninetyfiverem;
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

        /* Styles adicionais para a nova sidebar (transição compacta/expandida) */
        /* Utility para esconder/mostrar labels suavemente */
        .sidebar-label {
            opacity: 0;
            display: none;
            white-space: nowrap;
            transition: opacity 0.2s ease-in-out;
        }
        .expanded .sidebar-label {
            display: inline-block;
            opacity: 1;
        }

        /* Ajustes de largura quando a sidebar é trocada */
        #sidebar { width: 80px; }
        #sidebar.expanded { width: 260px; }

        /* Ajustes visuais do cabeçalho de boas-vindas */
        .iffed-content-feed h1.display-4 {
            /* Garante uma fonte limpa e sem serifa igual da imagem */
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            
            font-size: 52px;    /* Levemente menor que 56px para ficar mais elegante */
            line-height: 1.1;
            font-weight: 300;   /* MUDANÇA PRINCIPAL: De 700 para 400 (Regular). Na foto a letra é fina. */
            color: #ffffff;
            margin: 0 0 5px 0; /* Um pouco mais de espaço embaixo */
            letter-spacing: -0.5px; /* Aproxima levemente as letras para ficar moderno */
        }

        .iffed-content-feed p.lead {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            
            font-size: 20px;    /* MUDANÇA: 14px é muito pequeno. Na foto o texto de apoio é maior. */
            color: #e0e0e0;     /* Um cinza quase branco, mais claro que o #cfcfcf */
            margin: 0 0 18px 0;
            font-weight: 400;   /* Normal */
        }

    </style>
</head>
<body>
    <div class="iffed-app-container">

        <!-- Nova sidebar (substitui a antiga) -->
        <nav id="sidebar" class="fixed top-0 left-0 h-full bg-black border-r border-[#262626] z-50 flex flex-col justify-between py-5 transition-all duration-300 ease-in-out">
            <div class="flex flex-col w-full px-2 space-y-2">
                <a href="index.php" class="flex items-center h-[60px] w-full rounded-lg transition-colors duration-200 text-white bg-[#202020] justify-center group nav-item" title="Página Inicial">
                    <i data-lucide="home" class="w-7 h-7 stroke-[2]"></i>
                    <span class="ml-4 text-lg font-medium sidebar-label">Página Inicial</span>
                </a>
                <a href="pages/pesquisar_nseifazrisso.html" class="flex items-center h-[60px] w-full rounded-lg transition-colors duration-200 text-[#a8a8a8] hover:bg-[#181818] hover:text-white justify-center group nav-item" title="Pesquisar">
                    <i data-lucide="search" class="w-7 h-7 stroke-[2]"></i>
                    <span class="ml-4 text-lg font-medium sidebar-label">Pesquisar</span>
                </a>
                <a href="pages/criar_post.html" class="flex items-center h-[60px] w-full rounded-lg transition-colors duration-200 text-[#a8a8a8] hover:bg-[#181818] hover:text-white justify-center group nav-item" title="Novo Post">
                    <i data-lucide="plus-square" class="w-7 h-7 stroke-[2]"></i>
                    <span class="ml-4 text-lg font-medium sidebar-label">Novo Post</span>
                </a>
                    <a href="pages/notificacoes.php" class="flex items-center h-[60px] w-full rounded-lg transition-colors duration-200 text-[#a8a8a8] hover:bg-[#181818] hover:text-white justify-center group nav-item" title="Notificações">
                    <i data-lucide="heart" class="w-7 h-7 stroke-[2]"></i>
                    <span class="ml-4 text-lg font-medium sidebar-label">Notificações</span>
                </a>
                <a href="pages/comunidades.html" class="flex items-center h-[60px] w-full rounded-lg transition-colors duration-200 text-[#a8a8a8] hover:bg-[#181818] hover:text-white justify-center group nav-item" title="Comunidades">
                    <i data-lucide="users" class="w-7 h-7 stroke-[2]"></i>
                    <span class="ml-4 text-lg font-medium sidebar-label">Comunidades</span>
                </a>
                <a href="pages/perfil.php" class="flex items-center h-[60px] w-full rounded-lg transition-colors duration-200 text-[#a8a8a8] hover:bg-[#181818] hover:text-white justify-center group nav-item" title="Perfil">
                    <i data-lucide="user-circle-2" class="w-7 h-7 stroke-[2.5]"></i>
                    <span class="ml-4 text-lg font-medium sidebar-label">Perfil</span>
                </a>
            </div>

            <div class="flex flex-col w-full px-2 space-y-2 mb-2">
                <a href="php/sair.php" class="flex items-center h-[60px] w-full rounded-lg transition-colors duration-200 text-[#a8a8a8] hover:bg-[#181818] hover:text-white justify-center group nav-item" title="Sair">
                    <i data-lucide="log-out" class="w-7 h-7 stroke-[2]"></i>
                    <span class="ml-4 text-lg font-medium sidebar-label">Sair</span>
                </a>

                <button id="toggleBtn" class="flex items-center justify-center h-[60px] w-full rounded-lg transition-colors duration-200 text-[#a8a8a8] hover:bg-[#181818] hover:text-white outline-none">
                    <i data-lucide="menu" class="w-7 h-7 stroke-[2]"></i>
                    <span class="ml-4 text-lg font-medium sidebar-label">Recolher</span>
                </button>
            </div>
        </nav>

        <div id="main-content" class="iffed-main-view">
            <header class="iffed-top-bar">
                <div class="spacer"></div> <h1 class="page-title mb-0">Página Inicial</h1>
            </header> <main class="iffed-content-feed">
                <h1 class="display-4">Bem-vindo ao IFeed!</h1>
                <p class="lead">Do corredor direto para a sua timeline.</p>

                <div class="row">
                    <?php
                    // Carrega posts mais recentes e mostra no feed
                    require_once __DIR__ . '/php/conexao.php';

                        $current_user = isset($_SESSION['id']) ? intval($_SESSION['id']) : 0;
                        $sql = "SELECT p.id, p.conteudo_textual, p.imagem, p.data_criacao, p.num_comentarios, p.num_curtidas, u.id AS usuario_id, u.nome, u.foto,
                               EXISTS(SELECT 1 FROM curtidas c WHERE c.id_postagem = p.id AND c.id_usuario = $current_user) AS liked
                            FROM postagens p
                            LEFT JOIN perfil u ON p.id_usuario = u.id
                            ORDER BY p.data_criacao DESC
                            LIMIT 50";

                    if ($res = $conn->query($sql)) {
                        // Carrega dados do usuário atual para usar no input de comentário
                        $sessionUserFoto = 'assets/img/padrao.jpg';
                        $sessionUserName = '';
                        if ($current_user) {
                            $stmU = $conn->prepare('SELECT nome, foto FROM perfil WHERE id = ? LIMIT 1');
                            if ($stmU) {
                                $stmU->bind_param('i', $current_user);
                                $stmU->execute();
                                $stmU->bind_result($u_nome, $u_foto);
                                if ($stmU->fetch()) {
                                    $sessionUserName = $u_nome ?? '';
                                    $sf = $u_foto ?? '';
                                    if (empty($sf)) {
                                        $sessionUserFoto = 'assets/img/padrao.jpg';
                                    } elseif (strpos($sf, 'uploads/') === 0) {
                                        $sessionUserFoto = 'assets/' . $sf;
                                    } elseif (strpos($sf, 'assets_front') !== false || strpos($sf, 'http') === 0) {
                                        $sessionUserFoto = $sf;
                                    } else {
                                        $sessionUserFoto = 'assets/uploads/' . $sf;
                                    }
                                }
                                $stmU->close();
                            }
                        }

                        while ($row = $res->fetch_assoc()) {
                            $autorNome = htmlspecialchars($row['nome'] ?? 'Usuário');
                            $foto = $row['foto'] ?? '';
                            if (empty($foto)) {
                                $autorFoto = 'assets/img/padrao.jpg';
                            } elseif (strpos($foto, 'uploads/') === 0) {
                                $autorFoto = 'assets/' . $foto; // foto já guarda 'uploads/arquivo.jpg'
                            } elseif (strpos($foto, 'assets_front') !== false || strpos($foto, 'http') === 0) {
                                $autorFoto = $foto;
                            } else {
                                $autorFoto = 'assets/uploads/' . $foto; // nome simples
                            }
                            $conteudo = nl2br(htmlspecialchars($row['conteudo_textual']));
                            $imagem = $row['imagem'] ? 'assets/uploads/' . $row['imagem'] : null;
                            $data = date('d/m/Y H:i', strtotime($row['data_criacao']));
                            $num_curtidas = intval($row['num_curtidas']);
                            $num_comentarios = intval($row['num_comentarios']);
                            $liked = !empty($row['liked']);
                    ?>

                    <div class="col-sm-12 col-md-6 col-lg-4 mb-4">
                        <div class="card h-100">
                            <div class="card-header d-flex align-items-center">
                                    <div class="me-3" style="width:44px; height:44px; overflow:hidden; border-radius:50%;">
                                    <img src="<?php echo $autorFoto; ?>" alt="Avatar" style="width:100%; height:100%; object-fit:cover;" onerror="this.onerror=null;this.src='assets/img/padrao.jpg';">
                                </div>
                                <div class="flex-grow-1">
                                    <div class="card-title mb-0"><?php echo $autorNome; ?></div>
                                    <small class="post-meta">@<?php echo strtolower(preg_replace('/\s+/','_', $autorNome)); ?> • <?php echo $data; ?></small>
                                </div>
                            </div>

                            <?php if ($imagem): ?>
                                <img src="<?php echo $imagem; ?>" class="card-img-top" alt="Post image" onerror="this.style.display='none';">
                            <?php endif; ?>

                            <div class="card-body">
                                <p class="card-text"><?php echo $conteudo ?: '<em>—</em>'; ?></p>
                            </div>

                            <div class="card-footer post-actions d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center">
                                    <button class="btn-like btn btn-link text-decoration-none p-0" data-post-id="<?php echo $row['id']; ?>" aria-label="Curtir">
                                        <i class="bi <?php echo $liked ? 'bi-heart-fill text-danger' : 'bi-heart'; ?> like-icon" ></i>
                                    </button>
                                    <span class="ms-2 like-count"><?php echo $num_curtidas; ?></span>

                                    <button class="btn-comment btn btn-link text-decoration-none p-0 ms-3" data-post-id="<?php echo $row['id']; ?>" aria-label="Comentar">
                                        <i class="bi bi-chat"></i>
                                    </button>
                                    <span class="ms-2 comment-count"><?php echo $num_comentarios; ?></span>
                                </div>
                                <div>
                                    <i class="bi bi-send" title="Compartilhar"></i>
                                </div>
                            </div>

                            <!-- Painel de comentários (oculto por padrão) -->
                            <div id="comments-panel-<?php echo $row['id']; ?>" class="comments-panel" style="display:none;border-top:1px solid #2b2b2b;background:#0f0f0f;padding:12px;">
                                <div class="comments-list" style="max-height:220px;overflow:auto;padding-right:6px;">
                                        <!-- Comentários carregados via AJAX quando o painel for aberto -->
                                        <div class="text-muted" style="font-size:0.95rem;padding:8px 4px;">Clique no ícone de comentário para abrir e carregar os comentários.</div>
                                    </div>

                                <!-- Input para novo comentário -->
                                <div class="mt-2 d-flex align-items-start comment-input">
                                    <div style="width:40px;height:40px;overflow:hidden;border-radius:50%;margin-right:8px;flex:0 0 40px;">
                                        <img src="<?php echo $sessionUserFoto; ?>" alt="sua foto" style="width:100%;height:100%;object-fit:cover;" onerror="this.src='assets/img/padrao.jpg'">
                                    </div>
                                    <div style="flex:1;display:flex;gap:8px;">
                                        <input type="text" class="form-control form-control-sm comment-text-input" placeholder="Adicione um comentário..." />
                                        <button class="btn btn-sm btn-primary btn-send-comment" data-post-id="<?php echo $row['id']; ?>">Enviar</button>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    <?php
                        }
                        $res->free();
                    } else {
                        // Em caso de erro, mostra mensagem simples no feed
                        echo '<div class="col-12"><div class="alert alert-warning">Não foi possível carregar as postagens.</div></div>';
                    }
                    ?>
                </div>
            </main>

            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

            <script>
                // Inicializa os ícones Lucide
                    if (window.lucide) lucide.createIcons();

                // Lógica da nova Sidebar (toggle expand/collapse)
                (function(){
                    var sidebar = document.getElementById('sidebar');
                    var mainContent = document.getElementById('main-content');
                    var toggleBtn = document.getElementById('toggleBtn');
                    var navItems = document.querySelectorAll('.nav-item');

                    if(!toggleBtn) return;

                    toggleBtn.addEventListener('click', function(){
                        var isExpanded = sidebar.classList.toggle('expanded');
                        if(isExpanded){
                            mainContent.style.marginLeft = '260px';
                            navItems.forEach(function(item){
                                item.classList.remove('justify-center');
                                item.classList.add('justify-start','px-4');
                            });
                        } else {
                            mainContent.style.marginLeft = '80px';
                            navItems.forEach(function(item){
                                item.classList.remove('justify-start','px-4');
                                item.classList.add('justify-center');
                            });
                        }
                    });
                })();
            </script>
            <script>
                // Dados do usuário logado para usar ao inserir comentários dinamicamente
                var SESSION_USER = <?php echo json_encode([ 'id' => $current_user, 'name' => $sessionUserName, 'foto' => $sessionUserFoto ]); ?>;
            </script>
            <script>
                // Funções de interatividade: curtir e comentar via AJAX
                (function(){
                    // Escapa HTML para evitar XSS ao inserir comentários dinamicamente
                    function escapeHtml(str){
                        if (!str) return '';
                        return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#039;');
                    }
                    // Curtir / Descurtir (delegação + logs de depuração)
                    document.addEventListener('click', function(e){
                        var btn = e.target.closest ? e.target.closest('.btn-like') : null;
                        if (!btn) return;
                        e.preventDefault();
                        // Debug: mostre o post id no console
                        var postId = btn.getAttribute('data-post-id');
                        console.debug('Curtir clicado, postId=', postId);

                        // Evita cliques rápidos duplicados
                        if (btn.dataset.disabled === '1') {
                            console.debug('Clique ignorado (aguardando resposta) para post', postId);
                            return;
                        }
                        btn.dataset.disabled = '1';

                        var icon = btn.querySelector('.like-icon') || document.querySelector('.btn-like[data-post-id="' + postId + '"] .like-icon');
                        var card = btn.closest('.card');
                        var countEl = card ? card.querySelector('.like-count') : btn.parentElement.querySelector('.like-count');

                        var form = new FormData();
                        form.append('post_id', postId);

                        fetch('php/like.php', { method: 'POST', credentials: 'same-origin', body: form })
                            .then(function(r){ return r.json().catch(function(){ return { success: false, rawStatus: r.status }; }); })
                            .then(function(json){
                                btn.dataset.disabled = '0';
                                console.debug('Resposta like.php para post', postId, json);
                                if (!json || !json.success) return;
                                if (countEl) countEl.textContent = json.total;
                                if (icon) {
                                    if (json.action === 'liked') {
                                        icon.classList.remove('bi-heart');
                                        icon.classList.add('bi-heart-fill', 'text-danger');
                                    } else {
                                        icon.classList.remove('bi-heart-fill', 'text-danger');
                                        icon.classList.add('bi-heart');
                                    }
                                }
                            }).catch(function(err){
                                console.error('Erro ao chamar like.php', err);
                                btn.dataset.disabled = '0';
                            });
                    });

                    // Comentar: abre/fecha painel de comentários e envia comentário inline
                    document.querySelectorAll('.btn-comment').forEach(function(btn){
                        btn.addEventListener('click', function(e){
                            e.preventDefault();
                            var postId = this.getAttribute('data-post-id');
                            var panel = document.getElementById('comments-panel-' + postId);
                            if (!panel) return;
                            var isHidden = panel.style.display === 'none' || panel.style.display === '';
                            if (isHidden) {
                                panel.style.display = 'block';
                                var cl = panel.querySelector('.comments-list');
                                if (cl && !cl.dataset.loaded) {
                                    // Carrega comentários via AJAX
                                    cl.innerHTML = '<div class="text-muted" style="padding:8px">Carregando comentários...</div>';
                                    fetch('php/get_comments.php?post_id=' + encodeURIComponent(postId), { credentials: 'same-origin' })
                                        .then(r => r.json())
                                        .then(json => {
                                            if (!json.success) {
                                                cl.innerHTML = '<div class="text-danger" style="padding:8px">Erro ao carregar.</div>';
                                                return;
                                            }
                                            cl.innerHTML = '';
                                            json.data.forEach(function(c){
                                                var div = document.createElement('div');
                                                div.className = 'd-flex align-items-start mb-2';
                                                div.innerHTML = '<div style="width:36px;height:36px;border-radius:50%;overflow:hidden;margin-right:8px;flex:0 0 36px;"><img src="'+escapeHtml(c.autor_foto)+'" alt="avatar" style="width:100%;height:100%;object-fit:cover;" onerror="this.src=\'assets/img/padrao.jpg\'"></div>'+
                                                    '<div style="flex:1;"><div style="font-size:0.95rem;color:#eaeaea;font-weight:600;">'+escapeHtml(c.autor_nome)+' <small style="color:#9a9a9a;font-weight:400;margin-left:6px;font-size:0.85rem;">'+escapeHtml(new Date(c.data_criacao).toLocaleString('pt-BR', { day: '2-digit', month: '2-digit', hour: '2-digit', minute: '2-digit' }))+'</small></div><div style="color:#d1d1d1;font-size:0.95rem;">'+escapeHtml(c.conteudo).replace(/\n/g,'<br>')+'</div></div>';
                                                cl.appendChild(div);
                                            });
                                            cl.dataset.loaded = '1';
                                            cl.scrollTop = cl.scrollHeight;
                                        }).catch(function(){ cl.innerHTML = '<div class="text-danger" style="padding:8px">Erro de rede.</div>'; });
                                }
                                var inp = panel.querySelector('.comment-text-input');
                                if (inp) inp.focus();
                            } else {
                                panel.style.display = 'none';
                            }
                        });
                    });

                    // Envio de comentário (delegado) - busca botões com classe .btn-send-comment
                    document.addEventListener('click', function(e){
                        var target = e.target.closest ? e.target.closest('.btn-send-comment') : null;
                        if (!target) return;
                        e.preventDefault();
                        var postId = target.getAttribute('data-post-id');
                        var panel = document.getElementById('comments-panel-' + postId);
                        if (!panel) return;
                        var input = panel.querySelector('.comment-text-input');
                        if (!input) return;
                        var text = input.value.trim();
                        if (!text) return;
                        var form = new FormData();
                        form.append('conteudo', text);
                        form.append('id_postagem', postId);
                        target.disabled = true;
                        fetch('php/comment.php', { method: 'POST', credentials: 'same-origin', body: form })
                            .then(r => r.json())
                            .then(json => {
                                target.disabled = false;
                                if (!json.success) {
                                    alert('Erro ao enviar comentário');
                                    return;
                                }
                                // Monta bloco do comentário com dados do SESSION_USER
                                var cl = panel.querySelector('.comments-list');
                                var div = document.createElement('div');
                                div.className = 'd-flex align-items-start mb-2';
                                var foto = (SESSION_USER && SESSION_USER.foto) ? SESSION_USER.foto : 'assets/img/padrao.jpg';
                                var nome = (SESSION_USER && SESSION_USER.name) ? SESSION_USER.name : 'Você';
                                var now = new Date();
                                var timeStr = now.toLocaleString('pt-BR', { day: '2-digit', month: '2-digit', hour: '2-digit', minute: '2-digit' });
                                div.innerHTML = '<div style="width:36px;height:36px;border-radius:50%;overflow:hidden;margin-right:8px;flex:0 0 36px;"><img src="' + escapeHtml(foto) + '" alt="avatar" style="width:100%;height:100%;object-fit:cover;" onerror="this.src=\'assets/img/padrao.jpg\'"></div>' +
                                    '<div style="flex:1;">' +
                                    '<div style="font-size:0.95rem;color:#eaeaea;font-weight:600;">' + escapeHtml(nome) + ' <small style="color:#9a9a9a;font-weight:400;margin-left:6px;font-size:0.85rem;">' + escapeHtml(timeStr) + '</small></div>' +
                                    '<div style="color:#d1d1d1;font-size:0.95rem;">' + escapeHtml(text).replace(/\n/g, '<br>') + '</div>' +
                                    '</div>';
                                cl.appendChild(div);
                                // limpar input e rolar para o fim
                                input.value = '';
                                cl.scrollTop = cl.scrollHeight;
                                // Atualiza contador de comentários no card
                                var card = target.closest('.card');
                                if (card) {
                                    var countEl = card.querySelector('.comment-count');
                                    if (countEl) countEl.textContent = parseInt(countEl.textContent || '0') + 1;
                                }
                            }).catch(function(){ target.disabled = false; alert('Erro ao enviar comentário'); });
                    });
                })();
            </script>
        </div>
    </div>
</body>
</html>
