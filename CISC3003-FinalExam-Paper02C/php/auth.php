<?php

declare(strict_types=1);

require_once __DIR__ . '/helpers.php';

function current_user_id(): ?int
{
    require_session();
    $id = $_SESSION['user_id'] ?? null;
    if (!is_int($id)) {
        return null;
    }
    return $id;
}

function login_user(int $userId): void
{
    require_session();
    session_regenerate_id(true);
    $_SESSION['user_id'] = $userId;
}

function logout_user(): void
{
    require_session();
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }
    session_destroy();
}

function require_login(): void
{
    if (current_user_id() === null) {
        redirect('login.php');
    }
}

