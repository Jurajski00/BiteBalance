<?php
session_start();
header('Content-type: application/json');
require 'db.php';

$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch($action) {
    case 'registerUser':
        $name = isset($_POST['registerName']) ? trim($_POST['registerName']) : '';
        $surname = isset($_POST['registerSurname']) ? trim($_POST['registerSurname']) : '';
        $email = isset($_POST['registerEmail']) ? trim($_POST['registerEmail']) : '';
        $username = isset($_POST['registerUsername']) ? trim($_POST['registerUsername']) : '';
        $password = isset($_POST['registerPassword']) ? password_hash(trim($_POST['registerPassword']), PASSWORD_DEFAULT) : '';
        $weight = isset($_POST['registerWeight']) ? intval($_POST['registerWeight']) : '';
        $height = isset($_POST['registerHeight']) ? intval($_POST['registerHeight']) : '';
        $age = isset($_POST['registerAge']) ? intval($_POST['registerAge']) : '';

        if($age > 200) {
            echo json_encode([
                'success' => false,
                'message' => "You're not that old :D"
            ]);
            exit;
        }

        $sql = "INSERT INTO users (name, surname, email, username, password, weight, height, age)
        VALUES (:name, :surname, :email, :username, :password, :weight, :height, :age)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':name' => $name,
            ':surname' => $surname,
            ':email' => $email,
            ':username' => $username,
            ':password' => $password,
            ':weight' => $weight,
            ':height' => $height,
            ':age' => $age
        ]);

        echo json_encode([
            'success' => true,
            'message' => "User {$name} added successfully"
        ]);
        exit;

    case 'loginUser':
        $username = isset($_POST['loginUsername']) ? trim($_POST['loginUsername']) : '';
        $password = isset($_POST['loginPassword']) ? trim($_POST['loginPassword']) : '';
        $stmt = $pdo->prepare("SELECT id, password FROM users WHERE username = :username");
        $stmt->execute([
            ':username' => $username
        ]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($password, $user['password'])) {
            echo json_encode([
                'success' => false,
                'message' => 'Login failed'
                ]);
            exit;
        }

        $_SESSION['id_user'] = $user['id'];
        echo json_encode([
            'success' => true,
            'message' => "User {$username} logged in successfully"
        ]);
        exit;
        
    case 'logoutUser':
        session_unset();
        session_destroy();
        echo json_encode([
            'success' => true,
            'message' => 'User logged out'
        ]);
        exit;

    case 'updateProfile':
        $id_user = $_SESSION['id_user'] ?? 0;
        if (!$id_user) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized user session context.']);
            exit;
        }

        $name = trim($_POST['name'] ?? '');
        $surname = trim($_POST['surname'] ?? '');
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $weight = floatval($_POST['weight'] ?? 0);
        $height = intval($_POST['height'] ?? 0);
        $age = intval($_POST['age'] ?? 0);

        if (empty($name) || empty($surname) || empty($username) || empty($email) || !$weight || !$height || !$age) {
            echo json_encode(['success' => false, 'message' => 'All profile informational parameters are required fields.']);
            exit;
        }

        if ($age > 200) {
            echo json_encode(['success' => false, 'message' => "You're not that old :D"]);
            exit;
        }

        try {
            $stmt = $pdo->prepare("SELECT id FROM users WHERE (username = :username OR email = :email) AND id != :id LIMIT 1");
            $stmt->execute([':username' => $username, ':email' => $email, ':id' => $id_user]);
            if ($stmt->fetch()) {
                echo json_encode(['success' => false, 'message' => 'Username or email address parameters are already in use.']);
                exit;
            }

            $update = $pdo->prepare("UPDATE users SET name = :name, surname = :surname, username = :username, email = :email, weight = :weight, height = :height, age = :age WHERE id = :id");
            $update->execute([
                ':name' => $name,
                ':surname' => $surname,
                ':username' => $username,
                ':email' => $email,
                ':weight' => $weight,
                ':height' => $height,
                ':age' => $age,
                ':id' => $id_user
            ]);

            echo json_encode(['success' => true, 'message' => 'Profile information parameter updates applied successfully.']);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;

    case 'changePassword':
        $id_user = $_SESSION['id_user'] ?? 0;
        if (!$id_user) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized user session context.']);
            exit;
        }

        $current = $_POST['current'] ?? '';
        $new = $_POST['new'] ?? '';
        $confirm = $_POST['confirm'] ?? '';

        if (empty($current) || empty($new) || empty($confirm)) {
            echo json_encode(['success' => false, 'message' => 'All password entries are required fields.']);
            exit;
        }

        if ($new !== $confirm) {
            echo json_encode(['success' => false, 'message' => 'New password configuration values mismatch.']);
            exit;
        }

        try {
            $stmt = $pdo->prepare("SELECT password FROM users WHERE id = :id LIMIT 1");
            $stmt->execute([':id' => $id_user]);
            $user = $stmt->fetch();

            if (!$user || !password_verify($current, $user['password'])) {
                echo json_encode(['success' => false, 'message' => 'Incorrect original account password verification value.']);
                exit;
            }

            $newHashed = password_hash($new, PASSWORD_DEFAULT);
            $update = $pdo->prepare("UPDATE users SET password = :password WHERE id = :id");
            $update->execute([':password' => $newHashed, ':id' => $id_user]);

            echo json_encode(['success' => true, 'message' => 'Account password parameter configuration updated successfully.']);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;

    case 'adminUpdateUser':
        $admin_id = $_SESSION['id_user'] ?? 0;
        $check = $pdo->prepare("SELECT is_admin FROM users WHERE id = :id LIMIT 1");
        $check->execute([':id' => $admin_id]);
        $adm = $check->fetch();
        if (!$adm || (int)$adm['is_admin'] !== 1) {
            echo json_encode(['success' => false, 'message' => 'Restricted clearance token. Access denied.']);
            exit;
        }

        $target_id = intval($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $surname = trim($_POST['surname'] ?? '');
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $weight = floatval($_POST['weight'] ?? 0);
        $height = intval($_POST['height'] ?? 0);
        $age = intval($_POST['age'] ?? 0);
        $is_admin = isset($_POST['is_admin']) && $_POST['is_admin'] === '1' ? 1 : 0;

        if (!$target_id || empty($name) || empty($surname) || empty($username) || empty($email) || !$weight || !$height || !$age) {
            echo json_encode(['success' => false, 'message' => 'All validation update values are mandatory.']);
            exit;
        }

        try {
            $duplicate = $pdo->prepare("SELECT id FROM users WHERE (username = :username OR email = :email) AND id != :id LIMIT 1");
            $duplicate->execute([':username' => $username, ':email' => $email, ':id' => $target_id]);
            if ($duplicate->fetch()) {
                echo json_encode(['success' => false, 'message' => 'Target username or email configurations are already taken.']);
                exit;
            }

            $upd = $pdo->prepare("UPDATE users SET name = :name, surname = :surname, username = :username, email = :email, weight = :weight, height = :height, age = :age, is_admin = :is_admin WHERE id = :id");
            $upd->execute([
                ':name' => $name,
                ':surname' => $surname,
                ':username' => $username,
                ':email' => $email,
                ':weight' => $weight,
                ':height' => $height,
                ':age' => $age,
                ':is_admin' => $is_admin,
                ':id' => $target_id
            ]);

            echo json_encode(['success' => true, 'message' => 'User registry modification parameters applied successfully.']);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;

    case 'adminDeleteUser':
        $admin_id = $_SESSION['id_user'] ?? 0;
        $check = $pdo->prepare("SELECT is_admin FROM users WHERE id = :id LIMIT 1");
        $check->execute([':id' => $admin_id]);
        $adm = $check->fetch();
        if (!$adm || (int)$adm['is_admin'] !== 1) {
            echo json_encode(['success' => false, 'message' => 'Restricted clearance token. Access denied.']);
            exit;
        }

        $target_id = intval($_POST['id'] ?? 0);
        if ($target_id === $admin_id) {
            echo json_encode(['success' => false, 'message' => 'Self-deletion protocols are blocked for preservation purposes.']);
            exit;
        }

        try {
            $del = $pdo->prepare("UPDATE users SET archived = 1 WHERE id = :id");
            $del->execute([':id' => $target_id]);
            echo json_encode(['success' => true, 'message' => 'Target entry data records isolated successfully.']);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;

    default:
        echo json_encode([
            'success' => false,
            'message' => 'Unknown api action'
        ]);
        exit;
}