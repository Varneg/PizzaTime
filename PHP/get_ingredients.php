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

    try {
        $pdo = new PDO($dsn, $user, $pass, $options);
        $stmt = $pdo->query("SELECT IDIngredient, NameIngredient FROM ingredient");
        $ingredient = $stmt->fetchAll();

        echo json_encode($ingredient);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
?>