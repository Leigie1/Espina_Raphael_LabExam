<?php
declare(strict_types=1);
require __DIR__ . '/includes/bootstrap.php';
$notice = take_flash();
$oldEmail = (string) ($_SESSION['old_email'] ?? '');
unset($_SESSION['old_email']);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Registration</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <main class="figma-page register-page">
    <header class="page-heading">
      <h1>Create an account</h1>
      <p>Please register to get started</p>
    </header>
    <section class="form-panel" aria-label="Registration form">
      <?php if ($notice): ?><div class="notice <?= e($notice['type']) ?>" role="status"><?= e($notice['message']) ?></div><?php endif; ?>
      <form action="register_submit.php" method="post" class="auth-form">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <div class="field">
          <label for="email">Email</label>
          <input id="email" name="email" type="email" placeholder="you@example.com" autocomplete="email" required maxlength="254" value="<?= e($oldEmail) ?>">
        </div>
        <div class="field">
          <label for="password">Password</label>
          <input id="password" name="password" type="password" placeholder="At least 8 characters" autocomplete="new-password" minlength="8" required>
          <small>Use at least 8 characters.</small>
        </div>
        <div class="field">
          <label for="confirm_password">Confirm password</label>
          <input id="confirm_password" name="confirm_password" type="password" placeholder="Enter your password again" autocomplete="new-password" minlength="8" required>
        </div>
        <button class="button" type="submit">Create account</button>
      </form>
      <p class="switch-prompt">Already have an account? <a href="index.php">Sign in</a></p>
    </section>
  </main>
</body>
</html>
