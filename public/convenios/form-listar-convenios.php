<?php 
require '../../config/conexaoBanco.php';

$dadosConvenio = $conexao->query("
    SELECT
        id, nome, codigo, ativo 
    FROM convenios
");

$convenios = $dadosConvenio->fetchAll();

?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <title>Convênios</title>
</head>

<body class="bg-light">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js">
    </script>
    <div class="container mt-4">


        <?php if (isset($_GET['sucesso']) && $_GET['sucesso'] == 'convenio-atualizado') : ?>
        <div class="alert alert-success">
            <i class="bi bi-check-circle-fill"></i> Convênio atualizado com sucesso.
        </div>
        <?php endif; ?>

        <?php if (isset($_GET['sucesso']) && $_GET['sucesso'] == 'convenio-removido') : ?>
        <div class="alert alert-success">
            <i class="bi bi-check-circle-fill"></i> Convênio Removido.
        </div>
        <?php endif; ?>

        <?php if (isset($_GET['erro']) && $_GET['erro'] == 'impossivel-remover') : ?>
        <div class="alert alert-danger">
            <i class="bi bi-exclamation-triangle-fill"></i> Não foi possível remover o Convênio.
        </div>
        <?php endif; ?>

        <?php if (isset($_GET['erro']) && $_GET['erro'] == 'vinculo-existente') : ?>
        <div class="alert alert-danger">
            <i class="bi bi-exclamation-triangle-fill"></i> Não foi possível remover: Convênio possui vínculos no
            sistema.
        </div>
        <?php endif; ?>

        <h1 class="mb-3">Convênios</h1>
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Codigo</th>
                    <th>Status Convênio</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($convenios as $convenio):?>
                <tr>
                    <td><?php echo $convenio['id'];?></td>
                    <td><?php echo $convenio['nome'];?></td>
                    <td><?php echo $convenio['codigo'];?></td>
                    <td><?php echo $convenio['ativo'] ? 'Ativo' : 'Inativo'; ?></td>
                    <td>
                        <a href="form-alterar-convenio.php?id=<?php echo $convenio['id']; ?>"
                            class="btn btn-warning btn-sm">Alterar</a>
                        <a href="excluirConvenio.php?id=<?php echo $convenio['id']; ?>"
                            class=" btn btn-danger btn-sm">Excluir</a>
                    </td>
                </tr>
                <?php endforeach;?>
            </tbody>
        </table>
    </div>
</body>

</html>