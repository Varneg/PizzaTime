<?php
    header('Content-Type: application/json');

    require 'db_connect.php';
    
        $pdo = new PDO($dsn, $user, $pass, $options);

        $stmt = $pdo->query("SELECT IDStatus, StatusName FROM Statuso ORDER BY IDStatus");
        $statuses = $stmt->fetchAll();

        echo json_encode($statuses);

?>