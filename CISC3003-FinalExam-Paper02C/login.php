<?php

declare(strict_types=1);

header('Content-Type: text/html; charset=UTF-8');

require_once __DIR__ . '/php/helpers.php';
require_once __DIR__ . '/php/auth.php';

require_session();

$flashError = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash_error']);

$errors = [];
$oldEmail = '';

if (is_post()) {
    require_once __DIR__ . '/connect.php';

    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $password = (string)($_POST['password'] ?? '');
    $oldEmail = (string)($_POST['email'] ?? '');

    if ($email === false || $email === null) {
        $errors[] = 'Valid email is required.';
    }
    if (mb_strlen($password) < 8) {
        $errors[] = 'Password must be at least 8 characters.';
    }

    if (count($errors) === 0) {
        $stmt = $mysqli->prepare('SELECT id, password_hash, is_email_verified FROM users WHERE email = ? LIMIT 1');
        if (!$stmt) {
            $errors[] = 'Prepare failed.';
        } else {
            $stmt->bind_param('s', $email);
            $stmt->execute();
            $res = $stmt->get_result();
            $user = $res ? $res->fetch_assoc() : null;

            if (!$user || !password_verify($password, (string)$user['password_hash'])) {
                $errors[] = 'Invalid email or password.';
            } elseif ((int)$user['is_email_verified'] !== 1) {
                $errors[] = 'Please verify your email before login.';
            } else {
                login_user((int)$user['id']);
                redirect('dashboard.php');
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
  <title>Login - Paper02C</title>
  <link rel="stylesheet" href="css/styles.css">
</head>
<body>
  <header>
    <h1>Login</h1>
    <span class="hint"><a href="index.php">Home</a></span>
  </header>

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
    <form method="post" action="login.php" data-form="login" novalidate>
      <div>
        <label for="email">Email</label>
        <input id="email" name="email" type="email" required maxlength="255" value="<?= h($oldEmail) ?>">
      </div>
      <div>
        <label for="password">Password</label>
        <input id="password" name="password" type="password" required minlength="8">
      </div>
      <button type="submit">Login</button>
      <a class="btn secondary" href="register.php">Register</a>
      <a class="btn light" href="forgot_password.php">Forgot Password</a>
    </form>
  </div>

  <?php require __DIR__ . '/php/footer.php'; ?>
  <script src="js/script.js"></script>
</body>
</html>

