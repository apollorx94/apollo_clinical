<?php 

require '../../config/conexaoBanco.php';

$nome = $_POST['nome'];
$codigo = $_POST['codigo'];
$ativo = $_POST['ativo'];

if (empty($nome) || empty($codigo)) {
    header('Location: form-cadastrar-convenio.php?status=erro');
        exit;
}

$cadastraConvenio = $conexao->prepare("INSERT INTO convenios
    (nome, codigo, ativo) VALUES (:nome, :codigo, :ativo)");

$cadastraConvenio->bindValue(':nome', $nome);
$cadastraConvenio->bindValue(':codigo', $codigo);
$cadastraConvenio->bindValue(':ativo', $ativo);

try {

$cadastraConvenio->execute();

    header('Location: form-cadastrar-convenio.php?status=sucesso');
    exit;

} catch (PDOException $codigoBanco) {
    if ($codigoBanco->getCode() == '23505') {
        header('Location: form-cadastrar-convenio.php?status=duplicado');
        exit;
    }
    error_log('Erro ao cadastrar convenio: ' . $codigoBanco->getMessage());
    header('Location: form-cadastrar-convenio.php?status=erro');
    exit;
}

?>