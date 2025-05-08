<?php
    header('Content-Type: application/json');

    require 'db_connect.php';

        // Получаем все заказы
        $stmt = $pdo->query("SELECT o.IDOrder, o.IDStatus, o.DateOrder, o.DateDelivery, s.StatusName
                            FROM orders o
                            JOIN statuso s ON o.IDStatus = s.IDStatus
                            ORDER BY o.DateOrder DESC
        ");
        $orders = $stmt->fetchAll();

        foreach ($orders as &$order) {
            $orderId = $order['IDOrder'];

            // Получаем блюда для этого заказа
            $stmt = $pdo->prepare("SELECT  d.IDDish, d.NameDish, d.Price
                FROM orderdish od
                JOIN dish d ON od.IDOrderDish = d.IDDish
                WHERE od.IDOrder = ?
            ");
            $stmt->execute([$orderId]);
            $order['dishes'] = $stmt->fetchAll();

            // Получаем пиццы для этого заказа
            $stmt = $pdo->prepare("  SELECT op.IDOrderPizza, p.IDPizza, p.NamePizza, p.Price, p.PromotionalPrice
                                            FROM orderpizza op
                                            JOIN pizza p ON op.IDPizza = p.IDPizza
                                            WHERE op.IDOrder = ?
            ");
            $stmt->execute([$orderId]);
            $pizzas = $stmt->fetchAll();

            // Для каждой пиццы — состав
            foreach ($pizzas as &$pizza) {
                $orderPizzaId = $pizza['IDOrderPizza'];

                $stmt = $pdo->prepare("
                    SELECT i.IDIngredient, i.NameIngredient, oc.Quantity, i.Price
                    FROM ordercomposition oc
                    JOIN ingredient i ON oc.IDIngredient = i.IDIngredient
                    WHERE oc.IDOrderPizza = ?
                ");
                $stmt->execute([$orderPizzaId]);
                $pizza['ingredients'] = $stmt->fetchAll();
            }

            $order['pizzas'] = $pizzas;
        }

        echo json_encode($orders);
?>
