<?php
    $accessType = 'public';
    require 'auth.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="data:,">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <title>Diet tracker</title>
</head>
<body class="d-flex justify-content-center align-items-center min-vh-100">
    <div class="container">
        <div class="row mb-3">
            <div class="col-12">
                <h1 class="fw-bold text-success text-center">Login</h1>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-12">

                <form id="formLogin" class="p-4 bg-white rounded-5 shadow-lg">

                    <div class="col-12 mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" id="username" class="form-control" required>
                    </div>

                    <div class="col-12 mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" id="password" class="form-control" required>
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <button type="button" onclick="document.location = 'index.php'" class="btn btn-secondary px-4">Go back</button>
                        </div>

                        <div class="col-6 d-flex justify-content-end">
                            <button type="submit" class="btn btn-success px-4">Login</button>
                        </div>
                    </div>

                </form>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
    <script src="js/app.js"></script>
</body>
</html>