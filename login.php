<?php
declare(strict_types=1);
require __DIR__ . '/includes/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('index.php');
}
if (!csrf_is_valid()) {
    flash('error', 'Your session expired. Please try again.');
    redirect('index.php');
}
$email = strtolower(trim((string) ($_POST['email'] ?? '')));
$password = (string) ($_POST['password'] ?? '');
$_SESSION['old_email'] = $email;

if ($email === '' || $password === '') {
    flash('error', 'Enter your email and password to continue.');
    redirect('index.php');
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    flash('error', 'Enter a valid email address.');
    redirect('index.php');
}
$user = null;
foreach (read_users() as $storedUser) {
    if (isset($storedUser['email'], $storedUser['password_hash']) && hash_equals((string) $storedUser['email'], $email)) {
        $user = $storedUser;
        break;
    }
}
if (!$user || !password_verify($password, (string) $user['password_hash'])) {
    flash('error', 'Email or password is incorrect.');
    redirect('index.php');
}

session_regenerate_id(true);
$_SESSION['user_email'] = $email;
unset($_SESSION['old_email']);
redirect('index.php?welcome=1');
