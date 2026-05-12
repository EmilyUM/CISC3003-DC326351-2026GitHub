<?php

declare(strict_types=1);

header('Content-Type: text/html; charset=UTF-8');

session_start();

require_once __DIR__ . '/php/helpers.php';

$majors = [
    'CS' => 'Computer Science',
    'IS' => 'Information Systems',
    'SE' => 'Software Engineering',
    'DS' => 'Data Science',
];

$genders = ['Male', 'Female', 'Other'];
$interestOptions = ['PHP', 'MySQL', 'JavaScript', 'CSS'];

$errors = [];
$old = [
    'full_name' => '',
    'email' => '',
    'age' => '',
    'bio' => '',
    'major' => 'CS',
    'gender' => 'Male',
    'interests' => [],
    'agree_terms' => '',
];

if (is_post()) {
    $fullName = trim((string)filter_input(INPUT_POST, 'full_name', FILTER_UNSAFE_RAW));
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $age = filter_input(INPUT_POST, 'age', FILTER_VALIDATE_INT, [
        'options' => ['min_range' => 1, 'max_range' => 120],
    ]);
    $bio = trim((string)filter_input(INPUT_POST, 'bio', FILTER_UNSAFE_RAW));
    $major = (string)filter_input(INPUT_POST, 'major', FILTER_UNSAFE_RAW);
    $gender = (string)filter_input(INPUT_POST, 'gender', FILTER_UNSAFE_RAW);
    $interestsRaw = $_POST['interests'] ?? [];
    $agreeTerms = filter_input(INPUT_POST, 'agree_terms', FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);

    $old['full_name'] = $fullName;
    $old['email'] = (string)($_POST['email'] ?? '');
    $old['age'] = (string)($_POST['age'] ?? '');
    $old['bio'] = $bio;
    $old['major'] = $major;
    $old['gender'] = $gender;
    $old['interests'] = is_array($interestsRaw) ? $interestsRaw : [];
    $old['agree_terms'] = $agreeTerms ? '1' : '';

    if ($fullName === '' || mb_strlen($fullName) > 100) {
        $errors[] = 'Full name is required (max 100 chars).';
    }
    if ($email === false || $email === null) {
        $errors[] = 'A valid email is required.';
    }
    if ($age === false || $age === null) {
        $errors[] = 'Age must be an integer between 1 and 120.';
    }
    if ($bio === '' || mb_strlen($bio) < 10) {
        $errors[] = 'Bio is required (min 10 chars).';
    }
    if (!array_key_exists($major, $majors)) {
        $errors[] = 'Please select a valid major.';
    }
    if (!in_array($gender, $genders, true)) {
        $errors[] = 'Please select a valid gender.';
    }

    $interests = [];
    if (is_array($interestsRaw)) {
        foreach ($interestsRaw as $it) {
            $it = (string)$it;
            if (in_array($it, $interestOptions, true)) {
                $interests[] = $it;
            }
        }
    }
    if (count($interests) === 0) {
        $errors[] = 'Select at least one interest.';
    }
    if ($agreeTerms !== true) {
        $errors[] = 'You must agree to the terms.';
    }

    if (count($errors) === 0) {
        require __DIR__ . '/connect.php';

        $interestsCsv = implode(',', $interests);
        $agreeInt = 1;
        $stmt = $mysqli->prepare(
            'INSERT INTO submissions (full_name, email, age, bio, major, gender, interests, agree_terms) VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
        );
        if (!$stmt) {
            $errors[] = 'Prepare failed.';
        } else {
            $stmt->bind_param('ssissssi', $fullName, $email, $age, $bio, $major, $gender, $interestsCsv, $agreeInt);
            if (!$stmt->execute()) {
                $errors[] = 'Insert failed.';
            } else {
                $_SESSION['flash_success'] = 'Saved successfully. Inserted ID: ' . (string)$mysqli->insert_id;
                redirect('index.php?ok=1');
            }
        }
    }
}

$flashSuccess = $_SESSION['flash_success'] ?? null;
unset($_SESSION['flash_success']);

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>CISC3003 Final Exam Paper02A</title>
  <link rel="stylesheet" href="css/styles.css">
</head>
<body>
  <header>
    <h1>Scenario A: Form → Validate → Insert (MySQL)</h1>
    <span class="hint">A.01–A.10</span>
  </header>

  <?php if ($flashSuccess): ?>
    <div class="success"><?= h($flashSuccess) ?></div>
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

  <form method="post" action="index.php" data-form="p2a" novalidate>
    <div class="row">
      <div>
        <label for="full_name">Full Name</label>
        <input id="full_name" name="full_name" type="text" required maxlength="100" value="<?= h($old['full_name']) ?>">
      </div>
      <div>
        <label for="email">Email</label>
        <input id="email" name="email" type="email" required value="<?= h($old['email']) ?>">
      </div>
    </div>

    <div class="row">
      <div>
        <label for="age">Age</label>
        <input id="age" name="age" type="number" required min="1" max="120" value="<?= h($old['age']) ?>">
      </div>
      <div>
        <label for="major">Major (Select)</label>
        <select id="major" name="major" required>
          <?php foreach ($majors as $key => $label): ?>
            <option value="<?= h($key) ?>" <?= $old['major'] === $key ? 'selected' : '' ?>><?= h($label) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>

    <div class="fieldset">
      <div class="hint">Gender (Radio)</div>
      <?php foreach ($genders as $g): ?>
        <label>
          <input type="radio" name="gender" value="<?= h($g) ?>" <?= $old['gender'] === $g ? 'checked' : '' ?> required>
          <?= h($g) ?>
        </label>
      <?php endforeach; ?>
    </div>

    <div class="fieldset">
      <div class="hint">Interests (Checkboxes)</div>
      <?php foreach ($interestOptions as $it): ?>
        <label>
          <input type="checkbox" name="interests[]" value="<?= h($it) ?>" <?= in_array($it, $old['interests'], true) ? 'checked' : '' ?>>
          <?= h($it) ?>
        </label>
      <?php endforeach; ?>
    </div>

    <div>
      <label for="bio">Bio (Textarea)</label>
      <textarea id="bio" name="bio" required><?= h($old['bio']) ?></textarea>
      <div class="hint">At least 10 characters.</div>
    </div>

    <label>
      <input type="checkbox" name="agree_terms" value="1" <?= $old['agree_terms'] ? 'checked' : '' ?> required>
      I agree to the terms.
    </label>

    <button type="submit">Submit & Save to MySQL</button>
  </form>

  <?php require __DIR__ . '/php/footer.php'; ?>

  <script src="js/script.js"></script>
</body>
</html>

