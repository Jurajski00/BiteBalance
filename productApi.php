<?php
session_start();
header('Content-type: application/json');
require 'db.php';

$id_user = $_SESSION['id_user'] ?? 1;

$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch($action) {

    case 'getTypes':
        try {
            $sql = "SELECT id, name FROM types WHERE archived = 0 ORDER BY id ASC";
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            $types = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode([
                'success' => true,
                'data' => $types
            ]);
        } catch (PDOException $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Failed to load categories: ' . $e->getMessage()
            ]);
        }
        exit;

    case 'getProducts':
        try {
            $search = trim($_GET['search'] ?? '');
            $type_id = intval($_GET['type_id'] ?? 0);

            $sql = "SELECT p.*, t.name AS type_name 
                    FROM products p
                    LEFT JOIN types t ON p.id_type = t.id
                    WHERE p.id_user = :id_user AND p.archived = 0";
            
            $params = [':id_user' => $id_user];

            if ($search !== '') {
                $sql .= " AND p.name LIKE :search";
                $params[':search'] = $search . '%';
            }

            if ($type_id > 0) {
                $sql .= " AND p.id_type = :type_id";
                $params[':type_id'] = $type_id;
            }

            $sql .= " ORDER BY p.id DESC";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode([
                'success' => true,
                'data' => $products
            ]);
        } catch (PDOException $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Failed to fetch products: ' . $e->getMessage()
            ]);
        }
        exit;

    case 'addProduct':
    case 'editProduct':
        $id = intval($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $id_type = intval($_POST['id_type'] ?? 0);
        $energy = intval($_POST['energy'] ?? 0);
        $protein = floatval($_POST['protein'] ?? 0.0);
        $fat = floatval($_POST['fat'] ?? 0.0);
        $carbohydrates = floatval($_POST['carbohydrates'] ?? 0.0);
        
        if (empty($name) || $id_type <= 0) {
            echo json_encode([
                'success' => false,
                'message' => 'Missing mandatory properties: Name and Type cannot be blank.'
            ]);
            exit;
        }

        $photoName = null;
        if ($action === 'editProduct') {
            $stmtImg = $pdo->prepare("SELECT photo FROM products WHERE id = :id AND id_user = :id_user");
            $stmtImg->execute([':id' => $id, ':id_user' => $id_user]);
            $photoName = $stmtImg->fetchColumn() ?: null;
        }

        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['photo']['tmp_name'];
            $fileName = $_FILES['photo']['name'];
            $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

            $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];

            if (in_array($fileExtension, $allowedExtensions)) {
                $photoName = uniqid('prod_', true) . '.' . $fileExtension;
                
                $uploadFileDir = __DIR__ . '/uploads/photos/';
                
                if (!is_dir($uploadFileDir)) {
                    mkdir($uploadFileDir, 0777, true); 
                }
                
                $dest_path = $uploadFileDir . $photoName;

                if (!move_uploaded_file($fileTmpPath, $dest_path)) {
                    echo json_encode([
                        'success' => false,
                        'message' => 'System folder permission failure. Ensure the project root allows file modifications.'
                    ]);
                    exit;
                }
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Invalid file extension. Allowed structures: JPG, JPEG, PNG, WEBP.'
                ]);
                exit;
            }
        }

        try {
            if ($action === 'addProduct') {
                $sql = "INSERT INTO products (id_user, id_type, name, energy, protein, fat, carbohydrates, photo, archived) 
                        VALUES (:id_user, :id_type, :name, :energy, :protein, :fat, :carbohydrates, :photo, 0)";
                $params = [
                    ':id_user'       => $id_user,
                    ':id_type'       => $id_type,
                    ':name'          => $name,
                    ':energy'        => $energy,
                    ':protein'       => $protein,
                    ':fat'           => $fat,
                    ':carbohydrates' => $carbohydrates,
                    ':photo'         => $photoName
                ];
            } else {
                $sql = "UPDATE products 
                        SET id_type = :id_type, name = :name, energy = :energy, protein = :protein, 
                            fat = :fat, carbohydrates = :carbohydrates, photo = :photo 
                        WHERE id = :id AND id_user = :id_user";
                $params = [
                    ':id'            => $id,
                    ':id_user'       => $id_user,
                    ':id_type'       => $id_type,
                    ':name'          => $name,
                    ':energy'        => $energy,
                    ':protein'       => $protein,
                    ':fat'           => $fat,
                    ':carbohydrates' => $carbohydrates,
                    ':photo'         => $photoName
                ];
            }
            
            $stmt = $pdo->prepare($sql);
            $result = $stmt->execute($params);

            echo json_encode([
                'success' => true,
                'message' => ($action === 'addProduct') ? 'Product created successfully!' : 'Product updated successfully!'
            ]);

        } catch (PDOException $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Database operation failed: ' . $e->getMessage()
            ]);
        }
        exit;

    case 'deleteProduct':
        $id = intval($_POST['id'] ?? 0);

        if ($id <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid product ID.']);
            exit;
        }

        try {
            $sql = "UPDATE products SET archived = 1 WHERE id = :id AND id_user = :id_user";
            $stmt = $pdo->prepare($sql);
            $result = $stmt->execute([
                ':id' => $id,
                ':id_user' => $id_user
            ]);

            echo json_encode([
                'success' => $result,
                'message' => $result ? 'Product deleted successfully!' : 'Failed to delete product.'
            ]);
        } catch (PDOException $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Database error: ' . $e->getMessage()
            ]);
        }
        exit;

    default:
        echo json_encode([
            'success' => false,
            'message' => 'Unknown api action'
        ]);
        exit;
}