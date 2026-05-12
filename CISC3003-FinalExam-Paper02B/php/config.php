<?php

declare(strict_types=1);

$localConfig = __DIR__ . '/config.local.php';
if (is_file($localConfig)) {
    require_once $localConfig;
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

if (!defined('MAIL_TO_ADDRESS')) {
    define('MAIL_TO_ADDRESS', '');
}
if (!defined('MAIL_TO_NAME')) {
    define('MAIL_TO_NAME', 'FAN ZOU CHEN');
}
