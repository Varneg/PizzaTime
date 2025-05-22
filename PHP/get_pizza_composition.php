<?php
    header('Content-Type: application/json');

    require 'db_connect.php';

    $idPizza = $_GET['id'] ?? 0;

    // Get pizza base info
    $stmt = $pdo->prepare("SELECT IDPizza, NamePizza, Price, PromotionalPrice FROM pizza WHERE IDPizza = ?");
    $stmt->execute([$idPizza]);
    $pizza = $stmt->fetch(PDO::FETCH_ASSOC);

    // Get all ingredients and default quantities
    $stmt = $pdo->prepare("SELECT 
            i.IDIngredient, i.NameIngredient, i.Price, i.GramPerUnit,
            CASE WHEN c.IDIngredient IS NOT NULL THEN 1 ELSE 0 END AS IsBase,
            CASE WHEN c.IDIngredient IS NOT NULL THEN 1 ELSE 0 END AS Quantity
        FROM ingredient i
        LEFT JOIN composition c ON c.IDIngredient = i.IDIngredient AND c.IDPizza = ?
        ");
    $stmt->execute([$idPizza]);
    $ingredients = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "pizza" => $pizza,
        "ingredients" => $ingredients
]);
