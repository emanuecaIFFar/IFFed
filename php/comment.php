<?php
/*
objetivosl
  1. Verfica se usuário está logado
  2. Recebe conteúdo e ID da postagem via POST
  3. Insere comentário no banco
  4. Atualiza contador de comentários na postagem
  5. Redireciona de volta (ou retorna JSON para AJAX)
  
 -- tem dois modos de uso bagual, sei que suporta ajax e phpzada
 */


session_start();

/*
 * require_once = inclui arquivo, mas só uma vez
 * 
 * Diferença de include:
 * - include: se falhar, continua executando (warning)
 * - require: se falhar, para tudo (fatal error)
 * - require_once: só inclui se ainda não foi incluído
 * 
 * __DIR__ = constante mágica = diretório atual do arquivo
 * Exemplo: d:\usbwebserver\root\IFFed\IFFed\php
 */
require_once __DIR__ . '/conexao.php'; //__DIR__ = constante mágica = diretório atual do arquivo Exemplo: d:\usbwebserver\root\IFFed\IFFed\php



if (!isset($_SESSION['id'])) { //v se o user ta logado
   
    if (!empty($_POST['redirect_to'])) {
        header('Location: ' . $_POST['redirect_to']);
    exit; //empty se n me engano, [é se nao ta]
    }

    http_response_code(401);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['success' => false, 'error' => 'não autenticado']);
    exit; //ajax_se quiser
}


/*
  $_SERVER['REQUEST_METHOD'] = método HTTP usado
  
  Valores comuns:
  - 'GET' = acessou via URL (ex: digitou no navegador)
  - 'POST' = enviou formulário ou dados
  
  Comentários só podem ser criados via POST!
  se alguém tentar via GET, retorna erro.
  
  !== = diferente (comparação estrita)
 */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // 405 = Method Not Allowed
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['success' => false, 'error' => 'método inválido']);
    exit;
}


/*
 * trim() = remove espaços em branco do início e fim
 *   "  texto  " → "texto"
 * 
 * ?? '' = operador de coalescência nula
 *   Se $_POST['conteudo'] não existir, usa '' (string vazia)
 *   Evita erros se o campo não for enviado
 * 
 * intval() = converte para inteiro
 *   "123abc" → 123
 *   "abc" → 0
 *   Isso previne SQL Injection em números!
 */
$conteudo = trim($_POST['conteudo'] ?? '');
$id_postagem = intval($_POST['id_postagem'] ?? 0);
$user_id = intval($_SESSION['id']);

/*
 * Validação:
 * - $conteudo === '' → comentário vazio não é permitido
 * - $id_postagem <= 0 → ID inválido ou não fornecido
 */
if ($conteudo === '' || $id_postagem <= 0) {
    if (!empty($_POST['redirect_to'])) {
        header('Location: ' . $_POST['redirect_to']);
        exit;
    }
    http_response_code(400); // 400 = Bad Request
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['success' => false, 'error' => 'dados inválidos']);
    exit;
}


// cOMENTÁRIO NO BANCO

/*
 * Prepared Statements = forma SEGURA de executar SQL
 * 
 * Em vez de concatenar valores direto na query (perigoso!):
 *   "INSERT INTO ... VALUES ('$conteudo', $user_id, $id_postagem)"
 * 
 * Usamos placeholders (?) e bind_param:
 *   "INSERT INTO ... VALUES (?, ?, ?)"
 *   bind_param('sii', $conteudo, $user_id, $id_postagem)
 * 
 * Isso PREVINE SQL Injection porque os valores são
 * tratados separadamente da estrutura da query.
 * 
 * NOW() = função SQL que retorna data/hora atual
 */
$stmt = $conn->prepare('INSERT INTO comentarios (conteudo, id_usuario, id_postagem, data_criacao) VALUES (?, ?, ?, NOW())');

if (!$stmt) { //se falhar, empaty novamente
    
    if (!empty($_POST['redirect_to'])) {
        header('Location: ' . $_POST['redirect_to']);
        exit;
    }
    http_response_code(500); // erro chato {500}
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['success' => false, 'error' => 'erro no banco']);
    exit;
}

/* 
 * bind_param() = associa valores aos placeholders (?)
 * 
 * Primeiro parâmetro = tipos das variáveis:
 *   's' = string
 *   'i' = integer (número inteiro)
 *   'd' = double (número decimal)
 *   'b' = blob (dados binários)
 * 
 * 'sii' = 1ª string, 2ª integer, 3ª integer
 * 
 * A ordem deve corresponder à ordem dos ? na query!
 */
$stmt->bind_param('sii', $conteudo, $user_id, $id_postagem);


$ok = $stmt->execute(); //query execute, pra pocar

if (!$ok) {
    if (!empty($_POST['redirect_to'])) {
        header('Location: ' . $_POST['redirect_to']);
        exit;
    }
    http_response_code(500);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['success' => false, 'error' => 'falha ao salvar comentário']);
    exit;
}


$inserted_id = $stmt->insert_id; //insert_id = ID gerado pelo AUTO_INCREMENT
$stmt->close();

 
/*
contator de comentarios true
  Incrementa o num_comentarios na tabela postagens.
  
  Isso é uma "desnormalização" para performance:
  Em vez de contar comentários toda vez (SELECT COUNT(*)),
  mantemos um contador atualizado na própria postagem.
  
 try/catch = captura erros sem parar o script
 Se a coluna não existir, simplesmente ignora.
  
  Throwable = captura qualquer tipo de erro/exceção
 */
try {
    $u = $conn->prepare('UPDATE postagens SET num_comentarios = num_comentarios + 1 WHERE id = ?');
    if ($u) {
        $u->bind_param('i', $id_postagem);
        $u->execute();
        $u->close();
    }
} catch (Throwable $e) {
    // Não é fatal - apenas loga o erro e continua
    // error_log($e->getMessage());
}


if (!empty($_POST['redirect_to'])) {
    $loc = $_POST['redirect_to'];
    header('Location: ' . $loc);
    exit;
}

// Retorna JSON para compatibilidade com chamadas AJAX existentes
header('Content-Type: application/json; charset=utf-8');
echo json_encode([
    'success' => true, 
    'data' => [
        'id' => $inserted_id, 
        'conteudo' => $conteudo, 
        'id_postagem' => $id_postagem, 
        'id_usuario' => $user_id
    ]
]);
