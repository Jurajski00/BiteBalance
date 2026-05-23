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
    <nav class="navbar navbar-light bg-white border-bottom px-4">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold text-success" href="#">🍎 Diet tracker</a>
            <div class="ms-auto">
                <a href="#" class="btn btn-outline-secondary btn-sm">My account</a>
            </div>
        </div>
    </nav>

    <div class="bg-light py-2 shadow-sm">
        <ul class="nav nav-pills justify-content-center">
            <li class="nav-item"><a class="nav-link active" href="#">Mon</a></li>
            <li class="nav-item"><a class="nav-link" href="#">Tue</a></li>
            <li class="nav-item"><a class="nav-link" href="#">Wed</a></li>
            <li class="nav-item"><a class="nav-link" href="#">Thu</a></li>
            <li class="nav-item"><a class="nav-link" href="#">Fri</a></li>
            <li class="nav-item"><a class="nav-link" href="#">Sat</a></li>
            <li class="nav-item"><a class="nav-link" href="#">Sun</a></li>
        </ul>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>
</html>