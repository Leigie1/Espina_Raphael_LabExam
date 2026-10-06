<?php
declare(strict_types=1);
require __DIR__ . '/includes/bootstrap.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !csrf_is_valid()) {
    redirect('index.php');
}
unset($_SESSION['user_email']);
flash('success', 'You have signed out. See you next time.');
redirect('index.php');
