<?php
    header('Content-Type: application/json');

    require 'db_connect.php';

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

?>