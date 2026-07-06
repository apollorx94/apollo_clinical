<?php

require '../../config/conexaoBanco.php';

$idPaciente = $_GET['id'];

try {
    $excluirPaciente = $conexao->prepare("DELETE FROM pacientes WHERE id = :id");
    $excluirPaciente->bindValue(':id', $idPaciente);
    $excluirPaciente->execute();
    header('Location: form-listar-pacientes.php?sucesso=paciente-removido');
    exit;
} 

    catch (PDOException $codigoBanco) {
        
    if ($codigoBanco->getCode() == '23503') {
        header('Location: form-listar-pacientes.php?erro=vinculo-existente');
        exit;
    }
    
    header('Location: form-listar-pacientes.php?erro=impossivel-remover');
    exit;
    
}