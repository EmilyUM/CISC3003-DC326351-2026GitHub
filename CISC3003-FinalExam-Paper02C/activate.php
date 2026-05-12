<?php

declare(strict_types=1);

header('Content-Type: text/html; charset=UTF-8');

require_once __DIR__ . '/php/helpers.php';
require_once __DIR__ . '/connect.php';

$token = (string)filter_input(INPUT_GET, 'token', FILTER_UNSAFE_RAW);
$token = trim($token);

$message = '';
$ok = false;

if ($token === '' || !preg_match('/^[a-f0-9]{32}$/i', $token)) {
    $message = 'Invalid token.';
} else {
    $tokenHash = hash('sha256', $token);
    $stmt = $mysqli->prepare('UPDATE users SET is_email_verified = 1, email_verify_token_hash = NULL WHERE email_verify_token_hash = ? AND is_email_verified = 0');
    if ($stmt) {
        $stmt->bind_param('s', $tokenHash);
        $stmt->execute();
        if ($stmt->affected_rows > 0) {
            $ok = true;
            $message = 'Email verified. You can now login.';
        } else {
            $message = 'Token is invalid or already used.';
        }
    } else {
        $message = 'Server error.';
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Activate - Paper02C</title>
  <link rel="stylesheet" href="css/styles.css">
</head>
<body>
  <header>
    <h1>Email Activation</h1>
    <span class="hint"><a href="index.php">Home</a></span>
  </header>

  <div class="<?= $ok ? 'success' : 'error' ?>"><?= h($message) ?></div>
  <p><a class="btn" href="login.php">Go to Login</a></p>

  <?php require __DIR__ . '/php/footer.php'; ?>
</body>
</html>

