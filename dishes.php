<?php
    $accessType = 'private';
    require 'auth.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="Data:,">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <title>Diet tracker</title>
</head>
<body>

<header class="container-fluid border-bottom px-4 py-2">
    <div class="row align-items-center">
        <div class="col-auto">
            <a class="navbar-brand fs-3 fw-bold text-success" href="#">🍎 Diet tracker</a>
        </div>

        <div class="col-auto ms-auto">
            <a type="button" href="products.php" class="btn btn-outline-success">Add products</a>
        </div>
        
        <div class="col-auto">
            <a type="button" href="dishes.php" class="btn btn-outline-success">Add dishes</a>
        </div>

        <div class="col-auto">
            <a type="button" href="account.php" class="btn btn-success">My account</a>
        </div>
    </div>
</header>

<main>

</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>
</html>