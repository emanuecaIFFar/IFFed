<?php
// pages/debug_db.php
// Script simples para listar últimos registros da tabela perfil (apenas para debug local)
include('../php/conexao.php');

$sql = "SELECT id, nome, email, data_nasc, bio, foto FROM perfil ORDER BY id DESC LIMIT 50";
$result = $conn->query($sql);
if(!$result){
    die("Erro no SQL: " . $conn->error);
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <title>Debug DB - perfil</title>
    <style>body{font-family:Arial,Helvetica,sans-serif;background:#111;color:#eee;padding:20px}table{width:100%;border-collapse:collapse}th,td{padding:8px;border:1px solid #333;text-align:left}img{max-width:100px;height:auto;border-radius:6px}</style>
</head>
<body>
    <h1>Últimos registros da tabela <code>perfil</code></h1>
    <p>Se nada aparecer, verifique se o banco <code>iffed</code> e tabela <code>perfil</code> existem e se as credenciais em <code>php/conexao.php</code> estão corretas.</p>
    <table>
        <thead>
            <tr><th>ID</th><th>Nome</th><th>E-mail</th><th>Data Nasc</th><th>Bio</th><th>Foto (caminho)</th><th>Preview</th></tr>
        </thead>
        <tbody>
<?php while($row = $result->fetch_assoc()): ?>
    <tr>
        <td><?php echo htmlspecialchars($row['id']); ?></td>
        <td><?php echo htmlspecialchars($row['nome']); ?></td>
        <td><?php echo htmlspecialchars($row['email']); ?></td>
        <td><?php echo htmlspecialchars($row['data_nasc']); ?></td>
        <td><?php echo nl2br(htmlspecialchars($row['bio'])); ?></td>
        <td><?php echo htmlspecialchars($row['foto']); ?></td>
        <td><?php if(!empty($row['foto'])){ $path = '../assets_front/img/' . $row['foto']; if(file_exists($path)){ echo '<img src="' . $path . '" alt="foto">'; } else { echo '(arquivo não encontrado)'; } } else { echo '-'; } ?></td>
    </tr>
<?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>