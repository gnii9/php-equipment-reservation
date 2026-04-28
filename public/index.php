<?php

require __DIR__ . '/../vendor/autoload.php';

use App\Controllers\EquipmentController;
use App\Controllers\HomeController;
use App\Controllers\ReservationController;
use App\Support\Env;
use App\Support\Response;
use Dotenv\Dotenv;

// --- Load .env ---
$dotenv = Dotenv::createImmutable(dirname(__DIR__));
$dotenv->safeLoad();

// --- Validate biến môi trường bắt buộc ---
$dotenv->required(['APP_NAME', 'APP_ENV', 'APP_DEBUG', 'APP_URL',
                   'ORGANIZER_NAME', 'MAX_RESERVATIONS_PER_REQUEST']);
$dotenv->required('APP_DEBUG')->isBoolean();
$dotenv->required('MAX_RESERVATIONS_PER_REQUEST')->isInteger();

// --- Cấu hình error reporting theo môi trường ---
error_reporting(E_ALL);

if (Env::get('APP_ENV', 'prod') === 'dev' && Env::bool('APP_DEBUG', false)) {
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    ini_set('log_errors', '1');
} else {
    ini_set('display_errors', '0');
    ini_set('display_startup_errors', '0');
    ini_set('log_errors', '1');
    ini_set('error_log', dirname(__DIR__) . '/storage/logs/php-error.log');
}

// --- Load config và dữ liệu ---
$config    = require dirname(__DIR__) . '/config/app.php';
$equipment = require dirname(__DIR__) . '/src/Data/equipment.php';

// --- Lấy method và path từ request ---
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$path   = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);

// =====================
// ROUTING
// =====================

// Trang chủ
if ($path === '/' && $method === 'GET') {
    $controller = new HomeController();
    $data = $controller->index($config, $equipment);
    require dirname(__DIR__) . '/views/home.php';
    exit;
}

// GET /equipment
if ($path === '/equipment' && $method === 'GET') {
    (new EquipmentController())->index($equipment);
}

// HEAD /equipment
if ($path === '/equipment' && $method === 'HEAD') {
    (new EquipmentController())->head();
}

// 405 cho /equipment với method khác
if ($path === '/equipment' && !in_array($method, ['GET', 'HEAD'], true)) {
    Response::json(405, [
        'error' => 'Method Not Allowed',
    ], [
        'Allow' => 'GET, HEAD',
    ]);
}

// POST /reservations
if ($path === '/reservations' && $method === 'POST') {
    (new ReservationController())->store($equipment, $config);
}

// OPTIONS /reservations
if ($path === '/reservations' && $method === 'OPTIONS') {
    (new ReservationController())->options();
}

// 405 cho /reservations với method khác
if ($path === '/reservations' && !in_array($method, ['POST', 'OPTIONS'], true)) {
    Response::json(405, [
        'error' => 'Method Not Allowed',
    ], [
        'Allow' => 'POST, OPTIONS',
    ]);
}

// GET /health
if ($path === '/health' && $method === 'GET') {
    Response::json(200, [
        'status' => 'ok',
        'app'    => $config['app']['name'],
    ]);
}

// 404 - không khớp route nào
Response::json(404, [
    'error' => 'Not Found',
]);