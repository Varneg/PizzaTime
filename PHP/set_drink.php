<?php
    header('Content-Type: application/json');

    require 'db_connect.php';

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

?>