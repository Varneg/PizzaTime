<?php
    header('Content-Type: application/json');

    require 'db_connect.php';

        // Получаем данные из POST
        $name = $_POST['name'] ?? '';
        $price = $_POST['price'] ?? 0;
        $ingredients = isset($_POST['ingredients']) ? json_decode($_POST['ingredients'], true) : [];
        $promo = $_POST['promotional'] ?? null;
        $status = $_POST['status'];

        if($promo == 0 || $promo == 0.00){
            $promo = null;
        }

        if($promo == null){
            $promo = null;
        }
        if (trim($name) === '' || !is_numeric($price) || !is_array($ingredients) || count($ingredients) === 0) {
            echo json_encode(['status' => 'error', 'message' => 'Некоректні дані']);
            exit;
        }

        // Перевіряємо, чи є вже піца з таким ім'ям
        $stmt = $pdo->prepare("SELECT IDPizza, img FROM pizza WHERE NamePizza = ?");
        $stmt->execute([$name]);
        $existingPizza = $stmt->fetch();

        $imgData = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $imgData = file_get_contents($_FILES['image']['tmp_name']);
        }

        if ($existingPizza) {
            // Якщо піца вже існує, оновлюємо її дані
            $pizzaId = $existingPizza['IDPizza'];

            // Якщо надіслано нове зображення, оновлюємо його
            if ($imgData) {
                $stmt = $pdo->prepare("UPDATE pizza SET Price = ?, img = ?, Activ = ?, PromotionalPrice = ? WHERE IDPizza = ?");
                $stmt->execute([$price, $imgData, $status, $promo, $pizzaId]);
            } else {
                $stmt = $pdo->prepare("UPDATE pizza SET Price = ?, Activ = ?, PromotionalPrice = ? WHERE IDPizza = ?");
                $stmt->execute([$price, $status, $promo, $pizzaId]);
            }

            // Видаляємо старі інгредієнти з таблиці composition
            $stmt = $pdo->prepare("DELETE FROM composition WHERE IDPizza = ?");
            $stmt->execute([$pizzaId]);

        } else {
            // Якщо піци немає, додаємо нову
            $stmt = $pdo->prepare("INSERT INTO pizza (NamePizza, Price, img, Activ, PromotionalPrice) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$name, $price, $imgData, $status, $promo]);

            // Отримуємо ID нової піци
            $pizzaId = $pdo->lastInsertId();
        }

        // Додаємо інгредієнти до таблиці composition
        foreach ($ingredients as $ingredientName) {
            // Тут потрібен ID інгредієнта — шукаємо його по імені
            $stmt = $pdo->prepare("SELECT IDIngredient FROM ingredient WHERE NameIngredient = ?");
            $stmt->execute([$ingredientName]);
            $ingredientId = $stmt->fetchColumn();

            if ($ingredientId) {
                $stmt = $pdo->prepare("INSERT INTO composition (IDPizza, IDIngredient) VALUES (?, ?)");
                $stmt->execute([$pizzaId, $ingredientId]);
            }
        }

        echo json_encode(['status' => 'success', 'message' => 'Піцу збережено']);
?>
