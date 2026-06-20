<?php 

require '../../config/conexaoBanco.php';

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
    $convenioId = $_POST['convenio_id'];
    $observacoes = $_POST['observacoes'];

    if (empty($nome) || empty($cpf) || empty($telefone) || empty($data_nascimento)) {
        header('Location: cadastrar-paciente.php?status=erro');
        exit;
    }

    try {

    $cadastrarPessoa = $conexao->prepare("INSERT INTO pessoas (nome, cpf, telefone, email, cep, logradouro, numero, complemento, bairro, cidade, uf) VALUES (:nome, :cpf, :telefone, :email, :cep, :logradouro, :numero, :complemento, :bairro, :cidade, :uf)");

    $cadastrarPessoa->bindValue(':nome', $nome);
    $cadastrarPessoa->bindValue(':cpf', $cpf);
    $cadastrarPessoa->bindValue(':telefone', $telefone);
    $cadastrarPessoa->bindValue(':email', $email);
    $cadastrarPessoa->bindValue(':cep', $cep);
    $cadastrarPessoa->bindValue(':logradouro', $logradouro);
    $cadastrarPessoa->bindValue(':numero', $numero);
    $cadastrarPessoa->bindValue(':complemento', $complemento);
    $cadastrarPessoa->bindValue(':bairro', $bairro);
    $cadastrarPessoa->bindValue(':cidade', $cidade);
    $cadastrarPessoa->bindValue(':uf', $uf);

    $cadastrarPessoa->execute();
    $pessoaId = $conexao->lastInsertId('pessoas_id_seq');

    $cadastraPaciente = $conexao->prepare("INSERT INTO pacientes (pessoa_id, convenio_id, data_nascimento, observacoes) 
    VALUES (:pessoa_id, :convenio_id, :data_nascimento, :observacoes)");

    $cadastraPaciente->bindValue(':pessoa_id', $pessoaId);
    $cadastraPaciente->bindValue(':convenio_id', $convenioId);
    $cadastraPaciente->bindValue(':data_nascimento', $data_nascimento);
    $cadastraPaciente->bindValue(':observacoes', $observacoes);

    $cadastraPaciente->execute();

    header('Location: cadastrar-paciente.php?status=sucesso');
    exit;

}   catch (PDOException $codigoBanco) {
    if ($codigoBanco->getCode() == '23505') {
        header('Location: cadastrar-paciente.php?status=duplicado');
        exit;
    }
    error_log('Erro ao cadastrar paciente: ' . $codigoBanco->getMessage());
    header('Location: cadastrar-paciente.php?status=erro');
    exit;
}

?>