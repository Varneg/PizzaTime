<?php

$merchantAccount = "test_merch_n1";
$merchantSecretKey = "flk3409refn54t54t*FNJRET";

$orderReference = "ORDER_" . time();
$orderDate = time();
$amount = 511;
$currency = "UAH";
$productName = ["Покупка"];
$productCount = [1];
$productPrice = [$amount];

// Подпись
$signatureElements = [
    $merchantAccount,
    "yourdomain.com",
    $orderReference,
    $orderDate,
    $amount,
    $currency,
    $productName[0],
    $productPrice[0],
    $productCount[0]
];
$signature = base64_encode(sha1(implode(";", $signatureElements) . $merchantSecretKey, true));

$data = [
    "transactionType" => "CREATE_INVOICE",
    "apiVersion" => 1,
    "merchantAccount" => $merchantAccount,
    "merchantDomainName" => "yourdomain.com",
    "orderReference" => $orderReference,
    "orderDate" => $orderDate,
    "amount" => $amount,
    "currency" => $currency,
    "productName" => $productName,
    "productCount" => $productCount,
    "productPrice" => $productPrice,
    "clientFirstName" => $_POST['name'] ?? 'Ivan',
    "clientLastName" => "Ivanov",
    "clientEmail" => "test@example.com",
    "clientPhone" => $_POST['phone'] ?? '+380000000000',
    "language" => "ru",
    "merchantSignature" => $signature
];

// Отправляем запрос в WayForPay
$ch = curl_init('https://api.wayforpay.com/api');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
$response = curl_exec($ch);
curl_close($ch);

// Возврат ссылки для перехода на оплату
$responseData = json_decode($response, true);
if (isset($responseData['invoiceUrl'])) {
    header("Location: " . $responseData['invoiceUrl']);
    exit;
} else {
    echo "Ошибка: " . $response;
}
?>