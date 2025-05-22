<?php
    header('Content-Type: application/json');

    require 'db_connect.php';
    
        $data = json_decode(file_get_contents("php://input"), true);
        $orderId = $data['orderId'];
        $status = $data['status'];


        $stmt = $pdo->prepare('SELECT IDStatus FROM Statuso WHERE StatusName = ?');
        $stmt->execute([$status]);
        $statusRow = $stmt->fetch();

        if (!$statusRow) {
            echo json_encode(['success' => false, 'error' => 'Статус не знайдено']);
            exit;
        }

        $statusId = $statusRow['IDStatus'];

        $stmt = $pdo->prepare('UPDATE Orders SET IDStatus = ? WHERE IDOrder = ?');
        $stmt->execute([$statusId, $orderId]);

        if($status === 'Завершено'){
            $stmt = $pdo->prepare('UPDATE orders SET DateDelivery = ? WHERE IDOrder = ?');
            $stmt->execute([date('Y-m-d H:i:s'), $orderId]);
        }

        echo json_encode(['success' => true]);

?>