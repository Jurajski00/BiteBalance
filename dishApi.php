<?php
session_start();
header('Content-type: application/json');
require 'db.php';

$id_user = $_SESSION['id_user'] ?? 1;
$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch($action) {

    case 'searchProducts':
        $term = trim($_GET['term'] ?? '');
        if (empty($term)) {
            echo json_encode([]);
            exit;
        }
        try {
            $stmt = $pdo->prepare("SELECT id, name, energy FROM products WHERE name LIKE :term AND archived = 0 LIMIT 10");
            $stmt->execute([':term' => '%' . $term . '%']);
            echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        } catch (PDOException $e) {
            echo json_encode([]);
        }
        exit;

    case 'getDishes':
        try {
            $sql = "SELECT d.id, d.name, d.description, d.photo,
                           IFNULL(SUM((p.energy * dp.weight) / 100), 0) as total_kcal,
                           IFNULL(SUM((p.protein * dp.weight) / 100), 0) as total_protein,
                           IFNULL(SUM((p.fat * dp.weight) / 100), 0) as total_fat,
                           IFNULL(SUM((p.carbohydrates * dp.weight) / 100), 0) as total_carbs
                    FROM dishes d
                    LEFT JOIN dish_products dp ON d.id = dp.id_dish
                    LEFT JOIN products p ON dp.id_product = p.id
                    WHERE d.id_user = :id_user AND d.archived = 0
                    GROUP BY d.id
                    ORDER BY d.id DESC";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':id_user' => $id_user]);
            echo json_encode(['success' => true, 'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;

    case 'getDishIngredients':
        $dish_id = intval($_GET['dish_id'] ?? 0);
        try {
            $sql = "SELECT dp.id_product, dp.weight, p.name, p.energy 
                    FROM dish_products dp
                    JOIN products p ON dp.id_product = p.id
                    WHERE dp.id_dish = :id_dish";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':id_dish' => $dish_id]);
            echo json_encode(['success' => true, 'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;

    case 'addDish':
    case 'editDish':
        $id = intval($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $ingredientsList = json_decode($_POST['ingredients'] ?? '[]', true);

        if (empty($name) || empty($ingredientsList)) {
            echo json_encode(['success' => false, 'message' => 'Recipe validation rules broken: Name and ingredients are required.']);
            exit;
        }

        $photoName = null;
        if ($action === 'editDish') {
            $stmtImg = $pdo->prepare("SELECT photo FROM dishes WHERE id = :id AND id_user = :id_user");
            $stmtImg->execute([':id' => $id, ':id_user' => $id_user]);
            $photoName = $stmtImg->fetchColumn() ?: null;
        }

        // Process file uploader multi-part binary stream if available
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['photo']['tmp_name'];
            $fileName = $_FILES['photo']['name'];
            $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

            if (in_array($fileExtension, ['jpg', 'jpeg', 'png', 'webp'])) {
                $photoName = uniqid('dish_', true) . '.' . $fileExtension;
                $uploadFileDir = __DIR__ . '/uploads/photos/';
                
                if (!is_dir($uploadFileDir)) {
                    mkdir($uploadFileDir, 0777, true);
                }

                if (!move_uploaded_file($fileTmpPath, $uploadFileDir . $photoName)) {
                    echo json_encode(['success' => false, 'message' => 'System folder asset file migration failure context block lock.']);
                    exit;
                }
            }
        }

        try {
            $pdo->beginTransaction();

            if ($action === 'addDish') {
                $sql = "INSERT INTO dishes (id_user, name, description, photo, archived) VALUES (:id_user, :name, :description, :photo, 0)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':id_user' => $id_user,
                    ':name' => $name,
                    ':description' => !empty($description) ? $description : null,
                    ':photo' => $photoName
                ]);
                $targetDishId = $pdo->lastInsertId();
            } else {
                $targetDishId = $id;
                $sql = "UPDATE dishes SET name = :name, description = :description, photo = :photo WHERE id = :id AND id_user = :id_user";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':name' => $name,
                    ':description' => !empty($description) ? $description : null,
                    ':photo' => $photoName,
                    ':id' => $targetDishId,
                    ':id_user' => $id_user
                ]);


                $delStmt = $pdo->prepare("DELETE FROM dish_products WHERE id_dish = :id_dish");
                $delStmt->execute([':id_dish' => $targetDishId]);
            }

            $insertIngredientSql = "INSERT INTO dish_products (id_dish, id_product, weight) VALUES (:id_dish, :id_product, :weight)";
            $ingredientInsertStmt = $pdo->prepare($insertIngredientSql);

            foreach ($ingredientsList as $ing) {
                $ingredientInsertStmt->execute([
                    ':id_dish' => $targetDishId,
                    ':id_product' => intval($ing['id']),
                    ':weight' => floatval($ing['weight'])
                ]);
            }

            $pdo->commit();
            echo json_encode(['success' => true, 'message' => 'Recipe saved successfully!']);

        } catch (Exception $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            echo json_encode(['success' => false, 'message' => 'Transaction failed: ' . $e->getMessage()]);
        }
        exit;

    case 'deleteDish':
        $id = intval($_POST['id'] ?? 0);
        try {
            $sql = "UPDATE dishes SET archived = 1 WHERE id = :id AND id_user = :id_user";
            $stmt = $pdo->prepare($sql);
            $result = $stmt->execute([':id' => $id, ':id_user' => $id_user]);
            echo json_encode(['success' => $result]);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;

    default:
        echo json_encode(['success' => false, 'message' => 'Unknown action']);
        exit;
}