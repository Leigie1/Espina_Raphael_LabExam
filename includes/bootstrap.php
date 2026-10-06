<?php
declare(strict_types=1);

session_start();

const DATA_FILE = __DIR__ . '/../data/users.json';

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_is_valid(): bool
{
    $sent = $_POST['csrf_token'] ?? '';
    return is_string($sent) && isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $sent);
}

function read_users(): array
{
    if (!is_file(DATA_FILE)) {
        return [];
    }
    $json = file_get_contents(DATA_FILE);
    $users = is_string($json) ? json_decode($json, true) : null;
    return is_array($users) ? $users : [];
}

function with_users_file_lock(callable $callback): mixed
{
    $handle = fopen(DATA_FILE, 'c+');
    if ($handle === false) {
        throw new RuntimeException('The user store could not be opened.');
    }
    try {
        if (!flock($handle, LOCK_EX)) {
            throw new RuntimeException('The user store is temporarily unavailable.');
        }
        $contents = stream_get_contents($handle);
        $users = is_string($contents) && trim($contents) !== '' ? json_decode($contents, true) : [];
        if (!is_array($users)) {
            $users = [];
        }
        $result = $callback($users);
        rewind($handle);
        ftruncate($handle, 0);
        fwrite($handle, json_encode($users, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR));
        fflush($handle);
        flock($handle, LOCK_UN);
        return $result;
    } finally {
        fclose($handle);
    }
}

function flash(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function take_flash(): ?array
{
    $message = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return is_array($message) ? $message : null;
}

function redirect(string $location): never
{
    header('Location: ' . $location);
    exit;
}
