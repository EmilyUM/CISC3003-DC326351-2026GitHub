<?php

declare(strict_types=1);

require_once __DIR__ . '/php/helpers.php';
require_once __DIR__ . '/connect.php';

$email = filter_input(INPUT_GET, 'email', FILTER_VALIDATE_EMAIL);
if ($email === false || $email === null) {
    json_response(['available' => false, 'error' => 'invalid_email'], 400);
}

$stmt = $mysqli->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
if (!$stmt) {
    json_response(['available' => false, 'error' => 'prepare_failed'], 500);
}
$stmt->bind_param('s', $email);
$stmt->execute();
$res = $stmt->get_result();
$exists = $res && $res->fetch_assoc() !== null;

json_response(['available' => !$exists]);

