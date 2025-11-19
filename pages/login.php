<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Login - IFFed</title>
    <link rel="stylesheet" href="../assets_front/css/style.css">
    
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

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
      body {
        font-family: 'Inter', sans-serif;
        background-color: #000000;
        color: white;
      }
      /* Custom Scrollbar */
      ::-webkit-scrollbar { width: 8px; }
      ::-webkit-scrollbar-track { background: #09090b; }
      ::-webkit-scrollbar-thumb { background: #27272a; border-radius: 4px; }
      ::-webkit-scrollbar-thumb:hover { background: #3f3f46; }
    </style>

    <script type="importmap">
{
  "imports": {
    "react": "https://aistudiocdn.com/react@^19.2.0",
    "react-dom/client": "https://aistudiocdn.com/react-dom@^19.2.0/client",
    "lucide-react": "https://aistudiocdn.com/lucide-react@^0.554.0",
    "react/": "https://aistudiocdn.com/react@^19.2.0/"
  }
}
</script>

    <script src="https://unpkg.com/@babel/standalone/babel.min.js"></script>
</head>
<body>
    <div id="root"></div>

    <div class="container-login">
        <h2>Entrar na Rede</h2>

        <?php
        if(isset($_GET['erro']) && $_GET['erro'] == 'login'){
            echo "<p style='color: red; text-align: center; font-weight: bold;'>Usuário ou senha incorretos!</p>";
        }
        ?>

        <form action="../php/validar_login.php" method="POST">
            
            <label>Usuário (Seu Nome):</label>
            <input type="text" name="email" placeholder="emanuel@email.com" required>

            <label>Senha:</label>
            <input type="password" name="senha" placeholder="123" required>

            <button type="submit">Entrar</button>
        </form>

        <p style="text-align:center; margin-top:12px;">Não tem conta? <a href="cadastro.php">Cadastre-se aqui</a>.</p>
        
        <script type="text/babel" data-type="module">
            import React, { useState } from 'react';
            import { createRoot } from 'react-dom/client';
            import { Lock, User } from 'lucide-react';

            const Login = () => {
              const [email, setEmail] = useState('');
              const [password, setPassword] = useState('');
              const [error, setError] = useState(false);
              const [isLoading, setIsLoading] = useState(false);
              const [success, setSuccess] = useState(false);

              const handleLogin = (e) => {
                e.preventDefault();
                setIsLoading(true);
                setError(false);

                // Simulate PHP backend validation
                setTimeout(() => {
                  if (email === 'erro' || password === 'erro') {
                    setError(true);
                    setIsLoading(false);
                  } else {
                    setSuccess(true);
                    setIsLoading(false);
                  }
                }, 1000);
              };

              if (success) {
                return (
                  <div className="w-full bg-black border border-zinc-800 p-8 rounded-xl shadow-2xl text-center animate-fade-in">
                    <h2 className="text-2xl font-bold text-white mb-2">Login Efetuado</h2>
                    <p className="text-zinc-400">Bem-vindo à rede IFFed.</p>
                    <button 
                      onClick={() => { setSuccess(false); setEmail(''); setPassword(''); }}
                      className="mt-6 text-sm font-medium text-white hover:underline"
                    >
                      Sair / Voltar
                    </button>
                  </div>
                );
              }

              return (
                <div className="w-full relative group animate-fade-in">
                  {/* Border Glow Effect */}
                  <div className="absolute -inset-0.5 bg-gradient-to-b from-zinc-700 to-zinc-900 rounded-xl blur opacity-20 group-hover:opacity-40 transition duration-500"></div>
                  
                  <div className="relative bg-black border border-zinc-800 p-8 rounded-xl shadow-2xl">
                    <div className="text-center mb-8">
                      <h2 className="text-2xl font-bold tracking-tight text-white mb-2">Entrar na Rede</h2>
                    </div>

                    {/* Error Message mimicking PHP echo */}
                    {error && (
                      <div className="mb-6 p-3 bg-red-900/10 border border-red-500/20 rounded-lg text-center animate-pulse">
                         <p style={{ color: 'rgb(248 113 113)', fontWeight: 'bold' }} className="text-sm">
                            Usuário ou senha incorretos!
                         </p>
                      </div>
                    )}

                    <form onSubmit={handleLogin} className="space-y-6">
                      <div className="space-y-2">
                        <label className="text-sm font-medium text-zinc-300 block">Usuário (Seu Nome):</label>
                        <div className="relative">
                          <div className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <User className="h-5 w-5 text-zinc-500" />
                          </div>
                          <input
                            type="text"
                            placeholder="emanuel@email.com"
                            value={email}
                            onChange={(e) => setEmail(e.target.value)}
                            required
                            className="block w-full pl-10 pr-3 py-3 border border-zinc-800 rounded-lg bg-zinc-900/50 text-zinc-100 placeholder-zinc-600 focus:outline-none focus:ring-2 focus:ring-white/10 focus:border-zinc-600 transition-all sm:text-sm"
                          />
                        </div>
                      </div>

                      <div className="space-y-2">
                        <label className="text-sm font-medium text-zinc-300 block">Senha:</label>
                        <div className="relative">
                          <div className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <Lock className="h-5 w-5 text-zinc-500" />
                          </div>
                          <input
                            type="password"
                            placeholder="123"
                            value={password}
                            onChange={(e) => setPassword(e.target.value)}
                            required
                            className="block w-full pl-10 pr-3 py-3 border border-zinc-800 rounded-lg bg-zinc-900/50 text-zinc-100 placeholder-zinc-600 focus:outline-none focus:ring-2 focus:ring-white/10 focus:border-zinc-600 transition-all sm:text-sm"
                          />
                        </div>
                      </div>

                      <button
                        type="submit"
                        disabled={isLoading}
                        className="w-full flex justify-center py-3 px-4 border border-transparent text-sm font-bold rounded-lg text-black bg-white hover:bg-zinc-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-zinc-900 focus:ring-white transition-all duration-200 disabled:opacity-70 disabled:cursor-not-allowed mt-6"
                      >
                        {isLoading ? (
                          <span className="w-5 h-5 border-2 border-black border-t-transparent rounded-full animate-spin" />
                        ) : (
                          'Entrar'
                        )}
                      </button>
                    </form>

                    <div className="mt-8 text-center">
                      <p className="text-sm text-zinc-500">
                        Não tem conta?{' '}
                        <a href="#" className="font-medium text-white hover:underline underline-offset-4 transition-all">
                          Cadastre-se aqui
                        </a>.
                      </p>
                    </div>
                  </div>
                </div>
              );
            };

            const App = () => {
              return (
                <div className="min-h-screen bg-black text-white font-sans selection:bg-white/20 flex items-center justify-center relative overflow-hidden p-4">
                  
                  {/* Ambient Background Effects */}
                  <div className="absolute top-[-20%] left-[-10%] w-[50%] h-[50%] bg-zinc-800/20 rounded-full blur-[120px] pointer-events-none"></div>
                  <div className="absolute bottom-[-20%] right-[-10%] w-[50%] h-[50%] bg-zinc-900/20 rounded-full blur-[120px] pointer-events-none"></div>

                  {/* Main Login Container */}
                  <main className="w-full max-w-md z-10">
                    <Login />
                  </main>
                  
                </div>
              );
            };

            const root = createRoot(document.getElementById('root'));
            root.render(<App />);
            
        </script>
        
    </div>
</body>
</html>
