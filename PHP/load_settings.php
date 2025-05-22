<?php
    header('Content-Type: application/json');
    require 'db_connect.php';

    $stmt = $pdo->query("SELECT * FROM setting LIMIT 1");
    echo json_encode($stmt->fetch());
?>