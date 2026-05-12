<?php

declare(strict_types=1);

require_once __DIR__ . '/config.php';

$phpMailerDir = __DIR__ . '/../lib/PHPMailer/src';
$exceptionFile = $phpMailerDir . '/Exception.php';
$phpMailerFile = $phpMailerDir . '/PHPMailer.php';
$smtpFile = $phpMailerDir . '/SMTP.php';

if (!is_file($exceptionFile) || !is_file($phpMailerFile) || !is_file($smtpFile)) {
    throw new RuntimeException('PHPMailer library is missing under lib/PHPMailer.');
}

require_once $exceptionFile;
require_once $phpMailerFile;
require_once $smtpFile;

use PHPMailer\PHPMailer\PHPMailer;

function build_mailer(): PHPMailer
{
    if (MAIL_FROM_ADDRESS === '' || SMTP_USERNAME === '' || SMTP_PASSWORD === '') {
        throw new RuntimeException('Missing SMTP settings. Please fill php/config.local.php (SMTP_USERNAME/SMTP_PASSWORD/MAIL_FROM_ADDRESS).');
    }

    $mail = new PHPMailer(true);
    $mail->CharSet = 'UTF-8';
    $mail->isSMTP();
    $mail->Host = SMTP_HOST;
    $mail->Port = SMTP_PORT;
    $mail->SMTPSecure = SMTP_SECURE;
    $mail->SMTPAuth = true;
    $mail->Username = SMTP_USERNAME;
    $mail->Password = SMTP_PASSWORD;

    $mail->SMTPDebug = 0;

    $mail->setFrom(MAIL_FROM_ADDRESS, MAIL_FROM_NAME);
    return $mail;
}

function send_email(string $to, string $toName, string $subject, string $bodyText): void
{
    $mail = build_mailer();
    $mail->addAddress($to, $toName);
    $mail->Subject = $subject;
    $mail->Body = $bodyText;
    $mail->AltBody = $bodyText;
    $mail->send();
}
