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

        if($age > 200) {                                //test function
            echo json_encode([
                'success' => false,
                'message' => "You're not that old :D"
            ]);
            exit;
        }                                               // add more later

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
        break;

    case 'loginUser':
        $username = isset($_POST['loginUsername']) ? trim($_POST['loginUsername']) : '';
        $password = isset($_POST['loginPassword']) ? trim($_POST['loginPassword']) : '';
        $stmt = $pdo->prepare("SELECT id, password FROM users WHERE username = :username");
        $stmt->execute([
            ':username' => $username
        ]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['id_user'] = $user['id'];
            echo json_encode([
                'success' => true,
                'message' => "User {$username} logged in successfully"
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Login failed'
            ]);
        }
        break;
        
    default:
        echo json_encode([
            'success' => false,
            'message' => 'Unkown api action'
        ]);
        break;
}