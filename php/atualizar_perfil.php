<?php
/*
 * 
 * OBJETIVO:
 * Receber dados do formulário de edição de perfil
 * e atualizar no banco de dados.
 * 
 * CAMPOS QUE PODEM SER ATUALIZADOS:
 * - nome
 * - email  
 * - data_nasc (data de nascimento)
 * - bio (biografia)
 * - foto (upload de nova imagem)
 * 
 * FLUXO:
 * 1. Verifica se usuário está logado
 * 2. Valida método POST
 * 3. Recebe e sanitiza dados
 * 4. Processa upload de foto (se houver)
 * 5. Atualiza registro no banco
 * 6. Redireciona de volta para perfil
 */

// ============================================
// INICIALIZAÇÃO
// ============================================
session_start();
require_once __DIR__ . '/conexao.php';

// ============================================
// VERIFICAÇÃO DE AUTENTICAÇÃO
// ============================================
/*
 * Só usuários logados podem editar seu próprio perfil
 */
if (!isset($_SESSION['id'])) {
    header('Location: ../pages/login.php?erro=acesso_negado');
    exit;
}

// ============================================
// VERIFICAÇÃO DO MÉTODO HTTP
// ============================================
/*
 * Edição de perfil DEVE ser via POST (modifica dados)
 * GET não é permitido por segurança
 */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../pages/perfil.php');
    exit;
}

// ============================================
// RECEBENDO DADOS DO FORMULÁRIO
// ============================================
/*
 * intval() = converte para inteiro (segurança)
 * trim() = remove espaços extras
 * ?? '' = valor padrão se não existir
 */
$user_id = intval($_SESSION['id']);
$nome = trim($_POST['nome'] ?? '');
$email = trim($_POST['email'] ?? '');
$data_nasc = trim($_POST['data_nasc'] ?? '');
$bio = trim($_POST['bio'] ?? '');

// ============================================
// VALIDAÇÃO BÁSICA
// ============================================
/*
 * Nome e email são obrigatórios
 * Outros campos são opcionais
 */
if (empty($nome) || empty($email)) {
    header('Location: ../pages/perfil.php?erro=campos_obrigatorios');
    exit;
}

/*
 * filter_var() = valida formato do email
 * FILTER_VALIDATE_EMAIL = filtro específico para emails
 * Retorna false se o email for inválido
 */
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Location: ../pages/perfil.php?erro=email_invalido');
    exit;
}

// ============================================
// VERIFICAR SE EMAIL JÁ EXISTE (outro usuário)
// ============================================
/*
 * Precisamos garantir que o email não pertence a outro usuário
 * Mas se for o mesmo email do próprio usuário, tudo bem
 */
$stmt_check = $conn->prepare('SELECT id FROM perfil WHERE email = ? AND id != ? LIMIT 1');
$stmt_check->bind_param('si', $email, $user_id);
$stmt_check->execute();
$stmt_check->store_result();

if ($stmt_check->num_rows > 0) {
    // Email já está em uso por outro usuário
    $stmt_check->close();
    header('Location: ../pages/perfil.php?erro=email_existente');
    exit;
}
$stmt_check->close();

// ============================================
// PROCESSAMENTO DE UPLOAD DE FOTO
// ============================================
/*
 * $_FILES = array superglobal com arquivos enviados
 * 
 * Estrutura de $_FILES['foto']:
 * - name: nome original do arquivo
 * - type: tipo MIME (não confiável!)
 * - tmp_name: caminho temporário no servidor
 * - error: código de erro (0 = sucesso)
 * - size: tamanho em bytes
 */
$foto_nome = null; // Se continuar null, não atualiza a foto

