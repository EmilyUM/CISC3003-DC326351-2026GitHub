<?php

declare(strict_types=1);

require_once __DIR__ . '/php/config.php';

$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME, DB_PORT);
if ($mysqli->connect_errno) {
    http_response_code(500);
    header('Content-Type: text/plain; charset=UTF-8');
    echo 'Database connection failed.';
    exit;
}

$mysqli->set_charset('utf8mb4');
