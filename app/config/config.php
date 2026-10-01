<?php
declare(strict_types=1);

const APP_NAME = 'EduInsight AI';
const BASE_PATH = __DIR__ . '/../../';
const PUBLIC_PATH = BASE_PATH . 'public/';

$dbHost = getenv('DB_HOST') ?: '127.0.0.1';
$dbName = getenv('DB_NAME') ?: 'eduinsight_ai';
$dbUser = getenv('DB_USER') ?: 'root';
$dbPass = getenv('DB_PASS') ?: '';

try {
    $pdo = new PDO("mysql:host={$dbHost};dbname={$dbName};charset=utf8mb4", $dbUser, $dbPass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
} catch (PDOException $e) {
    $pdo = null;
    $dbError = 'Database belum terhubung. Import database/schema.sql dan periksa app/config/config.php.';
}

require_once BASE_PATH . 'app/core/helpers.php';
require_once BASE_PATH . 'app/services/RandomForestService.php';
