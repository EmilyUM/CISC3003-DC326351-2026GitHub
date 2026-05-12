<?php

declare(strict_types=1);

header('Content-Type: text/html; charset=UTF-8');

require_once __DIR__ . '/php/helpers.php';
require_once __DIR__ . '/php/auth.php';

$userId = current_user_id();

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>CISC3003 Final Exam Paper02C</title>
  <link rel="stylesheet" href="css/styles.css">
</head>
<body>
  <header>
    <h1>Scenario C: Signup / Login / Reset / Verify / Dashboard</h1>
    <span class="hint">C.01–C.09</span>
  </header>

  <?php if ($userId !== null): ?>
    <div class="success">
      You are logged in. <a href="dashboard.php">Go to Dashboard</a> · <a href="logout.php">Logout</a>
    </div>
  <?php endif; ?>

  <div class="card">
    <div class="row">
      <button id="btnSignup" type="button">Sign Up</button>
      <button id="btnSignin" type="button" class="secondary">Sign In</button>
    </div>
    <div class="hint">Buttons are controlled by JavaScript (script.js).</div>
  </div>

  <div class="grid" style="margin-top: 18px;">
    <section id="panelSignup" class="card">
      <h2>Create an account</h2>
      <p class="hint">Register a new user and verify email before login.</p>
      <a class="btn" href="register.php">Open Register Page</a>
    </section>

    <section id="panelSignin" class="card" hidden>
      <h2>Login</h2>
      <p class="hint">Email must be verified before login.</p>
      <a class="btn secondary" href="login.php">Open Login Page</a>
      <div style="margin-top: 10px;">
        <a class="hint" href="forgot_password.php">Forgot password?</a>
      </div>
    </section>
  </div>

  <?php require __DIR__ . '/php/footer.php'; ?>
  <script src="js/script.js"></script>
</body>
</html>

