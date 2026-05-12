<?php

declare(strict_types=1);

header('Content-Type: text/html; charset=UTF-8');

require_once __DIR__ . '/php/helpers.php';
require_once __DIR__ . '/php/config.php';

require_session();

$flashSuccess = $_SESSION['flash_success'] ?? null;
$flashError = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash_success'], $_SESSION['flash_error']);

$errors = [];
$oldEmail = '';

if (is_post()) {
    require_once __DIR__ . '/connect.php';

    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $oldEmail = (string)($_POST['email'] ?? '');

    if ($email === false || $email === null) {
        $errors[] = 'Valid email is required.';
    }

    if (count($errors) === 0) {
        $stmt = $mysqli->prepare('SELECT id, name FROM users WHERE email = ? LIMIT 1');
        if ($stmt) {
            $stmt->bind_param('s', $email);
            $stmt->execute();
            $res = $stmt->get_result();
            $user = $res ? $res->fetch_assoc() : null;

            if ($user) {
                $token = bin2hex(random_bytes(16));
                $tokenHash = hash('sha256', $token);
                $expiresAt = (new DateTimeImmutable('+30 minutes'))->format('Y-m-d H:i:s');

                $up = $mysqli->prepare('UPDATE users SET password_reset_token_hash = ?, password_reset_expires_at = ? WHERE id = ?');
                if ($up) {
                    $id = (int)$user['id'];
                    $up->bind_param('ssi', $tokenHash, $expiresAt, $id);
                    $up->execute();

                    $resetLink = rtrim(BASE_URL, '/') . '/reset_password.php?token=' . urlencode($token);
                    $body = "Hello {$user['name']},\n\nReset your password using this link (valid for 30 minutes):\n{$resetLink}\n\n";

                    try {
                        require_once __DIR__ . '/php/mailer.php';
                        send_email((string)$email, (string)$user['name'], 'CISC3003 Password Reset', $body);
                    } catch (Throwable $e) {
                        $_SESSION['flash_error'] = 'Email sending failed. Check SMTP settings.';
                        redirect('forgot_password.php');
                    }
                }
            }
        }

        $_SESSION['flash_success'] = 'If the email exists, a reset link has been sent.';
        redirect('forgot_password.php');
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Forgot Password - Paper02C</title>
  <link rel="stylesheet" href="css/styles.css">
</head>
<body>
  <header>
    <h1>Forgot Password</h1>
    <span class="hint"><a href="index.php">Home</a></span>
  </header>

  <?php if ($flashSuccess): ?>
    <div class="success"><?= h($flashSuccess) ?></div>
  <?php endif; ?>
  <?php if ($flashError): ?>
    <div class="error"><?= h($flashError) ?></div>
  <?php endif; ?>

  <?php if (count($errors) > 0): ?>
    <div class="error">
      <ul>
        <?php foreach ($errors as $e): ?>
          <li><?= h($e) ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <div class="card">
    <form method="post" action="forgot_password.php" novalidate>
      <div>
        <label for="email">Email</label>
        <input id="email" name="email" type="email" required maxlength="255" value="<?= h($oldEmail) ?>">
      </div>
      <button type="submit">Send Reset Link</button>
      <a class="btn secondary" href="login.php">Back to Login</a>
    </form>
  </div>

  <?php require __DIR__ . '/php/footer.php'; ?>
</body>
</html>

