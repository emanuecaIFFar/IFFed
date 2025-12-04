<?php
/*
Ooobjetivo;
 1. Verifica autenticação
  2. Recebe conteúdo via POST
  3. Processa upload de imagem (se houver)
  4. Valida tipo e tamanho da imagem
  5. Insere no banco de dados
  6. Redireciona para o perfil
 */


session_start();
require_once __DIR__ . '/conexao.php';

if (!isset($_SESSION['id'])) {
    header('Location: ../pages/login.php?erro=nao_autenticado');
    exit; 
 } //ve se o querido ta logado


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../pages/criar_post.php');
    exit; //lembra rei que e so metodf post
}


/*
 * intval($_SESSION['id']) garante que é um número
 * trim() remove espaços extras do texto
 * ?? '' evita erros se o campo não existir
 */

$user_id = intval($_SESSION['id']);
$conteudo = trim($_POST['conteudo'] ?? ''); //RECEBENDO DADOS DO FORMULÁRIO

$imagem_nome = NULL;

/*
 * verifica se foi enviada uma imagem:
 */
if (!empty($_FILES['imagem']['name'])) {
    $file = $_FILES['imagem']; //verifica se foi enviada uma imagem:
    
    /*
     * UPLOAD_ERR_OK = constante PHP que vale 0
     * Significa "upload sem erros"
     * 
     * Outras constantes de erro:
     * UPLOAD_ERR_INI_SIZE (1) = arquivo muito grande (php.ini)
     * UPLOAD_ERR_FORM_SIZE (2) = arquivo muito grande (formulário)
     * UPLOAD_ERR_PARTIAL (3) = upload incompleto
     * UPLOAD_ERR_NO_FILE (4) = nenhum arquivo
     */

    if ($file['error'] === UPLOAD_ERR_OK) {
        $tmp = $file['tmp_name']; //O PHP salva uploads numa pasta temp antes de movermos
        
        /*
         * getimagesize() = função que lê informações da imagem
         * 
         * Retorna array com:
         * [0] = largura em pixels
         * [1] = altura em pixels
         * ['mime'] = tipo MIME (ex: "image/jpeg")
         * 
         * Se não for imagem válida, retorna FALSE
         * Isso é uma VALIDAÇÃO DE SEGURANÇA!
         * Alguém poderia tentar enviar um .php disfarçado
         */

        $info = getimagesize($tmp);
        
        /*
         * Limite de tamanho: 5MB
         * 
         * 1024 bytes = 1 KB
         * 1024 * 1024 = 1 MB
         * 5 * 1024 * 1024 = 5 MB = 5242880 bytes
         */
        $maxBytes = 5 * 1024 * 1024; // 5MB
        
        // Validação 1: É uma imagem de verdade?
        if ($info === false) {
            /*
             * $_SESSION['flash_error'] = mensagem temporária
             * 
             * "Flash messages" são mensagens que aparecem UMA vez.
             * A página de destino lê e apaga.
             * Útil para mostrar erros após redirect.
             */
            $_SESSION['flash_error'] = 'Arquivo não é uma imagem válida.';
            header('Location: ../pages/criar_post.php'); 
            exit;
        }
        
        // Validação 2: Tamanho OK?
        if ($file['size'] > $maxBytes) {
            $_SESSION['flash_error'] = 'Imagem muito grande (max 5MB).';
            header('Location: ../pages/criar_post.php'); 
            exit;
        }
        
        /*
         * Validação 3: Tipo permitido?
         * 
         * Array associativo: tipo MIME → extensão
         * Só aceitamos JPEG, PNG e WebP
         * 
         * Isso evita arquivos perigosos como .exe, .php, etc.
         */
        $allowed = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp'
        ];
        
        $mime = $info['mime']; // Ex: "image/jpeg"
        
        if (!isset($allowed[$mime])) {
            $_SESSION['flash_error'] = 'Tipo de imagem não permitido.';
            header('Location: ../pages/criar_post.php'); 
            exit;
        }
        
        /*
         * Determina extensão pelo tipo MIME, não pelo nome original!
         * Isso evita que alguém envie "virus.php.jpg"
         */
        $ext = $allowed[$mime];
        $uploadsDir = __DIR__ . '/../assets/uploads/'; //salvando imagem
        
        /*
         * is_dir() = verifica se a pasta existe
         * mkdir() = cria a pasta se não existir
         * 
         * 0755 = permissões Unix (rwxr-xr-x)
         * true = criar pastas intermediárias (recursivo)
         */
        if (!is_dir($uploadsDir)) {
            mkdir($uploadsDir, 0755, true);
        }
        
    
        $imagem_nome = time() . '_' . $user_id . '.' . $ext;
        $dest = $uploadsDir . $imagem_nome;
        
    
        if (!move_uploaded_file($tmp, $dest)) {
            $_SESSION['flash_error'] = 'Falha ao mover imagem.';
            header('Location: ../pages/criar_post.php'); 
            exit;
        }
    }
    // se error != UPLOAD_ERR_OK, simplesmente fods a imagem
}

//finalmente inseridno no bd
/*
  SQL INSERT com prepared statement (seguro!)
  
  Campos:
  - conteudo_textual = texto da postagem
  - id_usuario = quem postou
  - imagem = nome do arquivo (ou NULL)
  - data_criacao = NOW() = data/hora atual
  - num_comentarios = 0 (inicial)
  - num_curtidas = 0 (inicial)
 */
$sql = 'INSERT INTO postagens (conteudo_textual, id_usuario, imagem, data_criacao, num_comentarios, num_curtidas) VALUES (?, ?, ?, NOW(), 0, 0)';

$stmt = $conn->prepare($sql);
if (!$stmt) {
    $_SESSION['flash_error'] = 'Erro no banco.';
    header('Location: ../pages/criar_post.php'); 
    exit;
}

/*
 * bind_param('sis', ...) = tipos dos parâmetros
 *   's' = string (conteudo)
 *   'i' = integer (user_id)
 *   's' = string (imagem - ou NULL que vira string vazia)
 */
$imgParam = $imagem_nome; // Pode ser NULL ou nome do arquivo
$stmt->bind_param('sis', $conteudo, $user_id, $imgParam);

$ok = $stmt->execute();
if (!$ok) {
    $_SESSION['flash_error'] = 'Falha ao salvar postagem.';
    header('Location: ../pages/criar_post.php'); 
    exit;
}
$stmt->close();


$_SESSION['flash_success'] = 'Post criado com sucesso.';
header('Location: ../pages/perfil.php');
exit;
//deu certo, redereciona para o perfil