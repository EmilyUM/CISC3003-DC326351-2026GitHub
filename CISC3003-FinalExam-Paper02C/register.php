<?php

declare(strict_types=1);

header('Content-Type: text/html; charset=UTF-8');

require_once __DIR__ . '/php/helpers.php';
require_once __DIR__ . '/php/config.php';

require_session();

$flashError = $_SESSION['flash_error'] ?? null;
$flashSuccess = $_SESSION['flash_success'] ?? null;
unset($_SESSION['flash_error'], $_SESSION['flash_success']);

$errors = [];
$oldName = '';
$oldEmail = '';

if (is_post()) {
    require_once __DIR__ . '/connect.php';

    $name = trim((string)filter_input(INPUT_POST, 'name', FILTER_UNSAFE_RAW));
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $password = (string)($_POST['password'] ?? '');
    $confirm = (string)($_POST['confirm_password'] ?? '');

    $oldName = $name;
    $oldEmail = (string)($_POST['email'] ?? '');

    if ($name === '' || mb_strlen($name) < 2 || mb_strlen($name) > 100) {
        $errors[] = 'Name is required (2–100 chars).';
    }
    if ($email === false || $email === null) {
        $errors[] = 'Valid email is required.';
    }
    if (mb_strlen($password) < 8) {
        $errors[] = 'Password must be at least 8 characters.';
    }
    if ($password !== $confirm) {
        $errors[] = 'Passwords do not match.';
    }

    if (count($errors) === 0) {
        $stmt = $mysqli->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
        if (!$stmt) {
            $errors[] = 'Prepare failed.';
        } else {
            $stmt->bind_param('s', $email);
            $stmt->execute();
            $res = $stmt->get_result();
            if ($res && $res->fetch_assoc() !== null) {
                $errors[] = 'Email is already registered.';
            }
        }
    }

    if (count($errors) === 0) {
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $token = bin2hex(random_bytes(16));
        $tokenHash = hash('sha256', $token);

        $stmt = $mysqli->prepare(
            'INSERT INTO users (name, email, password_hash, is_email_verified, email_verify_token_hash) VALUES (?, ?, ?, 0, ?)'
        );
        if (!$stmt) {
            $errors[] = 'Prepare failed.';
        } else {
            $stmt->bind_param('ssss', $name, $email, $passwordHash, $tokenHash);
            if (!$stmt->execute()) {
                $errors[] = 'Create account failed.';
            } else {
                $verifyLink = rtrim(BASE_URL, '/') . '/activate.php?token=' . urlencode($token);
                $body = "Hello {$name},\n\nPlease confirm your email to activate your account:\n{$verifyLink}\n\n";

                try {
                    require_once __DIR__ . '/php/mailer.php';
                    send_email((string)$email, $name, 'CISC3003 Email Verification', $body);
                    $_SESSION['flash_success'] = 'Account created. Verification email sent. Please check your inbox.';
                } catch (Throwable $e) {
                    $_SESSION['flash_success'] = 'Account created. Email sending failed (open Debug in Scenario B or check SMTP settings).';
                }

                redirect('register.php');
            }
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register - Paper02C</title>
  <link rel="stylesheet" href="css/styles.css">
</head>
<body>
  <header>
    <h1>Register</h1>
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
    <form method="post" action="register.php" data-form="register" novalidate>
      <div>
        <label for="name">Full Name</label>
        <input id="name" name="name" type="text" required maxlength="100" value="<?= h($oldName) ?>">
      </div>
      <div>
        <label for="email">Email</label>
        <input id="email" name="email" type="email" required maxlength="255" value="<?= h($oldEmail) ?>">
        <div id="email_status" class="status hint"> </div>
      </div>
      <div class="row">
        <div>
          <label for="password">Password</label>
          <input id="password" name="password" type="password" required minlength="8">
        </div>
        <div>
          <label for="confirm_password">Confirm Password</label>
          <input id="confirm_password" name="confirm_password" type="password" required minlength="8">
        </div>
      </div>

      <button type="submit">Create Account</button>
      <a class="btn secondary" href="login.php">Go to Login</a>
    </form>
  </div>

  <?php require __DIR__ . '/php/footer.php'; ?>
  <script src="js/script.js"></script>
</body>
</html>