if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
    $arquivo = $_FILES['foto'];
    
    /*
     * Validação do tipo de arquivo
     * getimagesize() = retorna info da imagem ou false se não for imagem
     * Mais seguro que confiar em $_FILES['type']!
     */
    $info_imagem = getimagesize($arquivo['tmp_name']);
    if ($info_imagem === false) {
        header('Location: ../pages/perfil.php?erro=arquivo_invalido');
        exit;
    }
    
    /*
     * Verificar tipos permitidos
     * IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_GIF, IMAGETYPE_WEBP
     * São constantes do PHP para tipos de imagem
     */
    $tipos_permitidos = [IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_GIF, IMAGETYPE_WEBP];
    if (!in_array($info_imagem[2], $tipos_permitidos)) {
        header('Location: ../pages/perfil.php?erro=tipo_nao_permitido');
        exit;
    }
    
    /*
     * Limitar tamanho (5MB = 5 * 1024 * 1024 bytes)
     */
    $tamanho_maximo = 5 * 1024 * 1024; // 5MB
    if ($arquivo['size'] > $tamanho_maximo) {
        header('Location: ../pages/perfil.php?erro=arquivo_grande');
        exit;
    }
    
    /*
     * Gerar nome único para o arquivo
     * time() = timestamp atual (garante unicidade)
     * bin2hex(random_bytes(8)) = 16 caracteres aleatórios
     * 
     * Isso evita:
     * 1. Conflitos de nome
     * 2. Caracteres especiais no nome
     * 3. Ataques de path traversal
     */
    $extensoes = [
        IMAGETYPE_JPEG => 'jpg',
        IMAGETYPE_PNG => 'png', 
        IMAGETYPE_GIF => 'gif',
        IMAGETYPE_WEBP => 'webp'
    ];
    $extensao = $extensoes[$info_imagem[2]];
    $novo_nome = time() . '_' . bin2hex(random_bytes(8)) . '.' . $extensao;
    
    /*
     * Caminho de destino
     * __DIR__ = diretório atual (php/)
     * Subimos um nível (..) e entramos em assets/uploads/
     */
    $destino = __DIR__ . '/../assets/uploads/' . $novo_nome;
    
    /*
     * move_uploaded_file() = move arquivo do temp para destino
     * Função segura do PHP - só funciona com uploads reais
     */
    if (move_uploaded_file($arquivo['tmp_name'], $destino)) {
        // Sucesso! Salvar no formato esperado pelo sistema
        $foto_nome = 'uploads/' . $novo_nome;
    } else {
        // Falha ao mover - pode ser permissão de pasta
        header('Location: ../pages/perfil.php?erro=falha_upload');
        exit;
    }
}

// ============================================
// ATUALIZAÇÃO NO BANCO DE DADOS
// ============================================
/*
 * Montamos a query dinamicamente:
 * - Se tem foto nova, inclui no UPDATE
 * - Se não tem, mantém a foto atual
 * 
 * Prepared statements previnem SQL Injection!
 */
if ($foto_nome !== null) {
    // Com foto nova
    $sql = 'UPDATE perfil SET nome = ?, email = ?, data_nasc = ?, bio = ?, foto = ? WHERE id = ?';
    $stmt = $conn->prepare($sql);
    
    /*
     * bind_param tipos:
     * s = string
     * i = integer
     * 
     * 'sssssi' = 5 strings + 1 integer
     */
    $data_nasc_db = !empty($data_nasc) ? $data_nasc : null;
    $stmt->bind_param('sssssi', $nome, $email, $data_nasc_db, $bio, $foto_nome, $user_id);
} else {
    // Sem foto nova (mantém a atual)
    $sql = 'UPDATE perfil SET nome = ?, email = ?, data_nasc = ?, bio = ? WHERE id = ?';
    $stmt = $conn->prepare($sql);
    
    $data_nasc_db = !empty($data_nasc) ? $data_nasc : null;
    $stmt->bind_param('ssssi', $nome, $email, $data_nasc_db, $bio, $user_id);
}

/*
 * execute() = executa a query
 * Retorna true em sucesso, false em falha
 */
$sucesso = $stmt->execute();
$stmt->close();

if (!$sucesso) {
    header('Location: ../pages/perfil.php?erro=falha_banco');
    exit;
}

// ============================================
// ATUALIZAR SESSÃO
// ============================================
/*
 * Se o nome mudou, atualizamos na sessão também
 * Assim a UI reflete imediatamente a mudança
 */
$_SESSION['nome_usuario'] = $nome;

// ============================================
// REDIRECIONAR COM SUCESSO
// ============================================
/*
 * Padrão POST-Redirect-GET:
 * Após processar POST, redireciona para evitar
 * reenvio acidental do formulário (F5)
 */
header('Location: ../pages/perfil.php?sucesso=perfil_atualizado');
exit;
