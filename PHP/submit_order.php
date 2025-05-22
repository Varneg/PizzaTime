<?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    header('Content-Type: application/json');

    require 'db_connect.php';
    
        $pdo = new PDO($dsn, $user, $pass, $options);

        $data = json_decode(file_get_contents('php://input'), true);

        if (!isset($data['basket']) || empty($data['basket'])) {
            error_log("Basket отсутствует или пустой");
            echo json_encode(["error" => "Basket is missing or invalid"]);
            exit;
        } 

        $stmt = $pdo->prepare("INSERT INTO orders (IDStatus, DateOrder) VALUES (?, ?)" );
        $stmt->execute([1, date('Y-m-d H:i:s')]);

        $IDOrder = $pdo->lastInsertId();

        foreach ($data['basket'] as $item) {

            if ($item['type'] === 'pizza') {
                $stmt = $pdo->prepare("INSERT INTO orderpizza (IDOrder, IDPizza, IDSize) VALUES (?, ?, ?)");
                $stmt->execute([$IDOrder, $item['id'], $item['size']]);
            
                $IDOrderPizza = $pdo->lastInsertId();
            
                foreach($item['ingredients'] as $ingredient){
                    $stmt = $pdo->prepare("INSERT INTO ordercomposition (IDOrderPizza, IDIngredient, Quantity) VALUES (?, ?, ?)");
                    $stmt->execute([$IDOrderPizza, $ingredient['IDIngredient'], $ingredient['Quantity']]);
                }
            } elseif ($item['type'] === 'drink') {
                $stmt = $pdo->prepare("INSERT INTO orderdish (IDOrderDish, IDOrder) VALUES (?, ?)");
                $stmt->execute([$item['id'], $IDOrder]);
            }


            error_log("Item: " . print_r($item, true)); 
        }

        echo json_encode(['status' => 'OK']);

?>
