<?php
/*
como isso deve funcionar;


1. recebe dados via POST (do formulário)
2. processa a foto de perfil (se enviada)
3. insere no banco de dados
4. redireciona para login (sucesso) ou mostra erro

só funciona via POST!
direto pela URL vai causar erros.
 */


include('conexao.php');


/*
sintaxe basica do post la

 $_POST = array com todos os dados enviados pelo formulário
 
cada campo do formulário vira uma chave:
 <input name="nome"> → $_POST['nome']
 <input name="email"> → $_POST['email']

 */
$nome = $conn->real_escape_string($_POST['nome']);
$email = $conn->real_escape_string($_POST['email']);
$senha = $conn->real_escape_string($_POST['senha']); // ⚠️ Idealmente usaríamos hash!
$data = $_POST['data_nascimento']; // Formato: YYYY-MM-DD (padrão do input date)
$bio = $conn->real_escape_string($_POST['bio']);


/*
$_FILES = array especial para arquivos enviados via upload
  

  Estrutura do $_FILES['foto_perfil']:
  Array (
    ['name']     => "minha_foto.jpg"      // Nome original do arquivo
    ['type']     => "image/jpeg"          // Tipo MIME do arquivo
    ['tmp_name'] => "C:/tmp/php1234.tmp"  // Caminho temporário no servidor
    ['error']    => 0                     // Código de erro (0 = sucesso)
    ['size']     => 123456                // Tamanho em bytes
  )
  
  O PHP salva o arquivo numa pasta temporária primeiro.
  precisamos mover para a pasta definitiva.
 */


$nome_arquivo = "img/padrao.svg";

/*
 olhada no upload
  
  isset($_FILES['foto_perfil']) 
    → olha se o campo de arquivo existe
  
  $_FILES['foto_perfil']['error'] == 0
    → se não houve erro no upload
    → códigos de erro:
       0 = UPLOAD_ERR_OK (sucesso!)
       1 = Arquivo muito grande (limite do php.ini)
       2 = Arquivo muito grande (limite do form)
       3 = Upload incompleto
       4 = Nenhum arquivo enviado
       6 = Pasta temporária não encontrada
       7 = Erro ao gravar no disco
 */
if(isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] == 0) {
    
    // Cria variável para facilitar o acesso
    $arquivo = $_FILES['foto_perfil'];
    $novo_nome = uniqid() . "_" . $arquivo['name'];
    

    $pasta_destino = "../assets/uploads/";
    if(move_uploaded_file($arquivo['tmp_name'], $pasta_destino . $novo_nome)) {
        $nome_arquivo = "uploads/" . $novo_nome; //caminho relativo para salvar no banco:
    }
    // se falhar,mantém "padrao.jpg" como foto
}

$sql = "INSERT INTO perfil (nome, email, senha, data_nasc, bio, foto) 
        VALUES ('$nome', '$email', '$senha', '$data', '$bio', '$nome_arquivo')";

/*
 $conn->query($sql) = executa o comando SQL
 
 Para INSERT, UPDATE, DELETE:
   - Retorna TRUE se funcionou
   - Retorna FALSE se deu erro
 
  TRUE = comparação estrita (tipo E valor iguais)
  TRUE ==TRUE → verdadeiro
   1 === TRUE → falso (1 é int, TRUE é boolean)
 */
if($conn->query($sql) === TRUE) { //cadastro passou vai  para o login
 

    header("Location: ../pages/login.php?sucesso=cadastrado"); //vai pro get
    exit; // Sempre use exit após header()!
} else {
   


    echo "Erro ao cadastrar: " . $conn->error;
}
?>