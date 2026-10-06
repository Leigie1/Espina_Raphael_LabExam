<?php
declare(strict_types=1);
require __DIR__ . '/includes/bootstrap.php';

if (!empty($_SESSION['user_email']) && !isset($_GET['welcome'])) {
    redirect('index.php?welcome=1');
}
$notice = take_flash();
$welcome = isset($_GET['welcome']) && !empty($_SESSION['user_email']);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <main class="figma-page login-page">
    <header class="page-heading">
      <h1>Welcome</h1>
      <p>Please sign in to continue</p>
    </header>
    <section class="form-panel" aria-label="Login form">
      <?php if ($welcome): ?><div class="notice success" role="status">Login successful. Welcome, <?= e((string) $_SESSION['user_email']) ?>!</div><?php endif; ?>
      <?php if ($notice): ?><div class="notice <?= e($notice['type']) ?>" role="status"><?= e($notice['message']) ?></div><?php endif; ?>
      <form action="login.php" method="post" class="auth-form">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <div class="field">
          <label for="email">Email</label>
          <input id="email" name="email" type="email" placeholder="you@example.com" autocomplete="email" required maxlength="254" value="<?= e((string) ($_SESSION['old_email'] ?? '')) ?>">
          <?php unset($_SESSION['old_email']); ?>
        </div>
        <div class="field">
          <label for="password">Password</label>
          <input id="password" name="password" type="password" placeholder="Enter your password" autocomplete="current-password" required>
        </div>
        <button class="button" type="submit">Sign in</button>
      </form>
      <p class="switch-prompt">Don't have an account? <a href="register.php">Create an account</a></p>
    </section>
    <?php if (!empty($_SESSION['user_email'])): ?>
      <form class="signout-form" action="logout.php" method="post"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><button class="text-button" type="submit">Sign out</button></form>
    <?php endif; ?>
  </main>
</body>
</html>
