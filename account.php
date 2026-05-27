<?php
    $accessType = 'private';
    require 'auth.php';
    require 'db.php';

    $id_user = $_SESSION['id_user'] ?? 1;
    $user_name = '';
    $user_surname = '';
    $user_email = '';
    $user_username = '';
    $user_weight = '';
    $user_height = '';
    $user_age = '';
    $is_admin = 0;

    try {
        $stmt = $pdo->prepare("SELECT name, surname, email, username, weight, height, age, is_admin FROM users WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id_user]);
        $user = $stmt->fetch();
        if ($user) {
            $user_name = htmlspecialchars($user['name']);
            $user_surname = htmlspecialchars($user['surname']);
            $user_email = htmlspecialchars($user['email']);
            $user_username = htmlspecialchars($user['username']);
            $user_weight = htmlspecialchars($user['weight']);
            $user_height = htmlspecialchars($user['height']);
            $user_age = htmlspecialchars($user['age']);
            $is_admin = (int)$user['is_admin'];
        }
    } catch (PDOException $e) {
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="Data:,">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <title>BiteBalance - My Account</title>
</head>
<body class="bg-light">

<header class="container-fluid border-bottom px-4 py-2 bg-white">
    <div class="row align-items-center">
        <div class="col-auto">
            <a class="navbar-brand fs-3 fw-bold text-success" href="index.php">🍎 BiteBalance</a>
        </div>
        <div class="col-auto ms-auto d-flex gap-2">
            <?php if ($is_admin === 1): ?>
                <a href="admin.php" class="btn btn-danger fw-bold"><i class="bi bi-shield-lock me-1"></i> Control Panel</a>
            <?php endif; ?>
            <a href="index.php" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i> Back to Dashboard</a>
        </div>
    </div>
</header>

<main class="container py-5" style="max-width: 700px;">
    <div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4">
        <div class="d-flex align-items-center gap-3 mb-4">
            <div class="bg-success bg-opacity-10 text-success rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                <i class="bi bi-person-circle fs-2"></i>
            </div>
            <div>
                <h4 class="fw-bold text-dark mb-0"><?= $user_name . ' ' . $user_surname; ?></h4>
                <p class="text-muted mb-0">@<?= $user_username; ?> — <?= $user_email; ?></p>
            </div>
        </div>

        <hr class="text-muted opacity-25">

        <h5 class="fw-bold text-dark mb-3"><i class="bi bi-person-gear me-2 text-success"></i>Update Profile Information</h5>
        <div id="profileAlert" class="alert d-none small" role="alert"></div>

        <form id="formUpdateProfile">
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label for="inputProfileName" class="form-label fw-bold small text-muted">First Name *</label>
                    <input type="text" id="inputProfileName" class="form-control" value="<?= $user_name; ?>" required>
                </div>
                <div class="col-md-6">
                    <label for="inputProfileSurname" class="form-label fw-bold small text-muted">Last Name *</label>
                    <input type="text" id="inputProfileSurname" class="form-control" value="<?= $user_surname; ?>" required>
                </div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label for="inputProfileUsername" class="form-label fw-bold small text-muted">Username *</label>
                    <input type="text" id="inputProfileUsername" class="form-control" value="<?= $user_username; ?>" required>
                </div>
                <div class="col-md-6">
                    <label for="inputProfileEmail" class="form-label fw-bold small text-muted">Email Address *</label>
                    <input type="email" id="inputProfileEmail" class="form-control" value="<?= $user_email; ?>" required>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <label for="inputProfileWeight" class="form-label fw-bold small text-muted">Weight (kg) *</label>
                    <input type="number" step="0.1" id="inputProfileWeight" class="form-control" value="<?= $user_weight; ?>" required>
                </div>
                <div class="col-md-4">
                    <label for="inputProfileHeight" class="form-label fw-bold small text-muted">Height (cm) *</label>
                    <input type="number" id="inputProfileHeight" class="form-control" value="<?= $user_height; ?>" required>
                </div>
                <div class="col-md-4">
                    <label for="inputProfileAge" class="form-label fw-bold small text-muted">Age *</label>
                    <input type="number" id="inputProfileAge" class="form-control" value="<?= $user_age; ?>" required>
                </div>
            </div>

            <button type="submit" class="btn btn-success px-4 fw-bold rounded-pill">Save Profile changes</button>
        </form>
    </div>

    <div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4">
        <h5 class="fw-bold text-dark mb-3"><i class="bi bi-shield-lock me-2 text-success"></i>Change Password</h5>
        <div id="passwordAlert" class="alert d-none small" role="alert"></div>

        <form id="formChangePassword" autocomplete="off">
            <div class="mb-3">
                <label for="inputCurrentPassword" class="form-label fw-bold small text-muted">Current Password *</label>
                <input type="password" id="inputCurrentPassword" class="form-control" autocomplete="new-password" required>
            </div>
            <div class="mb-3">
                <label for="inputNewPassword" class="form-label fw-bold small text-muted">New Password *</label>
                <input type="password" id="inputNewPassword" class="form-control" autocomplete="new-password" minlength="6" required>
            </div>
            <div class="mb-4">
                <label for="inputConfirmPassword" class="form-label fw-bold small text-muted">Confirm New Password *</label>
                <input type="password" id="inputConfirmPassword" class="form-control" autocomplete="new-password" minlength="6" required>
            </div>
            <button type="submit" class="btn btn-success px-4 fw-bold rounded-pill">Update Password</button>
        </form>
    </div>

    <div class="card border-0 shadow-sm rounded-4 p-4 bg-white text-center">
        <h5 class="fw-bold text-dark mb-2">Session Management</h5>
        <p class="text-muted small mb-3">Safely terminate your current session log authentication state token parameters.</p>
        <button type="button" id="buttonLogoutUser" class="btn btn-danger px-4 fw-bold rounded-pill">
            <i class="bi bi-box-arrow-right me-1"></i> Secure Log out
        </button>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
<script src="js/userManagement.js"></script>
</body>
</html>