<?php
// ITEM 5 DA LOUSA: Segurança
session_start();

// Se NÃO existir a variável 'nome_usuario' na sessão...
if(!isset($_SESSION['nome_usuario'])) {
    // ...expulsa o usuário para o login
    header("Location: login.php?erro=acesso_negado");
    exit; // Mata o código aqui
}

include('../php/conexao.php'); // Importante conectar

// Buscar a foto e a bio do usuário logado
$id_usuario = $_SESSION['id'];
$sql = "SELECT * FROM perfil WHERE id = $id_usuario";
$resultado = $conn->query($sql);
$dados_usuario = $resultado->fetch_assoc();

// Normaliza caminho da foto para uso nas views
$foto_val = $dados_usuario['foto'] ?? '';
       if (empty($foto_val)) {
           $foto_path = '../assets/img/padrao.jpg';
} elseif (strpos($foto_val, 'uploads/') === 0) {
    $foto_path = '../assets/' . $foto_val; // já armazena 'uploads/nome.jpg'
} elseif (strpos($foto_val, 'assets_front') !== false || strpos($foto_val, 'http') === 0) {
    $foto_path = $foto_val;
} else {
    $foto_path = '../assets/uploads/' . $foto_val; // só o nome do arquivo
}

?>


<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ifeed - Meu Perfil</title>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Lucide Icons (Script para renderizar ícones SVG) -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <style>
        /* Configurações Globais */
        body { 
            background-color: #000000; 
            color: #e5e7eb; 
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            overflow-x: hidden; /* Evita scroll horizontal na animação */
        }
        
        /* Custom Scrollbar */
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #000; }
        ::-webkit-scrollbar-thumb { background: #333; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #555; }

        /* Sidebar Transitions */
        #sidebar {
            width: 80px; /* Largura inicial (fechada) */
        }
        #sidebar.expanded {
            width: 260px; /* Largura expandida */
        }

        /* Labels da Sidebar (Texto) */
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

        /* Ajuste do Conteúdo Principal */
        #main-container {
            margin-left: 80px; /* Deve bater com a largura inicial da sidebar */
            transition: margin-left 0.3s ease-in-out;
        }
        #main-container.expanded-margin {
            margin-left: 260px;
        }
    </style>
