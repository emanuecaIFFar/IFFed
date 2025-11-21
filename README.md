# IFFed - Rede Social

**Status:** Em desenvolvimento 🚧

## Sobre o projeto

Este projeto é uma Rede Social Escolar desenvolvida como atividade prática para a disciplina de Desenvolvimento Web. O objetivo é integrar Front-end e Back-end usando PHP e MySQL, cobrindo autenticação, upload de arquivos, sessões e exibição de perfis.

Principais conceitos usados:

- Conexão com banco de dados MySQL
- Autenticação (Login / Logout)
# IFFed - Rede Social

**Status:** Em desenvolvimento 🚧

## Sobre o projeto

Este projeto é uma Rede Social Escolar desenvolvida como atividade prática para a disciplina de Desenvolvimento Web. O objetivo é integrar Front-end e Back-end usando PHP e MySQL, cobrindo autenticação, upload de arquivos, sessões e exibição de perfis.

Principais conceitos usados:

- Conexão com banco de dados MySQL
- Autenticação (Login / Logout)
- Sessões com `$_SESSION`
- Upload de arquivos com `$_FILES`
- Estrutura simples de separação entre lógica (`php/`) e views (`pages/`)

## Tecnologias

- HTML5, CSS3
- PHP (procedural/estruturado)
- MySQL
- Ambiente local recomendado: USBWebServer ou XAMPP

## Como executar (instalação rápida)

1. Pré-requisitos

- Instale um servidor local que tenha PHP e MySQL (USBWebServer ou XAMPP).

2. Clone o repositório

Abra o terminal na pasta onde coloca os projetos web e rode:

```bash
git clone https://github.com/SEU_USUARIO/IFFed.git
```

3. Criar banco de dados e tabelas

Abra o `phpMyAdmin` ou outro cliente MySQL e execute o script abaixo para criar o banco e a tabela de perfil:

```sql
CREATE DATABASE IF NOT EXISTS iffed;
USE iffed;

CREATE TABLE IF NOT EXISTS perfil (
		id INT AUTO_INCREMENT PRIMARY KEY,
		nome VARCHAR(100) NOT NULL,
		email VARCHAR(100) NOT NULL UNIQUE,
		senha VARCHAR(255) NOT NULL,
		data_nasc DATE,
		bio TEXT,
		foto VARCHAR(255) DEFAULT 'padrao.jpg'
);

INSERT INTO perfil (nome, email, senha, bio) 
VALUES ('Admin', 'admin@iffed.com', '123', 'Conta de Teste');
```

> Observação: o projeto atualmente salva senhas em texto simples (apenas para testes). Em produção, sempre use `password_hash()` e `password_verify()`.

4. Ajustar conexão com o banco

Edite o arquivo `php/conexao.php` para checar as credenciais do seu ambiente (usuário/senha/host). Exemplos comuns:

- USBWebServer: usuário `root`, senha `usbw`
- XAMPP: usuário `root`, senha em branco

5. Acesse no navegador

Abra `http://localhost/IFFed/index.php` (ou a URL/porta correspondente ao seu servidor local).

## Estrutura de pastas

```
IFFed/
├── assets_front/      # CSS, JS, imagens
│   ├── css/
│   ├── img/
│   │   └── uploads/   # fotos enviadas pelos usuários
│   └── js/
│
├── pages/             # Views públicas (login, cadastro, perfil)
│   ├── login.php
│   ├── cadastro.php
│   └── perfil.php
│
├── php/               # Lógica do servidor
│   ├── conexao.php
│   ├── validar_login.php
│   ├── cadastrar.php
│   └── sair.php
│
└── index.php          # Página inicial
```

## Pasta de Debug

- Existem páginas de debug (por exemplo `pages/debug_db.php`) que exibem componentes padrão do site — como a `sidebar`, `top-bar` e exemplos de cards — para facilitar a criação de novas páginas.
- Use essas páginas como referência/backup: você pode copiar trechos do layout diretamente das páginas de debug ao criar uma nova view, evitando ter que recriar tudo novamente.


## Como o Front-end deve se comunicar com o Back-end

- `pages/login.php` deve enviar o formulário para `../php/validar_login.php` usando `method="POST"`.
- `pages/cadastro.php` deve usar `enctype="multipart/form-data"` e enviar para `../php/cadastrar.php`.
- `pages/perfil.php` depende de `$_SESSION['nome_usuario']` para mostrar o perfil; o topo do arquivo contém a checagem de sessão (não remova).

Exemplos importantes:

- Formulário de login:

```html
<form action="../php/validar_login.php" method="POST">
		<input type="text" name="email">
		<input type="password" name="senha">
</form>
```

- Formulário de cadastro (upload):

```html
<form action="../php/cadastrar.php" method="POST" enctype="multipart/form-data">
		<input type="file" name="foto_perfil">
		<!-- outros campos: nome, email, senha, data_nascimento, bio -->
</form>
```

- Exibição de foto no perfil:

