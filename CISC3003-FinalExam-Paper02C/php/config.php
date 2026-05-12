<?php

declare(strict_types=1);

$localConfig = __DIR__ . '/config.local.php';
if (is_file($localConfig)) {
    require_once $localConfig;
}

if (!defined('BASE_URL')) {
    define('BASE_URL', 'http://localhost/CISC3003-FinalExam-Paper02C/');
}

if (!defined('DB_HOST')) {
    define('DB_HOST', '127.0.0.1');
}
if (!defined('DB_PORT')) {
    define('DB_PORT', 3306);
}
if (!defined('DB_NAME')) {
    define('DB_NAME', 'cisc3003_p2c');
}
if (!defined('DB_USER')) {
    define('DB_USER', 'root');
}
if (!defined('DB_PASSWORD')) {
    define('DB_PASSWORD', '');
}

if (!defined('SMTP_HOST')) {
    define('SMTP_HOST', 'smtp.gmail.com');
}
if (!defined('SMTP_PORT')) {
    define('SMTP_PORT', 587);
}
if (!defined('SMTP_SECURE')) {
    define('SMTP_SECURE', 'tls');
}
if (!defined('SMTP_USERNAME')) {
    define('SMTP_USERNAME', '');
}
if (!defined('SMTP_PASSWORD')) {
    define('SMTP_PASSWORD', '');
}

if (!defined('MAIL_FROM_ADDRESS')) {
    define('MAIL_FROM_ADDRESS', '');
}
if (!defined('MAIL_FROM_NAME')) {
    define('MAIL_FROM_NAME', 'CISC3003 Paper02');
}
