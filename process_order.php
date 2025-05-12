<?php
session_start();
require_once 'config.php';

header('Content-Type: application/json');

// Логирование для отладки
error_log('Order attempt from user: ' . ($_SESSION['user_id'] ?? 'guest'));

// Проверка авторизации
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Для оформления тура необходимо авторизоваться'
    ]);
    exit;
}

// Получаем данные
$json = file_get_contents('php://input');
$data = json_decode($json, true);

// Валидация данных
$errors = [];
$requiredFields = ['destination', 'date_from', 'date_to', 'people', 'payment'];

foreach ($requiredFields as $field) {
    if (empty($data[$field])) {
        $errors[] = "Поле {$field} обязательно для заполнения";
    }
}

if (!empty($errors)) {
    echo json_encode([
        'status' => 'error',
        'message' => implode(', ', $errors)
    ]);
    exit;
}

try {
    $stmt = $pdo->prepare("
        INSERT INTO orders 
        (user_id, destination, date_from, date_to, people, payment_method, status, created_at) 
        VALUES (:user_id, :destination, :date_from, :date_to, :people, :payment, 'pending', NOW())
    ");
    
    $stmt->execute([
        ':user_id' => $_SESSION['user_id'],
        ':destination' => $data['destination'],
        ':date_from' => $data['date_from'],
        ':date_to' => $data['date_to'],
        ':people' => $data['people'],
        ':payment' => $data['payment']
    ]);

    // Успешный ответ
    echo json_encode([
        'status' => 'success',
        'message' => 'Тур успешно оформлен! Номер заказа: ' . $pdo->lastInsertId(),
        'order_id' => $pdo->lastInsertId()
    ]);

} catch (PDOException $e) {
    // Логируем ошибку
    error_log('Order error: ' . $e->getMessage());
    
    echo json_encode([
        'status' => 'error',
        'message' => 'Ошибка базы данных при оформлении заказа'
    ]);
}
?>