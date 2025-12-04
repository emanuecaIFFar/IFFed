<?php
/*
obejtivo;
 1. Verifica autenticação
  2. Verifica se o post pertence ao usuário
  3. Deleta dados relacionados (curtidas, comentários, notificações)
  4. Deleta a postagem
  5. Remove arquivo de imagem (se houver)
 6. Redireciona de volta ao perfil

//coisa que eu n fiz no começo
    L- Antes de deletar uma postagem, precisamos deletar
        tudo que "aponta" para ela (curtidas, comentários).
       Se não fizermos isso, o banco de dados pode dar erro.
 */

session_start();
require_once __DIR__ . '/conexao.php';

if (!isset($_SESSION['id'])) {
    header('Location: ../pages/login.php?erro=nao_autenticado');
    exit;
}


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../pages/perfil.php');
    exit;
} // DELETA APENAS VIA GET


$post_id = intval($_POST['post_id'] ?? 0);
$user_id = intval($_SESSION['id']);

// ID inválido? Volta pro perfil
if ($post_id <= 0) {
    header('Location: ../pages/perfil.php');
    exit;
}


/*
 * MUITO IMPORTANTE: Verifica se o post pertence ao usuário!
 * 
 * Sem isso, qualquer pessoa poderia deletar posts de outros
 * apenas sabendo o ID do post. Isso seria uma falha grave!
 * 
 * Buscamos o post e verificamos id_usuario
 */
$stmt = $conn->prepare('SELECT imagem, id_usuario FROM postagens WHERE id = ? LIMIT 1');
$stmt->bind_param('i', $post_id);
$stmt->execute();
$res = $stmt->get_result();

/*
 * fetch_assoc() retorna a linha como array, ou FALSE se não existir
 */
if (!$row = $res->fetch_assoc()) {
    // Post não existe
    $stmt->close();
    header('Location: ../pages/perfil.php');
    exit;
}
$stmt->close();

/*
 * Compara o dono do post com o usuário logado
 * intval() garante comparação numérica
 * !== é comparação estrita (tipo E valor)
 */
if (intval($row['id_usuario']) !== $user_id) {
    // Não é o dono! Tentativa de deletar post alheio
    // Silenciosamente redireciona (não damos dicas a hackers)
    header('Location: ../pages/perfil.php');
    exit;
}


/*
 * begin_transaction() = inicia uma transação
 * 
 * Transação é um "pacote" de operações que:
 * - Ou TODAS funcionam (commit)
 * - Ou NENHUMA funciona (rollback)
 * 
 * Isso evita dados inconsistentes. Imagine:
 * - Deletou curtidas ✓
 * - Deletou comentários ✓
 * - Erro ao deletar post ✗
 * 
 * Sem transação, ficaria sem curtidas/comentários
 * mas COM o post. Inconsistente!
 * 
 * Com transação, desfaz tudo se der erro.
 */
$conn->begin_transaction();

try {

    /*
     * Precisamos deletar na ORDEM CORRETA:
     * 1. Primeiro: dados que REFERENCIAM a postagem
     * 2. Por último: a própria postagem
     * 
     * Se tentarmos deletar a postagem primeiro,
     * o banco pode dar erro de Foreign Key.
     */
    
    /*
     * try/catch interno para cada DELETE
     * Isso permite que continue mesmo se uma tabela não existir
     */
    
    // Deleta curtidas do post
    try {
        $delCurtidas = $conn->prepare('DELETE FROM curtidas WHERE id_postagem = ?');
        if ($delCurtidas) {
            $delCurtidas->bind_param('i', $post_id);
            $delCurtidas->execute();
            $delCurtidas->close();
        }
    } catch (Throwable $e) {
        // Tabela pode não existir - ignora
        // error_log('Erro ao deletar curtidas: ' . $e->getMessage());
    }

    // Deleta comentários do post
    try {
        $delComentarios = $conn->prepare('DELETE FROM comentarios WHERE id_postagem = ?');
        if ($delComentarios) {
            $delComentarios->bind_param('i', $post_id);
            $delComentarios->execute();
            $delComentarios->close();
        }
    } catch (Throwable $e) {
        // Tabela pode não existir - ignora
    }

    // Deleta notificações relacionadas ao post
    try {
        $delNotif = $conn->prepare('DELETE FROM notificacoes WHERE id_postagem = ?');
        if ($delNotif) {
            $delNotif->bind_param('i', $post_id);
            $delNotif->execute();
            $delNotif->close();
        }
    } catch (Throwable $e) {
        // Tabela pode não existir - ignora
    }


    /*
     * DELETE com WHERE duplo:
     * - id = ? → post específico
     * - id_usuario = ? → garantia extra que é do usuário
     * 
     * Mesmo já tendo verificado antes, essa redundância
     * é uma camada extra de segurança.
     */
    $del = $conn->prepare('DELETE FROM postagens WHERE id = ? AND id_usuario = ?');
    $del->bind_param('ii', $post_id, $user_id);
    $ok = $del->execute();
    $del->close();

    if (!$ok) {
        throw new Exception('Falha ao deletar post');
    }

    /*
     * Se o post tinha imagem, deleta o arquivo do servidor
     * 
     * !empty() = verifica se tem valor
     * file_exists() = verifica se o arquivo existe no disco
     * @unlink() = deleta o arquivo (@ suprime warnings)
     */
    if (!empty($row['imagem'])) {
        $f = __DIR__ . '/../assets/uploads/' . $row['imagem'];
        if (file_exists($f)) {
            @unlink($f);
        }
    }

    /*
     * commit() = confirma todas as operações da transação
     * 
     * Só chega aqui se TUDO funcionou.
     * Os deletes são efetivados permanentemente.
     */
    $conn->commit();
    $_SESSION['flash_success'] = 'Publicação apagada.';

} catch (Exception $e) {
    /*
     * rollback() = desfaz TODAS as operações da transação
     * 
     * Se qualquer coisa deu erro, volta ao estado anterior.
     * É como um "Ctrl+Z" no banco de dados.
     */
    $conn->rollback();
    $_SESSION['flash_error'] = 'Erro ao apagar publicação: ' . $e->getMessage();
    
    /*
     * file_put_contents() = escreve conteúdo em arquivo
     * FILE_APPEND = adiciona ao final (não sobrescreve)
     * 
     * Útil para debug: cria um log de erros
     */
    file_put_contents(
        __DIR__ . '/../debug_delete_error.txt', 
        date('Y-m-d H:i:s') . " - Erro: " . $e->getMessage() . "\n", 
        FILE_APPEND
    );
}


header('Location: ../pages/perfil.php');
exit;
