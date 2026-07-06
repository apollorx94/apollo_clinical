<?php 

require '../../config/conexaoBanco.php';

$idPaciente = $_GET['id'];

try {
    
    $alterarPaciente = $conexao->prepare("SELECT
        pac.id,
        pes.nome,
        pes.cpf,
        pes.email,
        pes.cep,
        pes.telefone,
        pes.logradouro,
        pes.numero,
        pes.complemento,
        pes.bairro,
        pes.cidade,
        pes.uf,
        pac.observacoes,
        pac.convenio_id,
        pac.data_nascimento,
        conv.nome AS convenio_nome
    FROM pacientes pac
    JOIN pessoas pes ON pes.id = pac.pessoa_id
    LEFT JOIN convenios conv ON conv.id = pac.convenio_id WHERE pac.id = :id;");

    $alterarPaciente->bindValue(':id', $idPaciente);
    $alterarPaciente->execute();

    $paciente = $alterarPaciente->fetch();

}
    catch (PDOException $erro) {
    error_log('Erro ao buscar paciente: ' . $erro->getMessage());
    die('Erro ao carregar dados do paciente.');
}

$dadosConvenio = $conexao->query("SELECT id, nome FROM convenios");
$convenios = $dadosConvenio->fetchAll();


?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <title>Cadastro de Pacientes</title>
</head>

<body class="bg-light">
    <!-- BootStrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js">
    </script>

    <!-- Inicio do formulario -->

    <div class="container mt-4">

        <?php if (isset($_GET['status']) && $_GET['status'] == 'erro') :?>
        <div class="alert alert-danger">
            <i class="bi bi-exclamation-triangle"></i> Preencha os campos obrigatorios!
        </div>
        <?php endif;?>

        <?php if (isset($_GET['status']) && $_GET['status'] == 'duplicado') : ?>
        <div class="alert alert-danger">
            <i class="bi bi-exclamation-triangle"></i> Este CPF já foi cadastrado!
        </div>
        <?php endif; ?>

        <?php if (isset($_GET['status']) && $_GET['status'] == 'sucesso') : ?>

        <div class="alert alert-success">
            <i class="bi bi-check-circle"></i> Paciente cadastrado com sucesso!
        </div>
        <?php endif; ?>


        <div class="mb-4">
            <h2 class="fw-bold"><i class="bi bi-person-add"></i> Edição de Paciente</h2>
            <p class="text-muted">Preencha os campos abaixo para o cadastro do paciente</p>
        </div>
        <form action="editarPaciente.php" method="post">
            <div class="mb-3">
                <label class="form-label">Nome:</label>
                <input type="text" name="nome" value="<?php echo $paciente['nome']; ?>" class="form-control">
            </div>
            <div class="mb-3">
                <label class="form-label">CPF:</label>
                <input type="text" name="cpf" value="<?php echo $paciente['cpf']; ?> " class="form-control">
            </div>
            <div class="mb-3">
                <label class="form-label">Telefone:</label>
                <input type="text" name="telefone" value="<?php echo $paciente['telefone']; ?>" class="form-control">
            </div>
            <div class="mb-3">
                <label class="form-label">E-mail:</label>
                <input type="email" name="email" value="<?php echo $paciente['email']; ?>" class="form-control">
            </div>
            <div class="mb-3">
                <label class="form-label">Data de Nascimento:</label>
                <input type="date" name="data_nascimento" value="<?php echo $paciente['data_nascimento']; ?>"
                    class="form-control">
            </div>
            <div class="card shadow-sm mb-3">
                <div class="card-header">
                    <i class="bi bi-geo-alt-fill"></i>
                    Endereço
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-8">
                            <label class="form-label">Rua</label>
                            <input type="text" name="logradouro" class="form-control"
                                value="<?php echo $paciente['logradouro']; ?>" placeholder="Nome da Rua">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Número</label>
                            <input type="text" name="numero" value="<?php echo $paciente['numero']; ?>"
                                class="form-control" placeholder="123">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="form-label">Complemento</label>
                            <input type="text" name="complemento" value="<?php echo $paciente['complemento']; ?>"
                                class="form-control" placeholder="Apartamento, Sala, Bloco...">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-5">
                            <label class="form-label">Bairro</label>
                            <input type="text" name="bairro" value="<?php echo $paciente['bairro']; ?>"
                                class="form-control">
                        </div>

                        <div class="col-md-5">
                            <label class="form-label">Cidade</label>
                            <input type="text" name="cidade" value="<?php echo $paciente['cidade']; ?>"
                                class="form-control">
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">UF</label>
                            <input type="text" name="uf" maxlength="2" value="<?php echo $paciente['uf']; ?>"
                                class="form-control text-uppercase">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3">
                            <label class="form-label">CEP</label>
                            <input type="text" name="cep" value="<?php echo $paciente['cep']; ?>" class="form-control"
                                placeholder="00000-000">
                        </div>
                    </div>

                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Convenio:</label>
                <select name="convenio_id" class="form-select">
                    <?php foreach ($convenios as $convenio) :?>
                    <option value="<?php echo $convenio['id'];?>"><?php echo $convenio['nome'];?>
                    </option>
                    <?php endforeach;?>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Observações</label>
                <textarea name="observacoes" class="form-control"><?php echo $paciente['observacoes']; ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary mb-3">Salvar Alterações</button>
        </form>
    </div>
</body>

</html>