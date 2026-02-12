<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Получаем данные
$input = json_decode(file_get_contents('php://input'), true);

$name  = trim($input['name'] ?? '');
$phone = trim($input['phone'] ?? '');

// Валидация
if (empty($name) || empty($phone)) {
    echo json_encode(['success' => false, 'message' => 'Заполните все поля']);
    exit;
}

// Защита от инъекций
$name  = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
$phone = htmlspecialchars($phone, ENT_QUOTES, 'UTF-8');

// Настройки
$to      = 'matveev@pro-online.ru';
$subject = 'Новая заявка с сайта ProOnline';

// Формируем письмо
$body  = "Новая заявка с сайта\n";
$body .= "========================\n\n";
$body .= "Имя:     $name\n";
$body .= "Телефон: $phone\n\n";
$body .= "Дата:    " . date('d.m.Y H:i:s') . "\n";
$body .= "IP:      " . ($_SERVER['REMOTE_ADDR'] ?? 'unknown') . "\n";

$headers  = "From: noreply@" . ($_SERVER['SERVER_NAME'] ?? 'pro-online.ru') . "\r\n";
$headers .= "Reply-To: noreply@" . ($_SERVER['SERVER_NAME'] ?? 'pro-online.ru') . "\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
$headers .= "X-Mailer: ProOnline Website\r\n";

// Отправляем
$sent = mail($to, $subject, $body, $headers);

if ($sent) {
    echo json_encode(['success' => true, 'message' => 'Заявка отправлена']);
} else {
    echo json_encode(['success' => false, 'message' => 'Ошибка отправки']);
}
