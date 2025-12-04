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
if (empty($foto_val) || $foto_val === 'padrao.jpg' || $foto_val === 'img/padrao.jpg' || $foto_val === 'img/padrao.svg') {
    // Foto padrão (avatar genérico)
    $foto_path = '../assets/img/padrao.svg';
} elseif (strpos($foto_val, 'uploads/') === 0) {
    $foto_path = '../assets/' . $foto_val; // já armazena 'uploads/nome.jpg'
} elseif (strpos($foto_val, 'img/') === 0) {
    $foto_path = '../assets/' . $foto_val; // outras imagens em img/
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
            <a href="pesquisar_nseifazrisso.php" class="flex items-center h-[60px] w-full rounded-lg transition-colors duration-200 text-[#a8a8a8] hover:bg-[#181818] hover:text-white justify-center group nav-item" title="Pesquisar">
                <i data-lucide="search" class="w-7 h-7 stroke-[2]"></i>
                <span class="ml-4 text-lg font-medium sidebar-label">Pesquisar</span>
            </a>

            <!-- Novo Post -->
            <a href="criar_post.php" class="flex items-center h-[60px] w-full rounded-lg transition-colors duration-200 text-[#a8a8a8] hover:bg-[#181818] hover:text-white justify-center group nav-item" title="Novo Post">
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
                                        src="<?php echo $foto_path; ?>" 
                                        alt="Profile Avatar" 
                                        class="w-full h-full object-cover opacity-90 group-hover:opacity-100 transition-opacity"
                                        onerror="this.onerror=null;this.src='../assets/img/padrao.svg';"
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
                    <button type="button" id="editarPerfilBtn" class="px-6 py-2.5 text-sm font-bold bg-white text-black rounded-lg hover:bg-gray-200 transition-all shadow-[0_0_15px_rgba(255,255,255,0.1)]">Editar Perfil</button>
                </div>
            </div>

        </main>
    </div>

    <!-- ================= MODAL DE EDIÇÃO ================= -->
    <div id="modalEditar" class="fixed inset-0 bg-black/80 z-[100] hidden items-center justify-center p-4">
        <div class="bg-[#1E1E1E] border border-[#333] rounded-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
            <!-- Header do Modal -->
            <div class="flex justify-between items-center p-5 border-b border-[#333] sticky top-0 bg-[#1E1E1E] z-10">
                <h2 class="text-lg font-semibold text-white">Editar Perfil</h2>
                <button type="button" id="fecharModal" class="text-gray-400 hover:text-white transition-colors">
                    <i data-lucide="x" class="w-6 h-6"></i>
                </button>
            </div>
            
            <!-- Formulário -->
            <form action="../php/atualizar_perfil.php" method="POST" enctype="multipart/form-data" class="p-5 space-y-5">
                
                <!-- Foto de Perfil -->
                <div class="flex flex-col items-center space-y-3">
                    <div class="relative group">
                        <div class="w-28 h-28 rounded-full overflow-hidden border-2 border-[#333] bg-black">
                            <img id="previewFoto" src="<?php echo $foto_path; ?>" alt="Avatar" class="w-full h-full object-cover" onerror="this.src='../assets/img/padrao.svg';">
                        </div>
                        <label for="inputFoto" class="absolute bottom-0 right-0 bg-[#333] hover:bg-[#444] text-white p-2 rounded-full border border-black cursor-pointer transition-colors">
                            <i data-lucide="camera" class="w-4 h-4"></i>
                        </label>
                    </div>
                    <input type="file" id="inputFoto" name="foto" accept="image/jpeg,image/png,image/gif,image/webp" class="hidden">
                    <p class="text-xs text-gray-500">JPG, PNG, GIF ou WebP (máx. 5MB)</p>
                </div>

                <!-- Nome -->
                <div class="space-y-2">
                    <label for="inputNome" class="flex items-center text-xs font-bold text-gray-500 uppercase tracking-widest gap-2">
                        <i data-lucide="user" class="w-3.5 h-3.5"></i>
                        Nome Completo
                    </label>
                    <input 
                        type="text" 
                        id="inputNome" 
                        name="nome" 
                        value="<?php echo htmlspecialchars($dados_usuario['nome'] ?? ''); ?>"
                        required
                        class="w-full bg-black/40 border border-[#333] rounded-lg px-4 py-3 text-white focus:outline-none focus:border-[#555] transition-colors"
                        placeholder="Seu nome completo"
                    >
                </div>

                <!-- Email -->
                <div class="space-y-2">
                    <label for="inputEmail" class="flex items-center text-xs font-bold text-gray-500 uppercase tracking-widest gap-2">
                        <i data-lucide="mail" class="w-3.5 h-3.5"></i>
                        E-mail
                    </label>
                    <input 
                        type="email" 
                        id="inputEmail" 
                        name="email" 
                        value="<?php echo htmlspecialchars($dados_usuario['email'] ?? ''); ?>"
                        required
                        class="w-full bg-black/40 border border-[#333] rounded-lg px-4 py-3 text-white focus:outline-none focus:border-[#555] transition-colors"
                        placeholder="seu@email.com"
                    >
                </div>

                <!-- Data de Nascimento -->
                <div class="space-y-2">
                    <label for="inputDataNasc" class="flex items-center text-xs font-bold text-gray-500 uppercase tracking-widest gap-2">
                        <i data-lucide="calendar" class="w-3.5 h-3.5"></i>
                        Data de Nascimento
                    </label>
                    <input 
                        type="date" 
                        id="inputDataNasc" 
                        name="data_nasc" 
                        value="<?php echo htmlspecialchars($dados_usuario['data_nasc'] ?? ''); ?>"
                        class="w-full bg-black/40 border border-[#333] rounded-lg px-4 py-3 text-white focus:outline-none focus:border-[#555] transition-colors"
                    >
                </div>

                <!-- Biografia -->
                <div class="space-y-2">
                    <label for="inputBio" class="flex items-center text-xs font-bold text-gray-500 uppercase tracking-widest gap-2">
                        <i data-lucide="file-text" class="w-3.5 h-3.5"></i>
                        Biografia
                    </label>
                    <textarea 
                        id="inputBio" 
                        name="bio" 
                        rows="4"
                        class="w-full bg-black/40 border border-[#333] rounded-lg px-4 py-3 text-white focus:outline-none focus:border-[#555] transition-colors resize-none"
                        placeholder="Conte um pouco sobre você..."
                    ><?php echo htmlspecialchars($dados_usuario['bio'] ?? ''); ?></textarea>
                </div>

                <!-- Botões -->
                <div class="flex gap-3 pt-2">
                    <button type="button" id="cancelarModal" class="flex-1 px-4 py-3 text-sm font-medium text-gray-400 hover:text-white border border-[#333] rounded-lg transition-colors">
                        Cancelar
                    </button>
                    <button type="submit" class="flex-1 px-4 py-3 text-sm font-bold bg-white text-black rounded-lg hover:bg-gray-200 transition-colors">
                        Salvar Alterações
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Mensagens de Feedback -->
    <?php if (isset($_GET['sucesso'])): ?>
    <div id="msgSucesso" class="fixed top-20 right-6 bg-green-600 text-white px-5 py-3 rounded-lg shadow-lg z-[200] animate-pulse">
        ✓ Perfil atualizado com sucesso!
    </div>
    <script>setTimeout(() => document.getElementById('msgSucesso')?.remove(), 3000);</script>
    <?php endif; ?>

    <?php if (isset($_GET['erro'])): ?>
    <div id="msgErro" class="fixed top-20 right-6 bg-red-600 text-white px-5 py-3 rounded-lg shadow-lg z-[200]">
        <?php 
        $erros = [
            'campos_obrigatorios' => '⚠ Nome e email são obrigatórios',
            'email_invalido' => '⚠ Formato de email inválido',
            'email_existente' => '⚠ Este email já está em uso',
            'arquivo_invalido' => '⚠ Arquivo enviado não é uma imagem válida',
            'tipo_nao_permitido' => '⚠ Tipo de imagem não permitido',
            'arquivo_grande' => '⚠ Imagem muito grande (máx. 5MB)',
            'falha_upload' => '⚠ Erro ao salvar imagem',
            'falha_banco' => '⚠ Erro ao atualizar perfil'
        ];
        echo $erros[$_GET['erro']] ?? '⚠ Ocorreu um erro';
        ?>
    </div>
    <script>setTimeout(() => document.getElementById('msgErro')?.remove(), 4000);</script>
    <?php endif; ?>

        <!-- Lista de postagens do usuário logado -->
        <div class="p-6 md:p-10">
            <div class="w-full max-w-4xl mx-auto">
                <h3 class="text-lg font-semibold text-white mb-4">Suas Publicações</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <?php
                    // Busca posts do usuário logado
                    // Atualizado para verificar se o próprio usuário curtiu seus posts (para pintar o coração)
                    $sql_posts = "SELECT p.id, p.conteudo_textual, p.imagem, p.data_criacao, p.num_curtidas, p.num_comentarios,
                                  EXISTS(SELECT 1 FROM curtidas c WHERE c.id_postagem = p.id AND c.id_usuario = ?) AS liked
                                  FROM postagens p 
                                  WHERE p.id_usuario = ? 
                                  ORDER BY p.data_criacao DESC";
                    
                    $stmt = $conn->prepare($sql_posts);
                    $stmt->bind_param('ii', $id_usuario, $id_usuario);
                    $stmt->execute();
                    $res2 = $stmt->get_result();
                    while($p = $res2->fetch_assoc()) {
                        $p_conteudo = nl2br(htmlspecialchars($p['conteudo_textual']));
                        $p_img = $p['imagem'] ? '../assets/uploads/' . $p['imagem'] : null;
                        $p_data = date('d/m/Y H:i', strtotime($p['data_criacao']));
                        $liked = !empty($p['liked']);
                    ?>
                    <div class="bg-[#1E1E1E] border border-[#333] rounded-lg p-3">
                        <div class="flex items-start gap-3">
                            <div style="width:44px; height:44px; overflow:hidden; border-radius:50%;">
                                <img src="<?php echo $foto_path; ?>" alt="Avatar" style="width:100%; height:100%; object-fit:cover;" onerror="this.onerror=null;this.src='../assets/img/padrao.svg';">
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
                                <div class="mt-3 flex flex-col gap-3">
                                    <div class="flex items-center justify-between text-gray-400">
                                        <div class="flex items-center gap-4">
                                            <!-- Botão de Like (Formulário) -->
                                            <form method="POST" action="../php/like.php" class="flex items-center">
                                                <input type="hidden" name="post_id" value="<?php echo intval($p['id']); ?>">
                                                <!-- Redireciona para a mesma página, na âncora do post -->
                                                <input type="hidden" name="redirect_to" value="../pages/perfil.php#post-<?php echo $p['id']; ?>">
                                                <button type="submit" class="flex items-center hover:text-white transition-colors group" title="<?php echo $liked ? 'Descurtir' : 'Curtir'; ?>">
                                                    <i class="bi <?php echo $liked ? 'bi-heart-fill text-red-500' : 'bi-heart group-hover:text-red-500'; ?>"></i>
                                                    <span class="ms-1 <?php echo $liked ? 'text-red-500' : ''; ?>"><?php echo intval($p['num_curtidas']); ?></span>
                                                </button>
                                            </form>

                                            <a href="?open_comments=<?php echo $p['id']; ?>#post-<?php echo $p['id']; ?>" class="flex items-center hover:text-white transition-colors">
                                                <i class="bi bi-chat"></i> <span class="ms-1"><?php echo intval($p['num_comentarios']); ?></span>
                                            </a>
                                        </div>
                                        <form method="POST" action="../php/delete_post.php" onsubmit="return confirm('Apagar esta publicação?');">
                                            <input type="hidden" name="post_id" value="<?php echo intval($p['id']); ?>">
                                            <button type="submit" class="px-3 py-1 bg-red-600 text-white rounded-md text-sm hover:bg-red-700 transition-colors">Apagar</button>
                                        </form>
                                    </div>

                                    <!-- Área de Comentários (Server-Side) -->
                                    <?php 
                                    $open_comments = isset($_GET['open_comments']) ? intval($_GET['open_comments']) : 0;
                                    if ($open_comments === intval($p['id'])): 
                                    ?>
                                    <div id="post-<?php echo $p['id']; ?>" class="bg-[#111] rounded-lg p-3 mt-2 border border-[#333]">
                                        <h4 class="text-sm font-bold text-gray-400 mb-3">Comentários</h4>
                                        <div class="space-y-3 max-h-60 overflow-y-auto pr-2 mb-3 custom-scrollbar">
                                            <?php
                                            $sql_comentarios = "SELECT c.conteudo, c.data_criacao, u.nome, u.foto 
                                                                FROM comentarios c 
                                                                JOIN perfil u ON c.id_usuario = u.id 
                                                                WHERE c.id_postagem = ? 
                                                                ORDER BY c.data_criacao ASC";
                                            $stmtC = $conn->prepare($sql_comentarios);
                                            if ($stmtC) {
                                                $pid = $p['id'];
                                                $stmtC->bind_param('i', $pid);
                                                $stmtC->execute();
                                                $stmtC->bind_result($c_conteudo, $c_data, $c_nome, $c_foto);
                                                $has_comments = false;
                                                while ($stmtC->fetch()) {
                                                    $has_comments = true;
                                                    // Tratamento da foto do comentarista
                                                    $c_foto_path = '../assets/img/padrao.svg';
                                                    if (!empty($c_foto)) {
                                                        if (strpos($c_foto, 'uploads/') === 0) $c_foto_path = '../assets/' . $c_foto;
                                                        elseif (strpos($c_foto, 'assets_front') !== false || strpos($c_foto, 'http') === 0) $c_foto_path = $c_foto;
                                                        else $c_foto_path = '../assets/uploads/' . $c_foto;
                                                    }
                                            ?>
                                                <div class="flex gap-3 items-start">
                                                    <div class="w-8 h-8 rounded-full overflow-hidden shrink-0 border border-[#333]">
                                                        <img src="<?php echo $c_foto_path; ?>" class="w-full h-full object-cover" onerror="this.src='../assets/img/padrao.svg'">
                                                    </div>
                                                    <div class="flex-grow">
                                                        <div class="flex items-baseline gap-2">
                                                            <span class="text-sm font-semibold text-white"><?php echo htmlspecialchars($c_nome); ?></span>
                                                            <span class="text-xs text-gray-500"><?php echo date('d/m H:i', strtotime($c_data)); ?></span>
                                                        </div>
                                                        <p class="text-sm text-gray-300 mt-0.5"><?php echo nl2br(htmlspecialchars($c_conteudo)); ?></p>
                                                    </div>
                                                </div>
                                            <?php 
                                                }
                                                if (!$has_comments) {
                                                    echo '<p class="text-xs text-gray-500 italic">Nenhum comentário ainda.</p>';
                                                }
                                                $stmtC->close();
                                            }
                                            ?>
                                        </div>
                                        
                                        <!-- Formulário de Comentário -->
                                        <form action="../php/comment.php" method="POST" class="flex gap-2">
                                            <input type="hidden" name="id_postagem" value="<?php echo $p['id']; ?>">
                                            <input type="hidden" name="redirect_to" value="../pages/perfil.php?open_comments=<?php echo $p['id']; ?>#post-<?php echo $p['id']; ?>">
                                            <input type="text" name="conteudo" placeholder="Escreva um comentário..." class="flex-grow bg-[#222] border border-[#333] rounded px-3 py-1.5 text-sm text-white focus:outline-none focus:border-[#555]" required>
                                            <button type="submit" class="px-3 py-1.5 bg-white text-black text-sm font-bold rounded hover:bg-gray-200 transition-colors">Enviar</button>
                                        </form>
                                    </div>
                                    <?php endif; ?>
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

        // 3. Lógica do Modal de Edição
        const modalEditar = document.getElementById('modalEditar');
        const editarPerfilBtn = document.getElementById('editarPerfilBtn');
        const fecharModal = document.getElementById('fecharModal');
        const cancelarModal = document.getElementById('cancelarModal');
        const inputFoto = document.getElementById('inputFoto');
        const previewFoto = document.getElementById('previewFoto');

        // Abrir modal
        if (editarPerfilBtn && modalEditar) {
            editarPerfilBtn.addEventListener('click', () => {
                modalEditar.classList.remove('hidden');
                modalEditar.classList.add('flex');
                document.body.style.overflow = 'hidden'; // Trava scroll da página
                // Re-renderiza ícones do Lucide no modal
                if (window.lucide) lucide.createIcons();
            });
        }

        // Fechar modal (botão X)
        if (fecharModal) {
            fecharModal.addEventListener('click', () => {
                modalEditar.classList.add('hidden');
                modalEditar.classList.remove('flex');
                document.body.style.overflow = ''; // Libera scroll
            });
        }

        // Fechar modal (botão Cancelar)
        if (cancelarModal) {
            cancelarModal.addEventListener('click', () => {
                modalEditar.classList.add('hidden');
                modalEditar.classList.remove('flex');
                document.body.style.overflow = '';
            });
        }

        // Fechar modal clicando fora
        if (modalEditar) {
            modalEditar.addEventListener('click', (e) => {
                if (e.target === modalEditar) {
                    modalEditar.classList.add('hidden');
                    modalEditar.classList.remove('flex');
                    document.body.style.overflow = '';
                }
            });
        }

        // Preview da foto ao selecionar arquivo
        if (inputFoto && previewFoto) {
            inputFoto.addEventListener('change', (e) => {
                const file = e.target.files[0];
                if (file) {
                    // Validar tipo
                    const tiposPermitidos = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                    if (!tiposPermitidos.includes(file.type)) {
                        alert('Tipo de arquivo não permitido. Use JPG, PNG, GIF ou WebP.');
                        inputFoto.value = '';
                        return;
                    }
                    // Validar tamanho (5MB)
                    if (file.size > 5 * 1024 * 1024) {
                        alert('Arquivo muito grande. Máximo: 5MB');
                        inputFoto.value = '';
                        return;
                    }
                    // Mostrar preview
                    const reader = new FileReader();
                    reader.onload = (ev) => {
                        previewFoto.src = ev.target.result;
                    };
                    reader.readAsDataURL(file);
                }
            });
        }

        // Fechar modal com tecla ESC
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && modalEditar && !modalEditar.classList.contains('hidden')) {
                modalEditar.classList.add('hidden');
                modalEditar.classList.remove('flex');
                document.body.style.overflow = '';
            }
        });
    </script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</body>
</html>