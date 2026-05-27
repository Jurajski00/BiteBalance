<?php
    $accessType = 'private';
    require 'auth.php';
    require 'db.php';

    $id_user = $_SESSION['id_user'] ?? 0;
    try {
        $checkAdmin = $pdo->prepare("SELECT is_admin FROM users WHERE id = :id LIMIT 1");
        $checkAdmin->execute([':id' => $id_user]);
        $currentUser = $checkAdmin->fetch();
        if (!$currentUser || (int)$currentUser['is_admin'] !== 1) {
            header('Location: index.php');
            exit;
        }
    } catch (PDOException $e) {
        header('Location: index.php');
        exit;
    }

    $users = [];
    try {
        $stmt = $pdo->prepare("SELECT id, name, surname, email, username, weight, height, age, is_admin FROM users WHERE archived = 0 ORDER BY id DESC");
        $stmt->execute();
        $users = $stmt->fetchAll();
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
    <title>Diet tracker - Admin Panel</title>
</head>
<body class="bg-light">

<header class="container-fluid border-bottom px-4 py-2 bg-white">
    <div class="row align-items-center">
        <div class="col-auto">
            <a class="navbar-brand fs-3 fw-bold text-danger" href="admin.php">🛡️ Admin Panel</a>
        </div>
        <div class="col-auto ms-auto">
            <a href="index.php" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i> Back to Dashboard</a>
        </div>
    </div>
</header>

<main class="container-fluid py-5 px-4">
    <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
        <h4 class="fw-bold text-dark mb-4"><i class="bi bi-people me-2 text-danger"></i>User Management Control Console</h4>
        
        <div id="adminAlert" class="alert d-none small" role="alert"></div>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Full Name</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Parameters (W / H / Age)</th>
                        <th>Role</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $u): ?>
                        <tr id="user-row-<?= $u['id']; ?>">
                            <td><span class="badge bg-secondary opacity-75"><?= $u['id']; ?></span></td>
                            <td class="fw-bold text-dark">
                                <span class="txt-name"><?= htmlspecialchars($u['name']); ?></span> 
                                <span class="txt-surname"><?= htmlspecialchars($u['surname']); ?></span>
                            </td>
                            <td>@<span class="txt-username"><?= htmlspecialchars($u['username']); ?></span></td>
                            <td><span class="txt-email"><?= htmlspecialchars($u['email']); ?></span></td>
                            <td class="small text-muted">
                                <span class="txt-weight"><?= htmlspecialchars($u['weight']); ?></span> kg / 
                                <span class="txt-height"><?= htmlspecialchars($u['height']); ?></span> cm / 
                                <span class="txt-age"><?= htmlspecialchars($u['age']); ?></span> yrs
                            </td>
                            <td>
                                <span class="badge <?= $u['is_admin'] ? 'bg-danger' : 'bg-info text-dark'; ?> badge-role">
                                    <?= $u['is_admin'] ? 'Admin' : 'User'; ?>
                                </span>
                            </td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-outline-primary btn-edit-user" data-id="<?= $u['id']; ?>">
                                        <i class="bi bi-pencil"></i> Edit
                                    </button>
                                    <button type="button" class="btn btn-outline-danger btn-delete-user" data-id="<?= $u['id']; ?>">
                                        <i class="bi bi-trash"></i> Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<div class="modal fade" id="modalEditUser" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form id="formAdminEditUser" class="modal-content border-0 shadow rounded-4">
            <div class="modal-header border-bottom-0 pt-4 px-4">
                <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square me-2 text-primary"></i>Modify User Record</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body px-4 py-3">
                <input type="hidden" id="editUserId">
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label small fw-bold text-muted">First Name</label>
                        <input type="text" id="editUserName" class="form-control form-control-sm" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-bold text-muted">Last Name</label>
                        <input type="text" id="editUserSurname" class="form-control form-control-sm" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold text-muted">Username</label>
                    <input type="text" id="editUserUsername" class="form-control form-control-sm" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold text-muted">Email Address</label>
                    <input type="email" id="editUserEmail" class="form-control form-control-sm" required>
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-4">
                        <label class="form-label small fw-bold text-muted">Weight (kg)</label>
                        <input type="number" step="0.1" id="editUserWeight" class="form-control form-control-sm" required>
                    </div>
                    <div class="col-4">
                        <label class="form-label small fw-bold text-muted">Height (cm)</label>
                        <input type="number" id="editUserHeight" class="form-control form-control-sm" required>
                    </div>
                    <div class="col-4">
                        <label class="form-label small fw-bold text-muted">Age</label>
                        <input type="number" id="editUserAge" class="form-control form-control-sm" required>
                    </div>
                </div>
                <div class="form-check form-switch mt-4 p-3 bg-light rounded-3">
                    <input class="form-check-input ms-0 me-2" type="checkbox" role="switch" id="editUserIsAdmin">
                    <label class="form-check-label fw-bold small text-danger" for="editUserIsAdmin">Grant Administrator Access Rights</label>
                </div>
            </div>
            <div class="modal-footer border-top-0 pb-4 px-4">
                <button type="button" class="btn btn-light rounded-pill btn-sm px-3" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary rounded-pill btn-sm px-4 fw-bold">Commit Changes</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
<script src="js/userManagement.js"></script>
</body>
</html>