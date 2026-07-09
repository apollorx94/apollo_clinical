<?php 

require '../../config/conexaoBanco.php';

$idConvenio = $_GET['id'];

try {
    $editarConvenio = $conexao->prepare("UPDATE convenios SET ativo = false WHERE id = :id");
    $editarConvenio->bindValue(':id', $idConvenio);
    $editarConvenio->execute();
    header('Location: form-listar-convenios.php?sucesso=paciente-removido');
    exit;
} 

    catch (PDOException $codigoBanco) {
        
    if ($codigoBanco->getCode() == '23503') {
        header('Location: form-listar-convenios.php?erro=vinculo-existente');
        exit;
    }
    
    header('Location: form-listar-convenios.php?erro=impossivel-remover');
    exit;
    
    
}


?>