</head>
<body class="min-h-screen flex flex-col">

    <!-- ================= SIDEBAR ================= -->
    <nav id="sidebar" class="fixed top-0 left-0 h-full bg-black border-r border-[#262626] z-50 flex flex-col justify-between py-5 transition-all duration-300 ease-in-out">
        
        <!-- Navegação Superior -->
        <div class="flex flex-col w-full px-2 space-y-2">
            
            <!-- Início -->
            <a href="../index.php" class="flex items-center h-[60px] w-full rounded-lg transition-colors duration-200 text-[#a8a8a8] hover:bg-[#181818] hover:text-white justify-center group nav-item" title="Página Inicial">
                <i data-lucide="home" class="w-7 h-7 stroke-[2]"></i>
                <span class="ml-4 text-lg font-medium sidebar-label">Página Inicial</span>
            </a>

            <!-- Pesquisar -->
            <a href="pesquisar_nseifazrisso.html" class="flex items-center h-[60px] w-full rounded-lg transition-colors duration-200 text-[#a8a8a8] hover:bg-[#181818] hover:text-white justify-center group nav-item" title="Pesquisar">
                <i data-lucide="search" class="w-7 h-7 stroke-[2]"></i>
                <span class="ml-4 text-lg font-medium sidebar-label">Pesquisar</span>
            </a>

            <!-- Novo Post -->
            <a href="criar_post.html" class="flex items-center h-[60px] w-full rounded-lg transition-colors duration-200 text-[#a8a8a8] hover:bg-[#181818] hover:text-white justify-center group nav-item" title="Novo Post">
                <i data-lucide="plus-square" class="w-7 h-7 stroke-[2]"></i>
                <span class="ml-4 text-lg font-medium sidebar-label">Novo Post</span>
            </a>

            <!-- Notificações -->
            <a href="notificacoes.php" class="flex items-center h-[60px] w-full rounded-lg transition-colors duration-200 text-[#a8a8a8] hover:bg-[#181818] hover:text-white justify-center group nav-item" title="Notificações">
                <i data-lucide="heart" class="w-7 h-7 stroke-[2]"></i>
                <span class="ml-4 text-lg font-medium sidebar-label">Notificações</span>
            </a>

            <!-- Comunidades -->
            <a href="comunidades.html" class="flex items-center h-[60px] w-full rounded-lg transition-colors duration-200 text-[#a8a8a8] hover:bg-[#181818] hover:text-white justify-center group nav-item" title="Comunidades">
                <i data-lucide="users" class="w-7 h-7 stroke-[2]"></i>
                <span class="ml-4 text-lg font-medium sidebar-label">Comunidades</span>
            </a>

            <!-- Perfil (ATIVO) -->
            <a href="perfil.php" class="flex items-center h-[60px] w-full rounded-lg transition-colors duration-200 text-white bg-[#202020] justify-center group nav-item" title="Perfil">
                <i data-lucide="user-circle-2" class="w-7 h-7 stroke-[2.5]"></i>
                <span class="ml-4 text-lg font-medium sidebar-label">Perfil</span>
            </a>
        </div>

        <!-- Ações Inferiores -->
        <div class="flex flex-col w-full px-2 space-y-2 mb-2">
            <!-- Sair -->
            <a href="../php/sair.php" class="flex items-center h-[60px] w-full rounded-lg transition-colors duration-200 text-[#a8a8a8] hover:bg-[#181818] hover:text-white justify-center group nav-item" title="Sair">
                <i data-lucide="log-out" class="w-7 h-7 stroke-[2]"></i>
                <span class="ml-4 text-lg font-medium sidebar-label">Sair</span>
            </a>

            <!-- Toggle / Menu -->
            <button id="toggleBtn" class="flex items-center justify-center h-[60px] w-full rounded-lg transition-colors duration-200 text-[#a8a8a8] hover:bg-[#181818] hover:text-white outline-none">
                <i data-lucide="menu" class="w-7 h-7 stroke-[2]"></i>
                <span class="ml-4 text-lg font-medium sidebar-label">Recolher</span>
            </button>
        </div>
    </nav>

    <!-- ================= MAIN CONTENT ================= -->
    <div id="main-container" class="flex-grow flex flex-col min-h-screen">
        
        <!-- HEADER -->
        <header class="h-[60px] w-full bg-black border-b border-[#262626] flex items-center justify-between px-6 sticky top-0 z-40">
            <!-- Spacer Esquerdo -->
            <div class="w-20"></div>
            
            <!-- Título Centralizado -->
            <h1 class="text-white text-xl font-semibold tracking-wide">
                Perfil
            </h1>

            <!-- Spacer Direito (para manter o título no centro) -->
            <div class="w-20"></div>
        </header>

        <!-- ÁREA DO PERFIL -->
        <main class="flex-grow p-6 md:p-10 flex items-start justify-center">
            
            <!-- Card de Perfil -->
            <div class="w-full max-w-4xl bg-[#1E1E1E] border border-[#333] rounded-2xl overflow-hidden shadow-2xl mt-4">
                
                <!-- Header do Card -->
                <div class="flex justify-between items-center p-6 border-b border-[#333]">
                    <h2 class="text-xl font-semibold text-white">Meu Perfil</h2>
                    <div class="px-3 py-1 bg-white/10 rounded-full border border-white/5">
                        <span class="text-xs font-medium text-gray-300 uppercase tracking-wider">
                            Visualização Pública
                        </span>
                    </div>
                </div>
        
                <!-- Corpo do Card -->
                <div class="p-8">
                    <div class="flex flex-col md:flex-row gap-10">
                    
                        <!-- Esquerda: Foto & ID -->
                        <div class="flex flex-col items-center md:items-start space-y-6 md:w-1/3 shrink-0">
                            <div class="relative group">
                                <div class="w-48 h-48 rounded-full overflow-hidden border-4 border-[#2a2a2a] shadow-lg bg-black">
                                    <img 
                                        src="<?php echo $foto_path; ?>" 
                                        alt="Profile Avatar" 
                                        class="w-full h-full object-cover opacity-90 group-hover:opacity-100 transition-opacity"
                                        onerror="this.onerror=null;this.src='../assets/img/padrao.jpg';"
                                    />
                                </div>
                                <button 
                                    class="absolute bottom-2 right-4 bg-[#333] hover:bg-[#444] text-white p-2.5 rounded-full border border-black shadow-md transition-colors cursor-pointer"
                                    title="Alterar foto"
                                >
                                    <i data-lucide="camera" class="w-5 h-5"></i>
                                </button>
                            </div>
                            
                            <div class="text-center md:text-left w-full pl-2">
                                <p class="text-gray-500 text-sm font-medium mb-1">IDENTIFICAÇÃO</p>
                                <p class="text-gray-300 text-lg font-mono">ID: @<?php echo htmlspecialchars($dados_usuario['id'] ?: '0'); ?></p>
                            </div>
                        </div>
            
                        <!-- Direita: Dados -->
                        <div class="flex-grow flex flex-col space-y-6 w-full">
                            
                            <!-- Nome -->
                            <div class="space-y-2">
                                <label class="flex items-center text-xs font-bold text-gray-500 uppercase tracking-widest gap-2">
                                    <i data-lucide="user" class="w-3.5 h-3.5"></i>
                                    Nome Completo
                                </label>
                                <div class="bg-black/40 border border-[#333] rounded-lg px-4 py-3 text-white text-lg font-medium">
                                    <?php echo htmlspecialchars(!empty($dados_usuario['nome']) ? $dados_usuario['nome'] : $_SESSION['nome_usuario']); ?>
                                </div>
                            </div>
            
                            <!-- Email -->
                            <div class="space-y-2">
                                <label class="flex items-center text-xs font-bold text-gray-500 uppercase tracking-widest gap-2">
                                    <i data-lucide="mail" class="w-3.5 h-3.5"></i>
                                    E-mail
                                </label>
                                <div class="bg-black/40 border border-[#333] rounded-lg px-4 py-3 text-white font-mono tracking-wide">
                                    <?php echo htmlspecialchars(!empty($dados_usuario['email']) ? $dados_usuario['email'] : 'Não informado'); ?>
                                </div>
                            </div>
            
                            <!-- Data de Nascimento -->
                            <div class="space-y-2">
                                <label class="flex items-center text-xs font-bold text-gray-500 uppercase tracking-widest gap-2">
                                    <i data-lucide="calendar" class="w-3.5 h-3.5"></i>
                                    Data de Nascimento
                                </label>
                                <div class="bg-black/40 border border-[#333] rounded-lg px-4 py-3 text-white">
                                    <?php echo !empty($dados_usuario['data_nasc']) ? date('d/m/Y', strtotime($dados_usuario['data_nasc'])) : 'Não informado'; ?>
                                </div>
                            </div>
            
                            <!-- Biografia -->
                            <div class="space-y-2">
                                <label class="flex items-center text-xs font-bold text-gray-500 uppercase tracking-widest gap-2">
                                    <i data-lucide="file-text" class="w-3.5 h-3.5"></i>
                                    Biografia
                                </label>
                                <div class="bg-black/40 border border-[#333] rounded-lg px-4 py-3 text-gray-300 leading-relaxed min-h-[120px]">
                                    <?php echo !empty($dados_usuario['bio']) ? nl2br(htmlspecialchars($dados_usuario['bio'])) : 'Ainda não cadastrou uma bio.'; ?>
                                </div>
                            </div>
            
                        </div>
                    </div>
                </div>
        
                <!-- Rodapé do Card -->
                <div class="border-t border-[#333] p-6 bg-[#1c1c1c] flex justify-end items-center gap-4">
                    <a href="../index.php" class="px-6 py-2.5 text-sm font-medium text-gray-400 hover:text-white transition-colors">Cancelar</a>
                    <a href="#" id="editarPerfilBtn" class="px-6 py-2.5 text-sm font-bold bg-white text-black rounded-lg hover:bg-gray-200 transition-all shadow-[0_0_15px_rgba(255,255,255,0.1)]">Editar Perfil</a>
                </div>
            </div>

        </main>
    </div>

        <!-- Lista de postagens do usuário logado -->
        <div class="p-6 md:p-10">
            <div class="w-full max-w-4xl mx-auto">
                <h3 class="text-lg font-semibold text-white mb-4">Suas Publicações</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <?php
                    // Busca posts do usuário logado
                    $stmt = $conn->prepare('SELECT id, conteudo_textual, imagem, data_criacao, num_curtidas, num_comentarios FROM postagens WHERE id_usuario = ? ORDER BY data_criacao DESC');
                    $stmt->bind_param('i', $id_usuario);
                    $stmt->execute();
                    $res2 = $stmt->get_result();
                    while($p = $res2->fetch_assoc()) {
                        $p_conteudo = nl2br(htmlspecialchars($p['conteudo_textual']));
                        $p_img = $p['imagem'] ? '../assets/uploads/' . $p['imagem'] : null;
                        $p_data = date('d/m/Y H:i', strtotime($p['data_criacao']));
                    ?>
                    <div class="bg-[#1E1E1E] border border-[#333] rounded-lg p-3">
                        <div class="flex items-start gap-3">
                            <div style="width:44px; height:44px; overflow:hidden; border-radius:50%;">
                                <img src="<?php echo $foto_path; ?>" alt="Avatar" style="width:100%; height:100%; object-fit:cover;" onerror="this.onerror=null;this.src='../assets/img/padrao.jpg';">
                            </div>
                            <div class="flex-grow">
                                <div class="text-white font-semibold"><?php echo htmlspecialchars($dados_usuario['nome'] ?: $_SESSION['nome_usuario']); ?></div>
                                <small class="text-[#888]"><?php echo $p_data; ?></small>
                                <div class="mt-3 text-gray-300"><?php echo $p_conteudo ?: '<em>—</em>'; ?></div>
                                <?php if ($p_img): ?>
                                    <div class="mt-3">
                                        <img src="<?php echo $p_img; ?>" alt="Imagem" style="max-width:100%; height:auto;" onerror="this.style.display='none';">
                                    </div>
                                <?php endif; ?>
                                <div class="mt-3 flex items-center justify-between">
                                    <div class="text-gray-400">
                                        <i class="bi bi-heart"></i> <span class="ms-1"><?php echo intval($p['num_curtidas']); ?></span>
                                        <i class="bi bi-chat ms-3"></i> <span class="ms-1"><?php echo intval($p['num_comentarios']); ?></span>
                                    </div>
                                    <div>
                                        <form method="POST" action="../php/delete_post.php" onsubmit="return confirm('Apagar esta publicação?');">
                                            <input type="hidden" name="post_id" value="<?php echo intval($p['id']); ?>">
                                            <button type="submit" class="px-3 py-1 bg-red-600 text-white rounded-md text-sm">Apagar</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php } 
                    $stmt->close(); ?>
                </div>
            </div>
        </div>

    <!-- JAVASCRIPT -->
    <script>
        // 1. Inicializar Ícones Lucide
        if (window.lucide) lucide.createIcons();

        // 2. Lógica da Sidebar (Expandir/Recolher)
        const sidebar = document.getElementById('sidebar');
        const mainContainer = document.getElementById('main-container');
        const toggleBtn = document.getElementById('toggleBtn');
        const navItems = document.querySelectorAll('.nav-item');

        if (toggleBtn) {
            toggleBtn.addEventListener('click', () => {
                // Alterna classe 'expanded' na sidebar
                const isExpanded = sidebar.classList.toggle('expanded');
                
                // Ajusta a margem do container principal
                if (isExpanded) {
                    mainContainer.classList.add('expanded-margin');
                    
                    // Ajusta os itens de menu para alinhamento à esquerda
                    navItems.forEach(item => {
                        item.classList.remove('justify-center');
                        item.classList.add('justify-start', 'px-4');
                    });
                } else {
                    mainContainer.classList.remove('expanded-margin');
                    
                    // Retorna os itens de menu para o centro
                    navItems.forEach(item => {
                        item.classList.remove('justify-start', 'px-4');
                        item.classList.add('justify-center');
                    });
                }
            });
        }
    </script>
    <style>
        /* Balão de 'Em desenvolvimento' */
        .dev-bubble {
            position: fixed;
            top: 84px;
            right: 24px;
            background: rgba(255,255,255,0.06);
            color: #fff;
            padding: 10px 14px;
            border-radius: 10px;
            box-shadow: 0 8px 30px rgba(0,0,0,0.6);
            font-size: 0.95rem;
            opacity: 0;
            transform: translateY(-6px);
            transition: opacity 220ms ease, transform 220ms ease;
            pointer-events: none;
            z-index: 9999;
        }
        .dev-bubble.show {
            opacity: 1;
            transform: translateY(0);
        }
    </style>

    <script>
        // Mostrar balão "Em desenvolvimento" ao clicar em Editar Perfil
        (function(){
            var btn = document.getElementById('editarPerfilBtn');
            if(!btn) return;
            btn.addEventListener('click', function(e){
                e.preventDefault();
                var bubble = document.getElementById('devBubble');
                if(!bubble){
                    bubble = document.createElement('div');
                    bubble.id = 'devBubble';
                    bubble.className = 'dev-bubble';
                    bubble.textContent = 'Em desenvolvimento...';
                    document.body.appendChild(bubble);
                }
                bubble.classList.add('show');
                if(window._devBubbleTimeout) clearTimeout(window._devBubbleTimeout);
                window._devBubbleTimeout = setTimeout(function(){
                    bubble.classList.remove('show');
                }, 2300);
            });
        })();
    </script>
</body>
</html>