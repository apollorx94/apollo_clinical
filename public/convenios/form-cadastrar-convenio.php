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
            <i class="bi bi-check-circle"></i> Convenio cadastrado com sucesso!
        </div>
        <?php endif;?>

        <?php if (isset($_GET['status']) && $_GET['status'] == 'duplicado'):?>
        <div class="alert alert-danger">
            <i class="bi bi-exclamation-triangle"></i> Este codigo ja esta em uso!
        </div>
        <?php endif;?>

        <div class="mb-4">
            <h2 class="fw-bold"><i class="bi bi-bandaid"></i> Novo Convênio</h2>
            <p class="text-muted">Preencha os campos abaixo para o cadastro de um novo convênio</p>
        </div>

        <!-- Inicio do formulario -->

        <form action="cadastrarConvenio.php" method="post">
            <div class="mb-3">
                <label class="form-label">Nome:</label>
                <input type="text" name="nome" class="form-control">
            </div>
            <div class="mb-3">
                <label class="form-label">Codigo:</label>
                <input type="text" name="codigo" class="form-control">
            </div>
            <div class="mb-3">
                <label class="form-label">Status Convenio:</label>
                <select name="ativo" class="form-select">
                    <option value="1">Ativo</option>
                    <option value="0">Inativo</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary mb-3">Cadastrar</button>
        </form>
    </div>
</body>

</html>