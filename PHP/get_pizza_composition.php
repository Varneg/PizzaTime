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

    $pdo = new PDO($dsn, $user, $pass, $options);

    $idPizza = $_GET['id'] ?? 0;

    // Get pizza base info
    $stmt = $pdo->prepare("SELECT IDPizza, NamePizza, Price FROM pizza WHERE IDPizza = ?");
    $stmt->execute([$idPizza]);
    $pizza = $stmt->fetch(PDO::FETCH_ASSOC);

    // Get all ingredients and default quantities
    $stmt = $pdo->prepare("
        SELECT 
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
