<?php

declare(strict_types=1);

header('Content-Type: text/html; charset=UTF-8');

require_once __DIR__ . '/php/helpers.php';

require_session();

require_once __DIR__ . '/connect.php';

$token = (string)filter_input(INPUT_GET, 'token', FILTER_UNSAFE_RAW);
if (is_post()) {
    $token = (string)($_POST['token'] ?? '');
}
$token = trim($token);

$errors = [];
$flashSuccess = $_SESSION['flash_success'] ?? null;
$flashError = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash_success'], $_SESSION['flash_error']);

if ($token === '' || !preg_match('/^[a-f0-9]{32}$/i', $token)) {
    $errors[] = 'Invalid token.';
}

if (is_post() && count($errors) === 0) {
    $password = (string)($_POST['password'] ?? '');
    $confirm = (string)($_POST['confirm_password'] ?? '');

    if (mb_strlen($password) < 8) {
        $errors[] = 'Password must be at least 8 characters.';
    }
    if ($password !== $confirm) {
        $errors[] = 'Passwords do not match.';
    }

    if (count($errors) === 0) {
        $tokenHash = hash('sha256', $token);
        $now = (new DateTimeImmutable())->format('Y-m-d H:i:s');

        $stmt = $mysqli->prepare('SELECT id FROM users WHERE password_reset_token_hash = ? AND password_reset_expires_at IS NOT NULL AND password_reset_expires_at >= ? LIMIT 1');
        if (!$stmt) {
            $errors[] = 'Prepare failed.';
        } else {
            $stmt->bind_param('ss', $tokenHash, $now);
            $stmt->execute();
            $res = $stmt->get_result();
            $row = $res ? $res->fetch_assoc() : null;
            if (!$row) {
                $errors[] = 'Token is invalid or expired.';
            } else {
                $userId = (int)$row['id'];
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $up = $mysqli->prepare('UPDATE users SET password_hash = ?, password_reset_token_hash = NULL, password_reset_expires_at = NULL WHERE id = ?');
                if (!$up) {
                    $errors[] = 'Prepare failed.';
                } else {
                    $up->bind_param('si', $hash, $userId);
                    if (!$up->execute()) {
                        $errors[] = 'Reset failed.';
                    } else {
                        $_SESSION['flash_success'] = 'Password updated. Please login.';
                        redirect('login.php');
                    }
                }
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
  <title>Reset Password - Paper02C</title>
  <link rel="stylesheet" href="css/styles.css">
</head>
<body>
  <header>
    <h1>Reset Password</h1>
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
    <form method="post" action="reset_password.php" novalidate>
      <input type="hidden" name="token" value="<?= h($token) ?>">
      <div class="row">
        <div>
          <label for="password">New Password</label>
          <input id="password" name="password" type="password" required minlength="8">
        </div>
        <div>
          <label for="confirm_password">Confirm Password</label>
          <input id="confirm_password" name="confirm_password" type="password" required minlength="8">
        </div>
      </div>
      <button type="submit">Update Password</button>
      <a class="btn secondary" href="login.php">Back to Login</a>
    </form>
  </div>

  <?php require __DIR__ . '/php/footer.php'; ?>
</body>
</html>

