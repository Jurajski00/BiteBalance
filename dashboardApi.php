<?php
session_start();
header('Content-type: application/json');
require 'db.php';

$id_user = $_SESSION['id_user'] ?? 1;
$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch($action) {

    case 'getLookupCatalogs':
        try {
            $stmt = $pdo->prepare("SELECT id, name, energy, protein, fat, carbohydrates FROM products WHERE id_user = :id_user AND archived = 0 ORDER BY name ASC");
            $stmt->execute([':id_user' => $id_user]);
            $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $sqlDishes = "SELECT d.id, d.name, IFNULL(SUM((p.energy * dp.weight) / 100), 0) as total_kcal
                          FROM dishes d
                          LEFT JOIN dish_products dp ON d.id = dp.id_dish
                          LEFT JOIN products p ON dp.id_product = p.id
                          WHERE d.id_user = :id_user AND d.archived = 0
                          GROUP BY d.id ORDER BY d.name ASC";
            $stmtD = $pdo->prepare($sqlDishes);
            $stmtD->execute([':id_user' => $id_user]);
            $dishes = $stmtD->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode([
                'success' => true,
                'products' => $products,
                'dishes' => $dishes
            ]);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;

    case 'getEntries':
        $targetDate = $_GET['date'] ?? date('Y-m-d');
        try {
            $sql = "SELECT ue.id, ue.meal_type, ue.weight, ue.id_product, ue.id_dish,
                           p.name AS product_name,
                           d.name AS dish_name,
                           
                           IF(ue.id_product IS NOT NULL, (p.energy * ue.weight) / 100, (dish_stats.tot_kcal * ue.weight) / dish_stats.tot_weight) AS calculated_kcal,
                           IF(ue.id_product IS NOT NULL, (p.protein * ue.weight) / 100, (dish_stats.tot_protein * ue.weight) / dish_stats.tot_weight) AS calculated_protein,
                           IF(ue.id_product IS NOT NULL, (p.fat * ue.weight) / 100, (dish_stats.tot_fat * ue.weight) / dish_stats.tot_weight) AS calculated_fat,
                           IF(ue.id_product IS NOT NULL, (p.carbohydrates * ue.weight) / 100, (dish_stats.tot_carbs * ue.weight) / dish_stats.tot_weight) AS calculated_carbs

                    FROM user_entries ue
                    LEFT JOIN products p ON ue.id_product = p.id
                    LEFT JOIN dishes d ON ue.id_dish = d.id
                    
                    LEFT JOIN (
                        SELECT dp.id_dish, 
                               SUM(dp.weight) as tot_weight,
                               SUM((p2.energy * dp.weight) / 100) as tot_kcal,
                               SUM((p2.protein * dp.weight) / 100) as tot_protein,
                               SUM((p2.fat * dp.weight) / 100) as tot_fat,
                               SUM((p2.carbohydrates * dp.weight) / 100) as tot_carbs
                        FROM dish_products dp
                        JOIN products p2 ON dp.id_product = p2.id
                        GROUP BY dp.id_dish
                    ) dish_stats ON ue.id_dish = dish_stats.id_dish

                    WHERE ue.id_user = :id_user AND ue.log_date = :date
                    ORDER BY ue.id ASC";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([':id_user' => $id_user, ':date' => $targetDate]);
            $entries = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $totals = ['calories' => 0, 'protein' => 0, 'fat' => 0, 'carbohydrates' => 0];
            foreach ($entries as $e) {
                $totals['calories'] += floatval($e['calculated_kcal'] ?? 0);
                $totals['protein'] += floatval($e['calculated_protein'] ?? 0);
                $totals['fat'] += floatval($e['calculated_fat'] ?? 0);
                $totals['carbohydrates'] += floatval($e['calculated_carbs'] ?? 0);
            }

            echo json_encode([
                'success' => true,
                'data' => $entries,
                'totals' => $totals
            ]);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;

    case 'addEntry':
        $date = $_POST['date'] ?? '';
        $meal_type = trim($_POST['meal_type'] ?? '');
        $weight = floatval($_POST['weight'] ?? 0);
        $id_product = isset($_POST['id_product']) && $_POST['id_product'] !== '' ? intval($_POST['id_product']) : null;
        $id_dish = isset($_POST['id_dish']) && $_POST['id_dish'] !== '' ? intval($_POST['id_dish']) : null;

        if (empty($date) || empty($meal_type) || $weight <= 0 || (!$id_product && !$id_dish)) {
            echo json_encode(['success' => false, 'message' => 'Validation error constraints rules broken parameters values empty.']);
            exit;
        }

        try {
            $sql = "INSERT INTO user_entries (id_user, id_product, id_dish, meal_type, weight, log_date) 
                    VALUES (:id_user, :id_product, :id_dish, :meal_type, :weight, :log_date)";
            $stmt = $pdo->prepare($sql);
            $result = $stmt->execute([
                ':id_user' => $id_user,
                ':id_product' => $id_product,
                ':id_dish' => $id_dish,
                ':meal_type' => $meal_type,
                ':weight' => $weight,
                ':log_date' => $date
            ]);
            echo json_encode(['success' => $result]);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;

    case 'deleteEntry':
        $id = intval($_POST['id'] ?? 0);
        try {
            $stmt = $pdo->prepare("DELETE FROM user_entries WHERE id = :id AND id_user = :id_user");
            $result = $stmt->execute([':id' => $id, ':id_user' => $id_user]);
            echo json_encode(['success' => $result]);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;

    default:
        echo json_encode(['success' => false, 'message' => 'Action definition is unknown.']);
        break;
}