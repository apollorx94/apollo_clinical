<?php

require '../../config/conexaoBanco.php';
// recebendo os dados do formulario via POST:

$nome = $_POST['nome'];
$cpf = $_POST['cpf'];
$telefone = $_POST['telefone'];
$email = $_POST['email'];
$data_nascimento = $_POST['data_nascimento'];
$logradouro = $_POST['logradouro'];
$numero = $_POST['numero'];
$complemento = $_POST['complemento'];
$bairro = $_POST['bairro'];
$cidade = $_POST['cidade'];
$uf = $_POST['uf'];
$cep = $_POST['cep'];
$contato_emergencial = $_POST['contato_emergencial'];
$observacoes = $_POST['observacoes'];
$id_paciente = $_POST['id_paciente'];
$id_pessoa = $_POST['id_pessoa'];
$convenio_id = $_POST['convenio_id'];

if (empty($nome) || empty($cpf) || empty($telefone) || empty($data_nascimento)) {
    header('Location: form-alterar-pacientes.php?status=erro');
    exit;
}

// consulta para atualizar dados em pessoas

$atualizarDadosPessoa = $conexao->prepare("UPDATE pessoas SET 
    nome = :nome,
    cpf = :cpf,
    telefone = :telefone,
    email = :email,
    cep = :cep,
    logradouro = :logradouro,
    numero = :numero,
    complemento = :complemento,
    bairro = :bairro,
    cidade = :cidade,
    uf = :uf,
    contato_emergencial = :contato_emergencial 
    WHERE id = :id_pessoa");

$atualizarDadosPessoa->bindValue(':nome', $nome);
$atualizarDadosPessoa->bindValue(':cpf', $cpf);
$atualizarDadosPessoa->bindValue(':telefone', $telefone);
$atualizarDadosPessoa->bindValue(':email', $email);
$atualizarDadosPessoa->bindValue(':cep', $cep);
$atualizarDadosPessoa->bindValue(':logradouro', $logradouro);
$atualizarDadosPessoa->bindValue(':numero', $numero);
$atualizarDadosPessoa->bindValue(':complemento', $complemento);
$atualizarDadosPessoa->bindValue(':bairro', $bairro);
$atualizarDadosPessoa->bindValue(':cidade', $cidade);
$atualizarDadosPessoa->bindValue(':uf', $uf);
$atualizarDadosPessoa->bindValue(':contato_emergencial', $contato_emergencial);
$atualizarDadosPessoa->bindValue(':id_pessoa', $id_pessoa);


// consulta para atualizar dados em pacientes

$atualizarDadosPaciente = $conexao->prepare("UPDATE pacientes SET
    data_nascimento = :data_nascimento,
    observacoes = :observacoes,
    convenio_id = :convenio_id
    WHERE id = :id_paciente");


$atualizarDadosPaciente->bindValue(':data_nascimento', $data_nascimento);
$atualizarDadosPaciente->bindValue(':observacoes', $observacoes);
$atualizarDadosPaciente->bindValue(':convenio_id', $convenio_id);
$atualizarDadosPaciente->bindValue(':id_paciente', $id_paciente);


try {

    $atualizarDadosPessoa->execute();
    $atualizarDadosPaciente->execute();

    header('Location: form-listar-pacientes.php?sucesso=paciente-atualizado');
    exit;
} catch (PDOException $codigoBanco) {
    if ($codigoBanco->getCode() == '23505') {
        header('Location: form-alterar-pacientes.php?id=' . $id_paciente . '&status=duplicado');
        exit;
    }
    error_log('Erro ao atualizar paciente: ' . $codigoBanco->getMessage());
    header('Location: form-alterar-pacientes.php?id=' . $id_paciente . '&status=erro');
    exit;
}
