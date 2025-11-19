# IFFed

## Lógica de login e navegação

A aplicação usa sessão PHP para diferenciar usuário **logado** de **visitante** e, com isso, mudar o que aparece no layout.

### 1. Login (`pages/login.php` → `php/validar_login.php`)

1. O usuário envia o formulário em `pages/login.php` para `php/validar_login.php`.
2. No `php/validar_login.php`:
   - Faz a validação (e-mail/senha, por exemplo, no banco).
   - Se der certo, cria variáveis de sessão e redireciona para `index.php`.
   - Se der errado, volta para o login com `?erro=login`.

Exemplo de sucesso no `validar_login.php`:

```php
session_start();

// ... validação de usuário no banco ...

if ($login_valido) {
  $_SESSION['nome_usuario'] = $nome_do_usuario; // usado para saber se está logado
  $_SESSION['id'] = $id_do_usuario;             // usado no perfil

  header('Location: ../index.php');
  exit;
} else {
  header('Location: ../pages/login.php?erro=login');
  exit;
}
```

### 2. Comportamento do `index.php`

No topo do `index.php` a sessão é iniciada:

```php
<?php
session_start();
?>
```

No header da página inicial, o botão muda dependendo se a sessão existe ou não:

```php
<?php if (!isset($_SESSION['nome_usuario'])): ?>
  <!-- Visitante: mostra botão Entrar -->
  <a href="pages/login.php" class="btn btn-sm btn-entrar ms-auto">Entrar</a>
<?php else: ?>
  <!-- Logado: mostra atalho para o perfil -->
  <a href="pages/perfil.php" class="btn btn-sm btn-entrar ms-auto">Meu perfil</a>
<?php endif; ?>
```

**Resumindo a regra:**

- **Se NÃO existir** `$_SESSION['nome_usuario']` → considera como **não logado** (visitante) e mostra "Entrar".
- **Se existir** `$_SESSION['nome_usuario']` → considera como **logado** e mostra "Meu perfil".

### 3. Proteção do perfil (`pages/perfil.php`)

No `perfil.php` o acesso é bloqueado para quem não estiver logado:

```php
session_start();

if (!isset($_SESSION['nome_usuario'])) {
  header('Location: login.php?erro=acesso_negado');
  exit;
}
```

Assim:

- Se tentar acessar o perfil **sem sessão**, é redirecionado para o login com `erro=acesso_negado`.
- Se estiver logado, o perfil carrega normalmente usando `$_SESSION['id']` para buscar os dados no banco.

# IFFed - Rede Social Escolar

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