```php
<img src="../assets_front/img/<?php echo $dados_usuario['foto']; ?>" alt="Foto de Perfil">
```

## Boas práticas e próximos passos

- Trocar o armazenamento de senha para `password_hash()`.
- Validar e sanitizar todos os inputs antes de inserir no banco.
- Proteger uploads (validar tipo/ tamanho / renomear arquivos e não confiar no `$_FILES['type']`).
- Implementar mensagens de sucesso/erro com `$_SESSION` flash messages ou parâmetros `?sucesso=...`.

## Contato

Se quiser, posso ajudar a:

- Implementar upload seguro (`php/upload_foto_teste.php`) e integração com `pages/perfil.php`.
- Migrar senhas para `password_hash()` e atualizar `validar_login.php`.
- Corrigir outros links ou automatizar testes de rotas.

Boa sorte com o projeto! 🚀


## Backend — IFeed

Inclui: como as sessões são usadas, responsabilidades dos arquivos PHP existentes, fluxos principais (login, cadastro, upload, posts, curtidas, comentários) e exemplos práticos.

**Variáveis de sessão usadas**
- `$_SESSION['id']` — id numérico do usuário autenticado (PK na tabela `perfil`).
- `$_SESSION['nome_usuario']` — nome de exibição do usuário (usado na UI e para checagens simples).

Todas as páginas/ endpoints que exigem autenticação devem chamar `session_start()` no topo do arquivo e checar `isset($_SESSION['id'])` antes de realizar ações sensíveis.

**Visão geral dos arquivos PHP existentes (papel de cada um)**

- `php/conexao.php`
  - Responsabilidade: estabelecer e exportar a conexão com MySQL (mysqli). Deve centralizar host, usuário, senha e nome do banco.
  - Uso típico: incluir com `require_once '../php/conexao.php';` e usar a variável/objeto de conexão retornado.

- `php/validar_login.php`
  - Responsabilidade: receber dados do formulário de login (tipicamente `POST: email, senha`), verificar credenciais no banco e iniciar a sessão.
  - Fluxo:
    1. `session_start()`
    2. Ler `$_POST['email']` e `$_POST['senha']`.
    3. Buscar usuário na tabela `perfil` por email.
    4. Verificar senha (atualmente o projeto pode usar comparação simples; substituir por `password_verify()` quando usar hashes).
    5. Se autenticado: setar `$_SESSION['id'] = $id` e `$_SESSION['nome_usuario'] = $nome` e redirecionar para a página principal/ perfil.
    6. Se falha: redirecionar de volta para o formulário com erro.

- `php/cadastrar.php`
  - Responsabilidade: processar formulário de cadastro (inclui upload de foto, criar registro em `perfil`).
  - Fluxo:
    1. `session_start()` (opcional, apenas se desejar logar automaticamente após cadastro).
    2. Receber campos via `$_POST` e arquivo via `$_FILES` (se houver upload de foto).
    3. Validar entradas (email único, formato, senha forte).
    4. Mover arquivo para `assets_front/img/uploads/` com nome seguro e salvar nome no campo `foto` (ex.: `time() . '_' . $userId . '.' . $ext`).
    5. Inserir registro em `perfil`.
    6. Redirecionar com `?sucesso=cadastrado` ou mensagem de erro.

- `php/sair.php`
  - Responsabilidade: encerrar a sessão do usuário.
  - Fluxo: `session_start(); session_unset(); session_destroy();` e redirecionar para a página pública (ex.: `index.php`).

- `php/upload_foto_teste.php` (se presente)
  - Responsabilidade: handler separado para testar upload de foto. Deve validar tipo/ tamanho e devolver sucesso/erro.

Observação: podem existir arquivos adicionais em `php/` com lógicas específicas; os nomes acima são os principais lidos pelo front-end atual.

---

## Fluxos principais (detalhados)

### 1) Login

- Request: método `POST` para `php/validar_login.php` com campos `email` e `senha`.
- Ação do servidor:
  - `session_start()`
  - Buscar usuário por `email` na tabela `perfil`.
  - Comparar senhas (ideal: `password_verify($senhaEntrada, $senhaHashNoDB)`).
  - Se OK: setar `$_SESSION['id']` e `$_SESSION['nome_usuario']` e redirecionar para `pages/perfil.php`.
  - Se não: redirecionar para login com `?erro=credenciais`.

Exemplo (esqueleto):

```php
<?php
session_start();
require_once 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: ../pages/login.php'); exit; }

$email = $_POST['email'] ?? '';
$senha = $_POST['senha'] ?? '';

$stmt = $conn->prepare('SELECT id, nome, senha FROM perfil WHERE email = ? LIMIT 1');
$stmt->bind_param('s', $email);
$stmt->execute();
$res = $stmt->get_result();
if ($user = $res->fetch_assoc()) {
    if (password_verify($senha, $user['senha'])) {
        $_SESSION['id'] = (int)$user['id'];
        $_SESSION['nome_usuario'] = $user['nome'];
        header('Location: ../pages/perfil.php');
        exit;
    }
}
header('Location: ../pages/login.php?erro=credenciais');
```

