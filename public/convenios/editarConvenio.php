<?php 

require '../../config/conexaoBanco.php';
// recebendo os dados do formulario via POST:

$id = $_POST['id_convenio'];
$nome = $_POST['nome'];
$codigo = $_POST['codigo'];
$ativo = $_POST['ativo'];

if (empty($id) || empty($nome) || empty($codigo) || !isset($ativo)) {
    header('Location: form-alterar-convenio.php?status=erro');
    exit;
}

// consulta para atualizar dados em pessoas

$alterarConvenio = $conexao->prepare("
    UPDATE convenios 
    SET nome = :nome,
    codigo = :codigo,
    ativo = :ativo
    WHERE id = :id_convenio");

$alterarConvenio->bindValue(':nome', $nome);
$alterarConvenio->bindValue(':codigo', $codigo);
$alterarConvenio->bindValue(':ativo', $ativo);
$alterarConvenio->bindValue(':id_convenio', $id);

try {
    
    $alterarConvenio->execute();

    header('Location: form-listar-convenios.php?sucesso=convenio-atualizado');
    exit;
    
} catch (PDOException $codigoBanco) {
    if ($codigoBanco->getCode() == '23505') {
        header('Location: form-alterar-convenio.php?id=' . $id . '&status=duplicado');
        exit;
    }
        error_log('Erro ao atualizar paciente: ' . $codigoBanco->getMessage());
        header('Location: form-alterar-convenio.php?id=' . $id . '&status=erro');
        exit;
}

?>