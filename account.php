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
    <title>Diet tracker</title>
</head>
<body> 

    <div class="container-fluid mt-1">
        <div class="row">
            <div class="col-12 d-flex justify-content-start">
                <a type="button" href="index.php" class="btn btn-secondary">Go back</a>
            </div>
        </div>

        <div class="row">
            <div class="col-12 d-flex justify-content-center">
                <button type="button" id="buttonLogoutUser" class="btn btn-danger">Log out</button>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
    <script src="js/app.js"></script>
</body>
</html>