### 2) Cadastro (com upload opcional)

- Request: `POST` para `php/cadastrar.php` com campos do formulário e `enctype="multipart/form-data"` se houver imagem.
- Ação do servidor:
  - Validar campos obrigatórios.
  - Checar duplicidade de e-mail.
  - Processar `$_FILES['foto_perfil']`: validar MIME, tamanho e mover para `assets_front/img/uploads/` com nome seguro.
  - Inserir novo registro em `perfil` (armazenar o nome do arquivo no campo `foto`).
  - Redirecionar para `pages/login.php?sucesso=cadastrado` ou logar automaticamente.

### 3) Upload de imagens (regra geral)

- Boas práticas:
  - Validar tipo real com `getimagesize()` e não confiar apenas no `$_FILES['type']`.
  - Limitar tamanho (ex.: 2-5MB).
  - Renomear arquivo: `time() . '_' . bin2hex(random_bytes(6)) . '.' . $ext`.
  - Definir permissões seguras e armazenar apenas o nome/ caminho relativo no DB.

### 4) Criar Post

- Request: `POST` para `php/create_post.php` (sugerido) com `conteudo_textual`, opcional `imagem`.
- Ação do servidor:
  - `session_start()` e checar `$_SESSION['id']`.
  - Validar conteúdo e processar upload (se houver).
  - Inserir em `postagens` com `id_usuario = $_SESSION['id']` e `data_criacao = NOW()`.

### 5) Curtir (Like)

- Request: `POST` para `php/like.php` com `id_postagem` e `action` (`like` ou `unlike`).
- Ação do servidor:
  - `session_start()` e checar `$_SESSION['id']`.
  - Inserir ou remover registro em `curtidas`.
  - Atualizar contador em `postagens.num_curtidas` (opcional, ou calcular com `COUNT(*)`).

### 6) Comentar

- Request: `POST` para `php/comment.php` com `id_postagem` e `conteudo`.
- Ação do servidor:
  - Checar sessão, inserir em `comments` (usar FK para `postagens`), criar notificação para autor do post.

---

## Mapeamento do esquema atual (tabelas que existem e campos relevants)

- `perfil` (users)
  - id (PK int auto_increment)
  - nome (varchar)
  - senha (varchar) — reforçar uso de hash
  - email (varchar, UNIQUE)
  - foto (varchar, nome relativo do arquivo)
  - data_nasc (date)
  - bio (text)

- `postagens` (posts)
  - id (PK)
  - conteudo_textual (text)
  - data_criacao (datetime)
  - id_usuario (FK -> perfil.id)
  - imagem (varchar)
  - num_comentarios (int)
  - num_curtidas (int)

- `curtidas` (likes)
  - id (PK)
  - id_postagem (FK -> postagens.id)
  - id_usuario (FK -> perfil.id)

Tabelas recomendadas a adicionar: `comments`, `notifications`, `communities`, `community_members` (DDL sugerido no `README.md`).

---

## Segurança e validação (práticas obrigatórias)

- Sempre usar prepared statements (`$stmt = $conn->prepare(...)`) e bind de parâmetros para evitar SQL Injection.
- Usar `password_hash()` para armazenar senhas e `password_verify()` no login.
- Sanitizar saídas com `htmlspecialchars()` antes de inserir dados em HTML.
- Proteger endpoints com `session_start()` + checagem de `$_SESSION['id']`.
- Validar uploads: tamanho máximo, limitar tipos (jpeg/png/webp), verificar com `getimagesize()`.

## Exemplo de checagem de sessão (topo de um endpoint privado)

```php
<?php
session_start();
if (!isset($_SESSION['id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'não autenticado']);
    exit;
}
// continuar com a lógica do endpoint
```

## Respostas e formato (recomendação)

- Endpoints de API (novos) devem retornar JSON com estrutura clara:
  - sucesso: `{ "success": true, "data": {...} }`
  - erro: `{ "success": false, "error": "mensagem" }`

Isso facilita integração via AJAX no front-end.

## Logs e diagnóstico

- Registrar erros críticos em arquivo de log (ex.: `logs/error.log`) com `error_log()`.
- Em ambiente dev, exibir erros; em produção, desabilitar exibição de erro e somente logar.

## Tarefas recomendadas para próxima iteração

- Migrar as senhas existentes para `password_hash()` (criar script de migração se necessário).
- Criar endpoints esqueleto em `php/` para `create_post.php`, `like.php`, `comment.php` e `get_posts.php`.
- Adicionar tabela `comments` e `notifications` no banco.

---

Se desejar, este documento pode ser copiado para o `README.md` principal ou mantido como `README.backend.md`. Também é possível gerar exemplos de payload JSON e os esboços dos endpoints em PHP conforme o estilo e as práticas descritas aqui.
