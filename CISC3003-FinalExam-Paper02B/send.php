<?php

declare(strict_types=1);

header('Content-Type: text/html; charset=UTF-8');

session_start();

require_once __DIR__ . '/php/helpers.php';

$debug = filter_input(INPUT_GET, 'debug', FILTER_VALIDATE_INT) === 1;

if (!$debug && !is_post()) {
    redirect('index.php');
}

$name = trim((string)filter_input(INPUT_POST, 'name', FILTER_UNSAFE_RAW));
$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
$subject = trim((string)filter_input(INPUT_POST, 'subject', FILTER_UNSAFE_RAW));
$message = trim((string)filter_input(INPUT_POST, 'message', FILTER_UNSAFE_RAW));

$errors = [];
if (!$debug) {
    if ($name === '' || mb_strlen($name) > 80) {
        $errors[] = 'Name is required (max 80 chars).';
    }
    if ($email === false || $email === null) {
        $errors[] = 'Valid email is required.';
    }
    if ($subject === '' || mb_strlen($subject) > 120) {
        $errors[] = 'Subject is required (max 120 chars).';
    }
    if ($message === '' || mb_strlen($message) < 10) {
        $errors[] = 'Message is required (min 10 chars).';
    }
}

try {
    require_once __DIR__ . '/php/mailer.php';
} catch (Throwable $e) {
    if ($debug) {
        http_response_code(500);
        echo '<pre>' . h($e->getMessage()) . '</pre>';
        exit;
    }
    $_SESSION['flash_error'] = 'PHPMailer is missing. Please download it into lib/PHPMailer.';
    redirect('index.php');
}

if (count($errors) > 0) {
    $_SESSION['flash_error'] = implode(' ', $errors);
    redirect('index.php');
}

if ($debug) {
    echo '<h1>PHPMailer Debug</h1>';
    echo '<p>Open <code>php/config.local.php</code> and fill SMTP settings, then refresh this page.</p>';
    echo '<hr>';
}

try {
    $mail = build_mailer($debug);
    $mail->Subject = $subject !== '' ? $subject : 'CISC3003 Contact Form (Debug)';
    $safeName = $name !== '' ? $name : 'Debug User';
    $safeEmail = $email !== false && $email !== null ? $email : 'debug@example.com';

    $mail->Body = "Name: {$safeName}\nEmail: {$safeEmail}\n\nMessage:\n{$message}";
    $mail->AltBody = $mail->Body;

    $mail->send();

    if ($debug) {
        echo '<p><strong>Sent.</strong></p>';
        exit;
    }

    $_SESSION['flash_success'] = 'Email sent successfully.';
    redirect('index.php?sent=1');
} catch (Throwable $e) {
    if ($debug) {
        echo '<pre>' . h($e->getMessage()) . '</pre>';
        exit;
    }
    $_SESSION['flash_error'] = 'Email failed to send.';
    redirect('index.php');
}

