<?php 

require '../../config/conexaoBanco.php';

$idConvenio = $_GET['id'];

try {
    
    $alterarConvenio = $conexao->prepare("SELECT
        nome,
        codigo,
        ativo
    FROM convenios 
    WHERE id = :id;");

    $alterarConvenio->bindValue(':id', $idConvenio);
    $alterarConvenio->execute();

    $convenios = $alterarConvenio->fetch();

}
    catch (PDOException $erro) {
    error_log('Erro ao buscar convenio: ' . $erro->getMessage());
    die('Erro ao carregar dados do Convenio.');
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <title>Convenios</title>
</head>

<body class="bg-light">
    <!-- BootStrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js">
    </script>

    <div class="container mt-4">


        <?php if (isset($_GET['status']) && $_GET['status'] == 'sucesso'):?>
        <div class="alert alert-success">
            <i class="bi bi-check-circle"></i> Convenio atualizado com sucesso!
        </div>
        <?php endif;?>

        <?php if (isset($_GET['status']) && $_GET['status'] == 'duplicado'):?>
        <div class="alert alert-danger">
            <i class="bi bi-exclamation-triangle"></i> Este codigo ja esta em uso!
        </div>
        <?php endif;?>

        <div class="mb-4">
            <h2 class="fw-bold"><i class="bi bi-bandaid"></i> Editar Convênio</h2>
            <p class="text-muted">Edite os campos abaixo do convenio</p>
        </div>

        <!-- Inicio do formulario -->

        <form action="editarConvenio.php" method="post">
            <div class="mb-3">
                <label class="form-label">Nome:</label>
                <input type="text" name="nome" value="<?php echo $convenios['nome'];?>" class="form-control">
            </div>
            <div class="mb-3">
                <label class="form-label">Codigo:</label>
                <input type="text" name="codigo" value="<?php echo $convenios['codigo'];?>" class="form-control">
            </div>
            <div class="mb-3">
                <label class="form-label">Status Convenio:</label>
                <select name="ativo" class="form-select">
                    <option value="1" <?php echo $convenios['ativo'] ? 'selected' : ''; ?>>Ativo</option>
                    <option value="0" <?php echo $convenios['ativo'] ? '' : 'selected'; ?>>Inativo</option>
                </select>
            </div>
            <input type="hidden" name="id_convenio" value="<?php echo $idConvenio; ?>">
            <button type="submit" class="btn btn-primary mb-3">Editar</button>
        </form>
    </div>
</body>

</html>