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
            <a href="#" class="flex items-center h-[60px] w-full rounded-lg transition-colors duration-200 text-[#a8a8a8] hover:bg-[#181818] hover:text-white justify-center group nav-item" title="Pesquisar">
                <i data-lucide="search" class="w-7 h-7 stroke-[2]"></i>
                <span class="ml-4 text-lg font-medium sidebar-label">Pesquisar</span>
            </a>

            <!-- Novo Post -->
            <a href="#" class="flex items-center h-[60px] w-full rounded-lg transition-colors duration-200 text-[#a8a8a8] hover:bg-[#181818] hover:text-white justify-center group nav-item" title="Novo Post">
                <i data-lucide="plus-square" class="w-7 h-7 stroke-[2]"></i>
                <span class="ml-4 text-lg font-medium sidebar-label">Novo Post</span>
            </a>

            <!-- Notificações -->
            <a href="#" class="flex items-center h-[60px] w-full rounded-lg transition-colors duration-200 text-[#a8a8a8] hover:bg-[#181818] hover:text-white justify-center group nav-item" title="Notificações">
                <i data-lucide="heart" class="w-7 h-7 stroke-[2]"></i>
                <span class="ml-4 text-lg font-medium sidebar-label">Notificações</span>
            </a>

            <!-- Comunidades -->
            <a href="#" class="flex items-center h-[60px] w-full rounded-lg transition-colors duration-200 text-[#a8a8a8] hover:bg-[#181818] hover:text-white justify-center group nav-item" title="Comunidades">
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
                                        src="../assets_front/img/<?php echo !empty($dados_usuario['foto']) ? $dados_usuario['foto'] : 'padrao.jpg'; ?>" 
                                        alt="Profile Avatar" 
                                        class="w-full h-full object-cover opacity-90 group-hover:opacity-100 transition-opacity"
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
                    <a href="#" class="px-6 py-2.5 text-sm font-bold bg-white text-black rounded-lg hover:bg-gray-200 transition-all shadow-[0_0_15px_rgba(255,255,255,0.1)]">Editar Perfil</a>
                </div>
            </div>

        </main>
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
</body>
</html>