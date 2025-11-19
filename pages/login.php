<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Login - IFFed</title>
    <link rel="stylesheet" href="../assets_front/css/style.css">

    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
      tailwind.config = {
          darkMode: 'class',
          theme: {
              extend: {
                  colors: {
                      zinc: {
                          850: '#1f1f22',
                          950: '#09090b',
                      }
                  },
                  fontFamily: {
                      sans: ['Inter', 'sans-serif'],
                  },
                  animation: {
                      'fade-in': 'fadeIn 0.5s ease-out',
                  },
                  keyframes: {
                      fadeIn: {
                          '0%': { opacity: '0', transform: 'translateY(-10px)' },
                          '100%': { opacity: '1', transform: 'translateY(0)' },
                      }
                  }
              }
          }
      }
    </script>

    <style>
        body {
            font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background-color: #000000;
            color: white;
        }
        /* Custom Scrollbar */
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #09090b; }
        ::-webkit-scrollbar-thumb { background: #27272a; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #3f3f46; }
    </style>
</head>
<body class="min-h-screen bg-black text-white font-sans selection:bg-white/20 flex items-center justify-center relative overflow-hidden p-4">

    <!-- Efeitos de fundo -->
    <div class="absolute top-[-20%] left-[-10%] w-[50%] h-[50%] bg-zinc-800/20 rounded-full blur-[120px] pointer-events-none"></div>
    <div class="absolute bottom-[-20%] right-[-10%] w-[50%] h-[50%] bg-zinc-900/20 rounded-full blur-[120px] pointer-events-none"></div>

    <!-- Container principal -->
    <main class="w-full max-w-md z-10">
        <div class="w-full relative group animate-fade-in">
            <!-- Borda com glow -->
            <div class="absolute -inset-0.5 bg-gradient-to-b from-zinc-700 to-zinc-900 rounded-xl blur opacity-20 group-hover:opacity-40 transition duration-500"></div>
			
            <div class="relative bg-black border border-zinc-800 p-8 rounded-xl shadow-2xl">
                <div class="text-center mb-8">
                    <h2 class="text-2xl font-bold tracking-tight text-white mb-2">Entrar na Rede</h2>
                </div>

                <!-- Mensagem de erro vinda do PHP -->
                <?php
                // Se na URL vier "login.php?erro=login", mostra isso:
                if (isset($_GET['erro']) && $_GET['erro'] === 'login') {
                    echo "
                    <div class='mb-6 p-3 bg-red-900/10 border border-red-500/20 rounded-lg text-center animate-pulse'>
                        <p class='text-sm font-bold text-red-400'>
                            Usuário ou senha incorretos!
                        </p>
                    </div>
                    ";
                }
                ?>

                <!-- Formulário real que vai para o PHP -->
                <form action="../php/validar_login.php" method="POST" class="space-y-6">
                    <div class="space-y-2">
                        <label class="text-sm font-medium text-zinc-300 block">Usuário (Seu Nome):</label>
                        <input
                            type="text"
                            name="email"
                            placeholder="emanuel@email.com"
                            required
                            class="block w-full px-3 py-3 border border-zinc-800 rounded-lg bg-zinc-900/50 text-zinc-100 placeholder-zinc-600 focus:outline-none focus:ring-2 focus:ring-white/10 focus:border-zinc-600 transition-all sm:text-sm"
                        />
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-medium text-zinc-300 block">Senha:</label>
                        <input
                            type="password"
                            name="senha"
                            placeholder="123"
                            required
                            class="block w-full px-3 py-3 border border-zinc-800 rounded-lg bg-zinc-900/50 text-zinc-100 placeholder-zinc-600 focus:outline-none focus:ring-2 focus:ring-white/10 focus:border-zinc-600 transition-all sm:text-sm"
                        />
                    </div>

                    <button
                        type="submit"
                        class="w-full flex justify-center py-3 px-4 border border-transparent text-sm font-bold rounded-lg text-black bg-white hover:bg-zinc-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-zinc-900 focus:ring-white transition-all duration-200 mt-6"
                    >
                        Entrar
                    </button>
                </form>

                <div class="mt-8 text-center">
                    <p class="text-sm text-zinc-500">
                        Não tem conta?
                        <a href="cadastro.php" class="font-medium text-white hover:underline underline-offset-4 transition-all">
                            Cadastre-se aqui
                        </a>.
                    </p>
                </div>
            </div>
        </div>
    </main>

</body>
</html>