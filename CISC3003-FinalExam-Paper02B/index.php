<?php

declare(strict_types=1);

header('Content-Type: text/html; charset=UTF-8');

session_start();

require_once __DIR__ . '/php/helpers.php';

$flashError = $_SESSION['flash_error'] ?? null;
$flashSuccess = $_SESSION['flash_success'] ?? null;
unset($_SESSION['flash_error'], $_SESSION['flash_success']);

$sent = filter_input(INPUT_GET, 'sent', FILTER_VALIDATE_INT) === 1;

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>CISC3003 Final Exam Paper02B</title>
  <link rel="stylesheet" href="css/styles.css">
</head>
<body>
  <header>
    <h1>Scenario B: Contact Form → PHPMailer → PRG</h1>
    <span class="hint">B.01–B.05</span>
  </header>

  <?php if ($sent || $flashSuccess): ?>
    <div class="success"><?= h($flashSuccess ?? 'Email sent successfully.') ?></div>
  <?php endif; ?>

  <?php if ($flashError): ?>
    <div class="error"><?= h($flashError) ?></div>
  <?php endif; ?>

  <form method="post" action="send.php" data-form="contact" novalidate>
    <div>
      <label for="name">Your Name</label>
      <input id="name" name="name" type="text" required maxlength="80">
    </div>
    <div>
      <label for="email">Your Email</label>
      <input id="email" name="email" type="email" required maxlength="255">
    </div>
    <div>
      <label for="subject">Subject</label>
      <input id="subject" name="subject" type="text" required maxlength="120">
    </div>
    <div>
      <label for="message">Message</label>
      <textarea id="message" name="message" required></textarea>
      <div class="hint">At least 10 characters.</div>
    </div>

    <button type="submit">Send Email</button>
    <a class="hint" href="send.php?debug=1">Debug sending</a>
  </form>

  <?php require __DIR__ . '/php/footer.php'; ?>

  <script src="js/script.js"></script>
</body>
</html>

