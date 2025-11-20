<?php
// pages/cadastro.php

// Se quiser usar sessão (por exemplo, para mensagem de sucesso/erro), descomente:
// session_start();

// Mostrar mensagens simples via ?status=ok ou ?status=erro
$mensagem_sucesso = '';
$mensagem_erro = '';
if (isset($_GET['status']) && $_GET['status'] == 'ok') {
        $mensagem_sucesso = 'Cadastro realizado com sucesso! Faça login.';
} elseif (isset($_GET['status']) && $_GET['status'] == 'erro') {
        $mensagem_erro = 'Erro ao cadastrar. Tente novamente.';
}
?>

<!DOCTYPE html>
<html lang="pt-BR" class="dark">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Cadastro - Criar Nova Conta</title>
        <!-- Importação do Tailwind CSS via CDN para estilização rápida -->
        <script src="https://cdn.tailwindcss.com"></script>
        <!-- Fonte Inter do Google Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        <style>
            /* Configurações globais de estilo */
            body {
                font-family: 'Inter', sans-serif;
                background-color: #000;
                color: #fff;
                overflow-x: hidden; /* Evita rolagem horizontal indesejada */
            }
      
            /* Customização da barra de rolagem para o tema escuro */
            ::-webkit-scrollbar {
                width: 8px;
            }
            ::-webkit-scrollbar-track {
                background: #000;
            }
            ::-webkit-scrollbar-thumb {
                background: #333;
                border-radius: 4px;
            }
            ::-webkit-scrollbar-thumb:hover {
                background: #555;
            }
      
            /* Correção do ícone de calendário do input date para ficar branco (invertido) */
            ::-webkit-calendar-picker-indicator {
                filter: invert(1);
                opacity: 0.6;
                cursor: pointer;
            }
      
            /* Animação de entrada suave (fade in e slide up) */
            .animate-fade-in-up {
                animation: fadeInUp 0.5s ease-out forwards;
            }

            @keyframes fadeInUp {
                from {
                    opacity: 0;
                    transform: translateY(20px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
        </style>
    <script type="importmap">
{
    "imports": {
        "@google/genai": "https://aistudiocdn.com/@google/genai@^1.30.0",
        "react": "https://aistudiocdn.com/react@^19.2.0",
        "react/": "https://aistudiocdn.com/react@^19.2.0/"
    }
}
</script>
    </head>
    <body class="min-h-screen w-full flex items-center justify-center p-4 relative">

        <!-- Efeitos de Brilho Ambiente (Background Glows) -->
        <!-- Círculo superior esquerdo -->
        <div class="fixed top-[-20%] left-[-10%] w-[500px] h-[500px] bg-neutral-900/30 rounded-full blur-[100px] pointer-events-none z-0"></div>
        <!-- Círculo inferior direito -->
        <div class="fixed bottom-[-20%] right-[-10%] w-[600px] h-[600px] bg-neutral-900/20 rounded-full blur-[120px] pointer-events-none z-0"></div>

        <!-- Card Principal Centralizado -->
        <div class="w-full max-w-md relative z-10 bg-black border border-neutral-800 rounded-2xl p-8 shadow-2xl shadow-black/50 animate-fade-in-up">
            <h2 class="text-2xl font-bold text-center text-white mb-6 tracking-tight">
                Criar Nova Conta
            </h2>

            <?php if(!empty($mensagem_sucesso)): ?>
                <p class="text-center text-green-500 font-semibold mb-4"><?php echo $mensagem_sucesso; ?></p>
            <?php endif; ?>
            <?php if(!empty($mensagem_erro)): ?>
                <p class="text-center text-red-500 font-semibold mb-4"><?php echo $mensagem_erro; ?></p>
            <?php endif; ?>

            <!-- Formulário de Cadastro -->
            <form id="registerForm" class="space-y-4" action="../php/cadastrar.php" method="POST" enctype="multipart/form-data">
        
                <!-- Seção de Upload da Foto de Perfil -->
                <div class="flex flex-col items-center mb-6">
                    <div 
                        id="uploadClickArea"
                        class="w-24 h-24 rounded-full bg-neutral-900 border border-neutral-700 flex items-center justify-center cursor-pointer overflow-hidden hover:border-white transition-colors group relative"
                        title="Clique para adicionar uma foto"
                    >
                        <!-- Ícone SVG (Placeholder) -->
                        <svg id="avatarIcon" xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-neutral-500 group-hover:text-white transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
            
                        <!-- Pré-visualização da Imagem (Oculta por padrão) -->
                        <img id="avatarPreview" src="" alt="Preview" class="w-full h-full object-cover hidden" />

                        <!-- Overlay que aparece ao passar o mouse -->
                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-opacity">
                            <span class="text-xs text-white font-medium">Alterar</span>
                        </div>
                    </div>
                    <!-- Input file oculto -->
                    <input 
                        type="file" 
                        id="fileInput" 
                        name="foto_perfil"
                        class="hidden" 
                        accept="image/*"
                    />
                    <span class="text-xs text-neutral-500 mt-2">Foto de Perfil</span>
                </div>

                <!-- Campo: Nome Completo -->
                <div class="mb-4 w-full">
                    <label class="block text-sm text-neutral-400 mb-2 font-medium">Nome Completo</label>
                    <input type="text" name="nome" placeholder="Seu nome" required
                        class="w-full bg-neutral-900/80 border border-neutral-800 text-white px-4 py-3 rounded-lg focus:outline-none focus:border-white/50 focus:ring-1 focus:ring-white/20 placeholder-neutral-600 transition-all duration-200 ease-in-out"
                    />
                </div>

                <!-- Campo: E-mail -->
                <div class="mb-4 w-full">
                    <label class="block text-sm text-neutral-400 mb-2 font-medium">E-mail</label>
                    <input type="email" name="email" placeholder="exemplo@email.com" required
                        class="w-full bg-neutral-900/80 border border-neutral-800 text-white px-4 py-3 rounded-lg focus:outline-none focus:border-white/50 focus:ring-1 focus:ring-white/20 placeholder-neutral-600 transition-all duration-200 ease-in-out"
                    />
                </div>

                <!-- Grid: Data de Nascimento e Senha (lado a lado em telas maiores) -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="mb-4 w-full">
                        <label class="block text-sm text-neutral-400 mb-2 font-medium">Data de Nascimento</label>
                        <input type="date" name="data_nascimento" required
                            class="w-full bg-neutral-900/80 border border-neutral-800 text-white px-4 py-3 rounded-lg focus:outline-none focus:border-white/50 focus:ring-1 focus:ring-white/20 placeholder-neutral-600 transition-all duration-200 ease-in-out"
                        />
                    </div>
                    <div class="mb-4 w-full">
                        <label class="block text-sm text-neutral-400 mb-2 font-medium">Senha</label>
                        <input type="password" name="senha" placeholder="••••••••" required
                            class="w-full bg-neutral-900/80 border border-neutral-800 text-white px-4 py-3 rounded-lg focus:outline-none focus:border-white/50 focus:ring-1 focus:ring-white/20 placeholder-neutral-600 transition-all duration-200 ease-in-out"
                        />
                    </div>
                </div>

                <!-- Campo: Biografia -->
                <div class="w-full mb-4">
                    <div class="flex justify-between items-center mb-2">
                        <label class="block text-sm text-neutral-400 font-medium">Biografia</label>
                    </div>
                    <textarea name="bio" rows="3" placeholder="Conte um pouco sobre você..."
                        class="w-full bg-neutral-900/80 border border-neutral-800 text-white px-4 py-3 rounded-lg focus:outline-none focus:border-white/50 focus:ring-1 focus:ring-white/20 placeholder-neutral-600 resize-none transition-all duration-200"
                    ></textarea>
                </div>

                <!-- Botão de Ação Principal: Cadastrar -->
                <button
                    type="submit"
                    class="w-full bg-white text-black font-bold py-3 px-4 rounded-lg mt-6 hover:bg-neutral-200 transition-colors duration-200"
                >
                    Cadastrar
                </button>

                <!-- Link Visual para Login -->
                <div class="text-center text-sm text-neutral-500 mt-6">
                    Já tem conta? 
                    <a href="login.php" class="text-white font-medium hover:underline">Faça login</a>
                </div>

            </form>
        </div>

        <script>
            // --- Lógica JavaScript (Vanilla) ---

            // Elementos do DOM relacionados ao upload de imagem
            const fileInput = document.getElementById('fileInput');
            const uploadClickArea = document.getElementById('uploadClickArea');
            const avatarPreview = document.getElementById('avatarPreview');
            const avatarIcon = document.getElementById('avatarIcon');

            // Aciona o input de arquivo oculto ao clicar na área circular
            uploadClickArea.addEventListener('click', () => {
                fileInput.click();
            });

            // Gerencia a mudança do arquivo selecionado
            fileInput.addEventListener('change', (e) => {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
          
                    // Quando a leitura do arquivo terminar, atualiza o src da imagem
                    reader.onload = (event) => {
                        avatarPreview.src = event.target.result;
                        avatarPreview.classList.remove('hidden'); // Mostra a imagem
                        avatarIcon.classList.add('hidden');       // Esconde o ícone padrão
                    };
          
                    // Lê o arquivo como uma URL de dados (base64)
                    reader.readAsDataURL(file);
                }
            });

        </script>
    </body>
</html>