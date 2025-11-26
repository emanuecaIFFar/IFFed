<?php
session_start();
if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ifeed - Notificações</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        body {
            background-color: #000000;
            color: #e5e7eb;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            overflow-x: hidden;
        }
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #000; }
        ::-webkit-scrollbar-thumb { background: #333; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #555; }
        #sidebar { width: 80px; }
        #sidebar.expanded { width: 260px; }
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
        #main-container {
            margin-left: 80px;
            transition: margin-left 0.3s ease-in-out;
        }
        #main-container.expanded-margin { margin-left: 260px; }
    </style>
</head>
<body class="min-h-screen flex flex-col">
    <nav id="sidebar" class="fixed top-0 left-0 h-full bg-black border-r border-[#262626] z-50 flex flex-col justify-between py-5 transition-all duration-300 ease-in-out">
        <div class="flex flex-col w-full px-2 space-y-2">
            <a href="../index.php" class="flex items-center h-[60px] w-full rounded-lg transition-colors duration-200 text-[#a8a8a8] hover:bg-[#181818] hover:text-white justify-center group nav-item" title="Página Inicial">
                <i data-lucide="home" class="w-7 h-7 stroke-[2]"></i>
                <span class="ml-4 text-lg font-medium sidebar-label">Página Inicial</span>
            </a>
            <a href="pesquisar_nseifazrisso.html" class="flex items-center h-[60px] w-full rounded-lg transition-colors duration-200 text-[#a8a8a8] hover:bg-[#181818] hover:text-white justify-center group nav-item" title="Pesquisar">
                <i data-lucide="search" class="w-7 h-7 stroke-[2]"></i>
                <span class="ml-4 text-lg font-medium sidebar-label">Pesquisar</span>
            </a>
            <a href="criar_post.html" class="flex items-center h-[60px] w-full rounded-lg transition-colors duration-200 text-[#a8a8a8] hover:bg-[#181818] hover:text-white justify-center group nav-item" title="Novo Post">
                <i data-lucide="plus-square" class="w-7 h-7 stroke-[2]"></i>
                <span class="ml-4 text-lg font-medium sidebar-label">Novo Post</span>
            </a>
            <a href="notificacoes.php" class="flex items-center h-[60px] w-full rounded-lg transition-colors duration-200 text-white bg-[#202020] justify-center group nav-item" title="Notificações">
                <i data-lucide="heart" class="w-7 h-7 stroke-[2.5]"></i>
                <span class="ml-4 text-lg font-medium sidebar-label">Notificações</span>
            </a>
            <a href="comunidades.html" class="flex items-center h-[60px] w-full rounded-lg transition-colors duration-200 text-[#a8a8a8] hover:bg-[#181818] hover:text-white justify-center group nav-item" title="Comunidades">
                <i data-lucide="users" class="w-7 h-7 stroke-[2]"></i>
                <span class="ml-4 text-lg font-medium sidebar-label">Comunidades</span>
            </a>
            <a href="perfil.php" class="flex items-center h-[60px] w-full rounded-lg transition-colors duration-200 text-[#a8a8a8] hover:bg-[#181818] hover:text-white justify-center group nav-item" title="Perfil">
                <i data-lucide="user-circle-2" class="w-7 h-7 stroke-[2]"></i>
                <span class="ml-4 text-lg font-medium sidebar-label">Perfil</span>
            </a>
        </div>
        <div class="flex flex-col w-full px-2 space-y-2 mb-2">
            <a href="../php/sair.php" class="flex items-center h-[60px] w-full rounded-lg transition-colors duration-200 text-[#a8a8a8] hover:bg-[#181818] hover:text-white justify-center group nav-item" title="Sair">
                <i data-lucide="log-out" class="w-7 h-7 stroke-[2]"></i>
                <span class="ml-4 text-lg font-medium sidebar-label">Sair</span>
            </a>
            <button id="toggleBtn" class="flex items-center justify-center h-[60px] w-full rounded-lg transition-colors duration-200 text-[#a8a8a8] hover:bg-[#181818] hover:text-white outline-none">
                <i data-lucide="menu" class="w-7 h-7 stroke-[2]"></i>
                <span class="ml-4 text-lg font-medium sidebar-label">Recolher</span>
            </button>
        </div>
    </nav>

    <div id="main-container" class="flex-grow flex flex-col min-h-screen">
        <header class="h-[60px] w-full bg-black border-b border-[#262626] flex items-center justify-between px-6 sticky top-0 z-40">
            <div class="w-20"></div>
            <h1 class="text-white text-xl font-semibold tracking-wide">Notificações</h1>
            <div class="w-20 flex justify-end">
                <button id="btnMarkAll" class="text-sm text-[#9ca3af] hover:text-white border border-[#333] px-3 py-1 rounded-full">Marcar tudo como lido</button>
            </div>
        </header>

        <main class="flex-grow flex items-start justify-center p-0 md:p-6">
            <div class="w-full max-w-2xl bg-[#1E1E1E] md:border border-[#333] md:rounded-2xl overflow-hidden shadow-2xl min-h-[80vh]">
                <div class="flex border-b border-[#333]">
                    <button class="flex-1 py-4 text-center text-white font-semibold border-b-2 border-white">Todas</button>
                    <button class="flex-1 py-4 text-center text-gray-500 font-medium hover:text-white hover:bg-[#2a2a2a] transition-colors">Não lidas</button>
                </div>

                <div id="notifications-list" class="flex flex-col">
                    <div class="flex gap-4 p-5 border-b border-[#2a2a2a] bg-[#2a2a2a]/30">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 rounded-full bg-white flex items-center justify-center overflow-hidden border-2 border-white">
                                <span class="text-black font-black text-lg">IF</span>
                            </div>
                        </div>
                        <div class="flex flex-col space-y-1 pr-4">
                            <div class="flex items-center gap-2">
                                <span class="text-white font-bold">IFeed Oficial</span>
                                <i data-lucide="badge-check" class="w-4 h-4 text-blue-400 fill-blue-400/10"></i>
                                <span class="text-gray-500 text-xs">• Agora mesmo</span>
                            </div>
                            <p class="text-gray-300 text-sm leading-relaxed">
                                Tudo calmo por aqui... Interaja com a comunidade para começar a ver suas notificações!
                            </p>
                        </div>
                    </div>
                    <div id="notifications-empty" class="text-center text-gray-400 py-10 text-sm">
                        Carregando notificações...
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        if (window.lucide) lucide.createIcons();
        const sidebar = document.getElementById('sidebar');
        const mainContainer = document.getElementById('main-container');
        const toggleBtn = document.getElementById('toggleBtn');
        const navItems = document.querySelectorAll('.nav-item');
        toggleBtn.addEventListener('click', () => {
            const isExpanded = sidebar.classList.toggle('expanded');
            if (isExpanded) {
                mainContainer.classList.add('expanded-margin');
                navItems.forEach(item => {
                    item.classList.remove('justify-center');
                    item.classList.add('justify-start', 'px-4');
                });
            } else {
                mainContainer.classList.remove('expanded-margin');
                navItems.forEach(item => {
                    item.classList.remove('justify-start', 'px-4');
                    item.classList.add('justify-center');
                });
            }
        });

        const listEl = document.getElementById('notifications-list');
        const emptyEl = document.getElementById('notifications-empty');
        const markAllBtn = document.getElementById('btnMarkAll');

        function resolveAvatar(url) {
            if (!url) return '../assets/img/padrao.jpg';
            if (url.startsWith('http')) return url;
            if (url.startsWith('../')) return url;
            if (url.startsWith('assets_front/')) return '../' + url;
            if (url.startsWith('uploads/')) return '../assets/' + url;
            return '../assets/uploads/' + url;
        }

        function formatDate(str) {
            if (!str) return '';
            const normalized = str.replace(' ', 'T');
            const date = new Date(normalized);
            if (isNaN(date.getTime())) return str;
            return date.toLocaleString('pt-BR', { day: '2-digit', month: '2-digit', hour: '2-digit', minute: '2-digit' });
        }

        function renderNotification(item) {
            const meta = {
                like: { icon: 'heart', color: '#E11D48', text: 'curtiu sua publicação' },
                comment: { icon: 'message-circle', color: '#22C55E', text: 'comentou' },
                follow: { icon: 'user-plus', color: '#3B82F6', text: 'começou a seguir você' },
                default: { icon: 'bell', color: '#F59E0B', text: 'tem uma novidade' }
            };
            const cfg = meta[item.tipo] || meta.default;
            const foto = resolveAvatar(item.origem_foto);
            const nome = item.origem_nome || 'Alguém';
            const tempo = formatDate(item.data_criacao);
            const comentario = item.tipo === 'comment' && item.id_postagem ? ' em sua publicação.' : '';
            const unreadDot = !parseInt(item.lida, 10) ? '<span class="absolute top-5 right-4 w-2.5 h-2.5 bg-blue-500 rounded-full"></span>' : '';
            return `
                <div class="flex gap-4 p-5 border-b border-[#2a2a2a] hover:bg-[#252525] transition-colors cursor-pointer relative">
                    ${unreadDot}
                    <div class="flex-shrink-0 relative">
                        <img src="${foto}" class="w-12 h-12 rounded-full object-cover border border-[#333]" alt="avatar" onerror="this.src='../assets/img/padrao.jpg'">
                        <div class="absolute -bottom-1 -right-1 rounded-full p-1 border-2 border-[#1E1E1E]" style="background:${cfg.color};">
                            <i data-lucide="${cfg.icon}" class="w-3 h-3 text-white"></i>
                        </div>
                    </div>
                    <div class="flex flex-col space-y-1 pr-4">
                        <div class="flex items-center gap-2">
                            <span class="text-white font-bold">${nome}</span>
                            <span class="text-gray-500 text-xs">• ${tempo}</span>
                        </div>
                        <p class="text-gray-400 text-sm">${cfg.text}${comentario}</p>
                    </div>
                </div>
            `;
        }

        function fetchNotifications() {
            emptyEl.textContent = 'Carregando notificações...';
            fetch('../php/api.php?action=get_notifications', { credentials: 'same-origin' })
                .then(r => r.json())
                .then(json => {
                    if (!json.success) throw new Error(json.error || 'Erro ao carregar');
                    const data = json.data || [];
                    if (data.length === 0) {
                        emptyEl.textContent = 'Sem notificações ainda. Quando alguém interagir com você, aparecerá aqui!';
                        return;
                    }
                    emptyEl.remove();
                    const html = data.map(renderNotification).join('');
                    listEl.insertAdjacentHTML('beforeend', html);
                    if (window.lucide) lucide.createIcons();
                })
                .catch(err => {
                    emptyEl.textContent = 'Não foi possível carregar as notificações: ' + err.message;
                });
        }

        function markAllRead() {
            markAllBtn.disabled = true;
            fetch('../php/api.php?action=mark_notifications_read', {
                method: 'POST',
                credentials: 'same-origin',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'all=1'
            })
            .then(r => r.json())
            .then(() => {
                window.location.reload();
            })
            .catch(() => {
                markAllBtn.disabled = false;
                alert('Erro ao marcar notificações.');
            });
        }

        markAllBtn.addEventListener('click', markAllRead);
        fetchNotifications();
    </script>
</body>
</html>
