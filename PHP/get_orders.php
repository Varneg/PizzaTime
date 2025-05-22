<?php
header('Content-Type: application/json');
require 'db_connect.php';

// Получаем все заказы
$stmt = $pdo->query("SELECT o.IDOrder, o.IDStatus, o.DateOrder, o.DateDelivery, s.StatusName
                    FROM orders o
                    JOIN statuso s ON o.IDStatus = s.IDStatus
                    ORDER BY o.DateOrder DESC");
$orders = $stmt->fetchAll();

foreach ($orders as &$order) {
    $orderId = $order['IDOrder'];

    // Получаем блюда
    $stmt = $pdo->prepare("SELECT d.IDDish, d.NameDish, d.Price
                           FROM orderdish od
                           JOIN dish d ON od.IDOrderDish = d.IDDish
                           WHERE od.IDOrder = ?");
    $stmt->execute([$orderId]);
    $order['dishes'] = $stmt->fetchAll();

    // Получаем пиццы
    $stmt = $pdo->prepare("SELECT op.IDOrderPizza, p.IDPizza, p.NamePizza, p.Price, p.PromotionalPrice, op.IDSize
                           FROM orderpizza op
                           JOIN pizza p ON op.IDPizza = p.IDPizza
                           WHERE op.IDOrder = ?");
    $stmt->execute([$orderId]);
    $pizzas = $stmt->fetchAll();

    foreach ($pizzas as &$pizza) {
        $orderPizzaId = $pizza['IDOrderPizza'];
        $pizzaId = $pizza['IDPizza'];

        // Получаем базовые ингредиенты (composition)
        $stmt = $pdo->prepare("SELECT i.IDIngredient, i.NameIngredient
                               FROM composition c
                               JOIN ingredient i ON c.IDIngredient = i.IDIngredient
                               WHERE c.IDPizza = ?");
        $stmt->execute([$pizzaId]);
        $baseIngredients = $stmt->fetchAll(PDO::FETCH_COLUMN, 1); // только имена

        // Получаем ингредиенты из заказа
        $stmt = $pdo->prepare("SELECT i.IDIngredient, i.NameIngredient, oc.Quantity, i.Price
                               FROM ordercomposition oc
                               JOIN ingredient i ON oc.IDIngredient = i.IDIngredient
                               WHERE oc.IDOrderPizza = ?");
        $stmt->execute([$orderPizzaId]);
        $ingredients = $stmt->fetchAll();

        // Вычитаем 1 из количества, если ингредиент — базовый
        foreach ($ingredients as &$ing) {
            if (in_array($ing['NameIngredient'], $baseIngredients)) {
                $ing['Quantity'] = max(0, $ing['Quantity'] - 1);
            }
        }

        $pizza['ingredients'] = $ingredients;
    }

    $order['pizzas'] = $pizzas;
}

echo json_encode($orders);
?>

