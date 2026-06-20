<?php
require '../../config/conexaoBanco.php';

$dadosPacientes = $conexao->query("
    SELECT 
        pac.id,
        pes.nome,
        pes.cpf,
        pes.telefone,
        pac.data_nascimento,
        conv.nome AS convenio_nome
    FROM pacientes pac
    JOIN pessoas pes ON pes.id = pac.pessoa_id
    LEFT JOIN convenios conv ON conv.id = pac.convenio_id
");
$pacientes = $dadosPacientes->fetchAll();
?>
<!DOCTYPE html>
<html lang="pr-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <title>Lista de Pacientes</title>
</head>

<body class="bg-light">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js">
    </script>


    <div class="container mt-4">

        <?php if (isset($_GET['sucesso']) && $_GET['sucesso'] == 'paciente-removido') :?>
        <div class="alert alert-success">
            <i class="bi bi-check-circle-fill"></i> Paciente Removido.
        </div>
        <?php endif; ?>

        <?php if (isset($_GET['erro']) && $_GET['erro'] == 'impossivel-remover') : ?>
        <div class="alert alert-danger">
            <i class="bi bi-exclamation-triangle-fill"></i> Não foi possível remover.
        </div>
        <?php endif; ?>

        <?php if (isset($_GET['erro']) && $_GET['erro'] == 'vinculo-existente') : ?>
        <div class="alert alert-danger">
            <i class="bi bi-exclamation-triangle-fill"></i> Não foi possível remover: paciente possui vínculos no
            sistema.
        </div>
        <?php endif; ?>

        <h1 class="mb-3">Pacientes</h1>
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>CPF</th>
                    <th>Telefone</th>
                    <th>Data de Nascimento</th>
                    <th>Convenio</th>
                    <th>Ações</th>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($pacientes as $paciente) : ?>
                <tr>
                    <td><?php echo $paciente['nome']; ?></td>
                    <td><?php echo $paciente['cpf']; ?></td>
                    <td><?php echo $paciente['telefone']; ?></td>
                    <td><?php echo $paciente['data_nascimento']; ?></td>
                    <td><?php echo $paciente['convenio_nome']; ?></td>
                    <td>
                        <a href="alterarPaciente.php?id=<?php echo $paciente['id'];?>"
                            class="btn btn-warning btn-sm">Alterar</a>
                        <a href="excluirPaciente.php?id=<?php echo $paciente['id'];?>"
                            class=" btn btn-danger btn-sm">Excluir</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>

</html>