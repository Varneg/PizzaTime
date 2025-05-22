<?php
    header('Content-Type: application/json');

    require 'db_connect.php';
        
    $stmt = $pdo->query("SELECT IDDish, NameDish, Price, img FROM dish");
    $data = $stmt->fetchAll();

    echo json_encode($data);
?>