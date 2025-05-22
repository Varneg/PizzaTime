<?php
    header('Content-Type: application/json');

    require 'db_connect.php';

    $stmt = $pdo->query("SELECT IDIngredient, NameIngredient FROM ingredient");
    $ingredient = $stmt->fetchAll();

    echo json_encode($ingredient);
?>