<?php
    header('Content-Type: application/json');

    $idPizza = $_GET['id'] ?? 0;

    require 'db_connect.php';

    $pdo = new PDO($dsn, $user, $pass, $options);

    $stmt = $pdo->prepare("SELECT 
            p.IDPizza,
            p.NamePizza,
            p.Price,
            p.Activ,
            p.PromotionalPrice,
            GROUP_CONCAT(i.NameIngredient SEPARATOR ', ') AS ingredients
        FROM pizza p
        JOIN composition c ON p.IDPizza = c.IDPizza
        JOIN ingredient i ON c.IDIngredient = i.IDIngredient
        WHERE p.IDPizza = ?
    ");

    $stmt->execute([$idPizza]);

    $ingredient = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($ingredient);
?>