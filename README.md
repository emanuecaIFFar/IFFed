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


## Backend — Especificação e Checklist

Este trecho descreve a especificação mínima necessária para implementar o backend do IFFed (posts, curtidas, comentários, comunidades e notificações). Use como referência ao criar tabelas, endpoints e integrações com o front-end já existente.

### Visão Geral
- Objetivo: Backend leve em PHP + MySQL que suporte: criar post, curtir, comentar, comunidades (criar/entrar/limite), e notificações integradas.
- Sessão: o sistema usa `$_SESSION['id']` e `$_SESSION['nome_usuario']` para identificar o usuário.
- Uploads: salvar arquivos em `assets_front/img/uploads/`.

### Estrutura de Dados (tabelas sugeridas)
- `users`: id, nome, email, senha, foto, criado_em
- `posts`: id, user_id, content, image (nullable), community_id (nullable), created_at, updated_at, is_deleted
- `likes`: id, user_id, post_id, created_at  (UNIQUE user_id+post_id)
- `comments`: id, user_id, post_id, content, created_at
- `communities`: id, creator_user_id, name, description, cover_image, member_limit (default 50), created_at, is_private
- `community_members`: id, community_id, user_id, role (member/moderator), joined_at (UNIQUE community_id+user_id)
- `notifications`: id, user_target_id, actor_user_id, type, post_id (nullable), comment_id (nullable), community_id (nullable), data_json (nullable), is_read (boolean), created_at

> Índices recomendados: `posts.created_at`, `likes.post_id`, `comments.post_id`, `(notifications.user_target_id, is_read)`.

### Tipos de Notificação (mínimo)
- `like` — quando alguém curte um post (target = autor do post)
- `comment` — quando alguém comenta um post (target = autor do post)
- `post_in_community` — quando um post é criado dentro de uma comunidade (opcional notificar membros/seguidores)
- `community_invite` — quando um usuário é convidado/ adicionado a uma comunidade
- `community_join` — quando alguém entra em uma comunidade (notificar criador/mods)
- `follow` — opcional (quando alguém segue outro usuário)

Mantenha apenas os tipos que serão usados para evitar complexidade desnecessária.

### Regras e Fluxos Principais
- Criar Post
	- Verificar sessão; validar conteúdo/arquivo.
	- Salvar imagem em `assets_front/img/uploads/` com nome seguro (timestamp+uid).
	- Inserir em `posts` (usar `community_id` se for post em comunidade).
	- Se `community_id` preenchido: opcionalmente criar notificações `post_in_community` para membros/opt-ins.

- Curtir (Like)
	- Endpoint recebe `post_id` e ação (`like`/`unlike`).
	- Inserir/deletar em `likes`. Se inseriu e autor ≠ actor, criar `notification` tipo `like`.

- Comentar
	- Endpoint recebe `post_id` e `content`.
	- Inserir em `comments`.
	- Criar `notification` tipo `comment` para autor do post (se autor ≠ actor).
	- Opcional: notificar outros comentadores recentes (evitar duplicidade).

- Comunidades
	- Criar comunidade: inserir em `communities`; adicionar criador em `community_members`.
	- Adicionar membro: checar `member_limit` (default 50). Inserir em `community_members` e criar `community_join`/`community_invite`.
	- Ao entrar: exibir comunidade no `perfil` do usuário (query em `community_members`).
	- Ao publicar em comunidade: criar `posts` com `community_id`; notificar membros conforme política (mods/opt-in).

### Endpoints sugeridos (arquivos `php/`)
- `php/create_post.php` — criar post (POST: `content`, optional `community_id`, `image`)
- `php/like.php` — like/unlike (POST: `post_id`, `action`)
- `php/comment.php` — criar comentário (POST: `post_id`, `content`)
- `php/create_community.php` — criar comunidade (POST: `name`, `description`, optional `cover_image`, `member_limit`)
- `php/add_community_member.php` — adicionar/aceitar membro (POST: `community_id`, `user_id`)
- `php/get_notifications.php` — retornar notificações do usuário (GET)
- `php/mark_notifications_read.php` — marcar notificações como lidas (POST)
- `php/get_posts.php` — retornar posts (GET: `page`, optional `community_id`)
- `php/get_community_members.php` — listar membros de uma comunidade (GET)
- `php/get_user_profile.php` — retornar perfil + comunidades + posts do usuário (GET)

Endpoints podem retornar JSON e usar checagem de sessão para endpoints privados.

### Regras de Segurança e Validação
- Verificar `session_start()` e `isset($_SESSION['id'])` em endpoints privados (return 401 se não autenticado).
- Usar prepared statements (mysqli) para evitar SQL injection.
- Validar uploads (MIME type, tamanho) e renomear arquivos antes de salvar.
- Checar permissões para ações sensíveis (ex.: remover post, adicionar membro). 

### Performance e Limitações Práticas
- Não notificar todos os membros para cada post em comunidade (estratégias: notificar mods/opt-ins; limitar a N; enfileirar processamento).
- Usar paginação em `get_posts` e `get_notifications`.
- Denormalizar contadores (`likes_count`, `comments_count`) apenas se necessário.

### Fluxos Resumidos (exemplos)
- Usuário cria post público → `posts` insert → aparece no `index`.
- Usuário B curte post de A → `likes` insert → `notifications` tipo `like` para A.
- Usuário C comenta post de A → `comments` insert → `notifications` tipo `comment` para A.
- Usuário cria comunidade X (member_limit 50) → inserido em `communities`, criador vira membro.

### Checklist prático de Implementação
- [ ] Criar tabelas no banco conforme modelo.
- [ ] Implementar endpoints `php/*.php` com validações e checks de sessão.
- [ ] Garantir pasta `assets_front/img/uploads/` com permissões de escrita.
- [ ] Integrar front (forms/AJAX) com os endpoints.
- [ ] Implementar `php/get_notifications.php` e contador no header/top-bar.
- [ ] Testar fluxos: criar post → curtir → comentar → criar comunidade → adicionar membro → verificar notificações.

### Recomendações / Melhorias Futuras
- Preferências de notificação (mute/unmute por comunidade/tipo).
- Real-time (WebSocket/Pusher) para notificações em tempo real.
- Processamento assíncrono de notificações (fila) para escalar.
- Migrar senhas para `password_hash()` e `password_verify()`.

### Onde colocar este documento
- Sugestão: manter esta seção dentro do `README.md` como "Backend — Especificação e Checklist" (já adicionada aqui). Para documentação separada, crie `README.backend.md`.

---

Se quiser, posso gerar também:
- Exemplos de payload JSON para cada endpoint (sem código de implementação).
- Um diagrama ER textual (relacionamentos entre tabelas) para colar no README.

Informe se quer que eu adicione payloads ou o diagrama ER a seguir neste mesmo arquivo.

