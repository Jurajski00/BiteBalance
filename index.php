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
                <a type="button" href="account.php" class="btn btn-success">My account</a>
            </div>
        </div>
    </header>

    <nav>
        <div class="container-fluid bg-light py-2 shadow-sm">
            <ul class="nav nav-underline gap-5 fs-4 justify-content-center">
                <li class="nav-item"><a class="nav-link link-success active" href="#">Mon</a></li>
                <li class="nav-item"><a class="nav-link link-success" href="#">Tue</a></li>
                <li class="nav-item"><a class="nav-link link-success" href="#">Wed</a></li>
                <li class="nav-item"><a class="nav-link link-success" href="#">Thu</a></li>
                <li class="nav-item"><a class="nav-link link-success" href="#">Fri</a></li>
                <li class="nav-item"><a class="nav-link link-success" href="#">Sat</a></li>
                <li class="nav-item"><a class="nav-link link-success" href="#">Sun</a></li>
            </ul>
        </div>  
    </nav>

    <main>
        <section>
            <div class="container border rounded-4 py-2 my-3 shadow-sm">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <h2 class="text-success fw-bold mb-0">Breakfast</h2>
                    </div>
                    <div class="col-auto ms-auto">
                        <button type="button" id="buttonAddEntry" class="btn btn-link text-success p-0 border-0">
                            <i class="bi bi-plus-circle-fill fs-2"></i>
                        </button>
                    </div>
                </div>
            </div>
        </section>

        <section>
            <div class="container border rounded-4 py-2 my-3 shadow-sm">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <h2 class="text-success fw-bold mb-0">Lunch</h2>
                    </div>
                    <div class="col-auto ms-auto">
                        <button type="button" id="buttonAddEntry" class="btn btn-link text-success p-0 border-0">
                            <i class="bi bi-plus-circle-fill fs-2"></i>
                        </button>
                    </div>
                </div>
            </div>
        </section>

        <section>
            <div class="container border rounded-4 py-2 my-3 shadow-sm">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <h2 class="text-success fw-bold mb-0">Dinner</h2>
                    </div>
                    <div class="col-auto ms-auto">
                        <button type="button" id="buttonAddEntry" class="btn btn-link text-success p-0 border-0">
                            <i class="bi bi-plus-circle-fill fs-2"></i>
                        </button>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>
</html>