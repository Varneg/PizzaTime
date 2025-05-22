<?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    header('Content-Type: application/json');
    require 'db_connect.php';

    $contactPhone = $_POST['contactPhone'];
    $email = $_POST['email'];
    $openingHours = $_POST['openingHours'];
    $instagram = $_POST['instagram'];
    $telegram = $_POST['telegram'];
    $facebook = $_POST['facebook'];
    $logo = null;

    if (isset($_FILES['logo']) && $_FILES['logo']['tmp_name']) {
        $logo = file_get_contents($_FILES['logo']['tmp_name']);
    }

    $sql = "UPDATE setting SET contactPhone=?, email=?, openingHours=?, instagram=?, telegram=?, facebook=?, logo=?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$contactPhone, $email, $openingHours, $instagram, $telegram, $facebook, $logo]);

    echo json_encode(['success' => true]);
?>