<?php
    session_start();

    require 'db_connect.php';

    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';


        $stmt = $pdo->prepare("SELECT IDWorker FROM Accounts WHERE Login = ? AND Password = ?");
        $stmt->execute([$username, $password]);
        $user = $stmt->fetch();

        if ($user) {
            $_SESSION['user'] = $user['IDWorker'];
            header("Location: /admin.php"); // путь к защищённой странице
            exit();
        } else {
            // Можно сделать редирект назад с сообщением через сессию или GET параметр
            header("Location: ../login.html?error=1");
            exit();
        }

?>
