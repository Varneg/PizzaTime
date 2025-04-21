<?php
    header('Content-Type: application/json');

    $host = 'localhost';
    $db   = 'pizzatime';
    $user = 'root';
    $pass = '';
    $charset = 'utf8mb4';

    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ];

    try{

        $pdo = new PDO($dsn, $user, $pass, $options);

        $name = $_POST['name'] ?? '';
        $price = $_POST['price'] ?? '';
        $weight = $_POST['weight'] ?? '';

        $stmt = $pdo->prepare("SELECT IDIngredient FROM ingredient WHERE NameIngredient = ?");
        $stmt->execute([$name]);
        $existingIngredient = $stmt->fetch();

        if($existingIngredient){
            $IDIngredient = $existingIngredient['IDIngredient'];

            $stmt = $pdo->prepare("UPDATE ingredient SET GramPerUnit = ?, Price = ? WHERE IDIngredient = ?");
            $stmt->execute([$weight, $price, $IDIngredient]);
        }else{
            $stmt = $pdo->prepare("INSERT INTO ingredient (NameIngredient, GramPerUnit, Price) VALUES (?, ?, ?)");
            $stmt->execute([$name, $weight, $price]);
        }

    }catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Помилка: ' . $e->getMessage()]);
    }
?>