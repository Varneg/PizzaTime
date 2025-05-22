<?php
    header('Content-Type: application/json');

    require 'db_connect.php';
    
        $pdo = new PDO($dsn, $user, $pass, $options);
        $stmt = $pdo->query("SELECT 
                p.IDPizza,
                p.NamePizza,
                p.Price,
                p.Activ,
                p.PromotionalPrice,
                GROUP_CONCAT(i.NameIngredient SEPARATOR ', ') AS ingredients
            FROM pizza p
            LEFT JOIN composition c ON p.IDPizza = c.IDPizza
            LEFT JOIN ingredient i ON c.IDIngredient = i.IDIngredient
            GROUP BY p.IDPizza
        ");

        $ingredient = $stmt->fetchAll();

        echo json_encode($ingredient);
?>