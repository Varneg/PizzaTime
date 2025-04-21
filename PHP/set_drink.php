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
        $imgData = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $imgData = file_get_contents($_FILES['image']['tmp_name']);
        }

        $stmt = $pdo->prepare("SELECT IDDish FROM dish WHERE NameDish = ?");
        $stmt->execute([$name]);
        $existingDish = $stmt->fetch();

        if($existingDish){
            $IDDish = $existingDish['IDDish'];

            $stmt = $pdo->prepare("UPDATE dish SET img = ?, Price = ? WHERE IDDish = ?");
            $stmt->execute([$imgData, $price, $IDDish]);
        }else{
            $stmt = $pdo->prepare("INSERT INTO dish (NameDish, img, Price) VALUES (?, ?, ?)");
            $stmt->execute([$name, $imgData, $price]);
        }

    }catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Помилка: ' . $e->getMessage()]);
    }
?>