<?php

declare(strict_types=1);

header('Content-Type: text/html; charset=UTF-8');

require_once __DIR__ . '/php/helpers.php';
require_once __DIR__ . '/php/auth.php';

require_login();

require_session();

require_once __DIR__ . '/connect.php';

$userId = (int)current_user_id();

$flashSuccess = $_SESSION['flash_success'] ?? null;
$flashError = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash_success'], $_SESSION['flash_error']);

$errors = [];

function load_user(mysqli $mysqli, int $userId): ?array
{
    $stmt = $mysqli->prepare('SELECT id, name, email, created_at FROM users WHERE id = ? LIMIT 1');
    if (!$stmt) {
        return null;
    }
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $res = $stmt->get_result();
    return $res ? $res->fetch_assoc() : null;
}

if (is_post()) {
    $action = (string)($_POST['action'] ?? '');
    if ($action === 'update_name') {
        $name = trim((string)filter_input(INPUT_POST, 'name', FILTER_UNSAFE_RAW));
        if ($name === '' || mb_strlen($name) < 2 || mb_strlen($name) > 100) {
            $errors[] = 'Name is required (2–100 chars).';
        } else {
            $stmt = $mysqli->prepare('UPDATE users SET name = ? WHERE id = ?');
            if (!$stmt) {
                $errors[] = 'Prepare failed.';
            } else {
                $stmt->bind_param('si', $name, $userId);
                if ($stmt->execute()) {
                    $_SESSION['flash_success'] = 'Profile updated.';
                    redirect('dashboard.php');
                }
                $errors[] = 'Update failed.';
            }
        }
    } elseif ($action === 'change_password') {
        $password = (string)($_POST['password'] ?? '');
        $confirm = (string)($_POST['confirm_password'] ?? '');
        if (mb_strlen($password) < 8) {
            $errors[] = 'Password must be at least 8 characters.';
        } elseif ($password !== $confirm) {
            $errors[] = 'Passwords do not match.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $mysqli->prepare('UPDATE users SET password_hash = ? WHERE id = ?');
            if (!$stmt) {
                $errors[] = 'Prepare failed.';
            } else {
                $stmt->bind_param('si', $hash, $userId);
                if ($stmt->execute()) {
                    $_SESSION['flash_success'] = 'Password updated.';
                    redirect('dashboard.php');
                }
                $errors[] = 'Update failed.';
            }
        }
    }
}

$user = load_user($mysqli, $userId);
if (!$user) {
    logout_user();
    redirect('login.php');
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard - Paper02C</title>
  <link rel="stylesheet" href="css/dashboard.css">
</head>
<body>
  <header>
    <h1>Dashboard</h1>
    <span class="hint"><a href="logout.php">Logout</a></span>
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

  <div class="cards">
    <section class="card">
      <h2>Welcome</h2>
      <p><strong>Name:</strong> <?= h($user['name']) ?></p>
      <p><strong>Email:</strong> <?= h($user['email']) ?></p>
      <p><strong>Member since:</strong> <?= h($user['created_at']) ?></p>
      <p class="hint">C.09: user dashboard with services under user control.</p>
    </section>

    <section class="card">
      <h2>Update Profile</h2>
      <form method="post" action="dashboard.php" novalidate>
        <input type="hidden" name="action" value="update_name">
        <div>
          <label for="name">Display Name</label>
          <input id="name" name="name" type="text" required maxlength="100" value="<?= h($user['name']) ?>">
        </div>
        <button class="btn" type="submit">Save</button>
      </form>
    </section>

    <section class="card">
      <h2>Change Password</h2>
      <form method="post" action="dashboard.php" novalidate>
        <input type="hidden" name="action" value="change_password">
        <div>
          <label for="password">New Password</label>
          <input id="password" name="password" type="password" required minlength="8">
        </div>
        <div>
          <label for="confirm_password">Confirm Password</label>
          <input id="confirm_password" name="confirm_password" type="password" required minlength="8">
        </div>
        <button class="btn secondary" type="submit">Update Password</button>
      </form>
    </section>

    <section class="card">
      <h2>Quick Links</h2>
      <p><a class="btn" href="index.php">Home</a></p>
      <p><a class="btn secondary" href="logout.php">Logout</a></p>
    </section>
  </div>

  <?php require __DIR__ . '/php/footer.php'; ?>
</body>
</html>

