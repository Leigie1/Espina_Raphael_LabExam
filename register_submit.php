<?php
declare(strict_types=1);
require __DIR__ . '/includes/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('register.php');
}
if (!csrf_is_valid()) {
    flash('error', 'Your session expired. Please try again.');
    redirect('register.php');
}
$email = strtolower(trim((string) ($_POST['email'] ?? '')));
$password = (string) ($_POST['password'] ?? '');
$confirmation = (string) ($_POST['confirm_password'] ?? '');
$_SESSION['old_email'] = $email;

if ($email === '' || $password === '' || $confirmation === '') {
    flash('error', 'Please complete every field.');
    redirect('register.php');
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 254) {
    flash('error', 'Enter a valid email address.');
    redirect('register.php');
}
if (strlen($password) < 8) {
    flash('error', 'Your password must be at least 8 characters.');
    redirect('register.php');
}
if (!hash_equals($password, $confirmation)) {
    flash('error', 'The passwords do not match. Please try again.');
    redirect('register.php');
}

try {
    $created = with_users_file_lock(function (array &$users) use ($email, $password): bool {
        foreach ($users as $user) {
            if (isset($user['email']) && hash_equals((string) $user['email'], $email)) {
                return false;
            }
        }
        $users[] = ['email' => $email, 'password_hash' => password_hash($password, PASSWORD_DEFAULT)];
        return true;
    });
} catch (Throwable $error) {
    flash('error', 'We could not save your account right now. Please try again.');
    redirect('register.php');
}

unset($_SESSION['old_email']);
if (!$created) {
    $_SESSION['old_email'] = $email;
    flash('error', 'An account with that email already exists. Try signing in.');
    redirect('register.php');
}
flash('success', 'Your account is ready. Please sign in.');
redirect('index.php');
