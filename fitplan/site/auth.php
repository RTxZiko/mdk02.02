<?php
declare(strict_types=1);

function current_user_id(): ?int
{
    return isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : null;
}

function sync_user_session(array $user): void
{
    $_SESSION['user_id'] = (int) $user['User_id'];
    $_SESSION['user_name'] = (string) $user['User_name'];
}

function clear_auth_session(): void
{
    unset($_SESSION['user_id'], $_SESSION['user_name']);
}

function find_user_by_id(mysqli $connection, int $userId): ?array
{
    $statement = mysqli_prepare($connection, 'SELECT User_id, Password_hash, User_name FROM Users WHERE User_id = ? LIMIT 1');
    mysqli_stmt_bind_param($statement, 'i', $userId);
    mysqli_stmt_execute($statement);
    $result = mysqli_stmt_get_result($statement);
    $user = mysqli_fetch_assoc($result) ?: null;
    mysqli_stmt_close($statement);

    return $user;
}

function find_user_by_identity(mysqli $connection, string $identity): ?array
{
    $statement = mysqli_prepare($connection, 'SELECT User_id, Password_hash, User_name FROM Users WHERE Mail = ? OR User_name = ? LIMIT 1');
    mysqli_stmt_bind_param($statement, 'ss', $identity, $identity);
    mysqli_stmt_execute($statement);
    $result = mysqli_stmt_get_result($statement);
    $user = mysqli_fetch_assoc($result) ?: null;
    mysqli_stmt_close($statement);

    return $user;
}

function update_user_password_hash(mysqli $connection, int $userId, string $hash): void
{
    $statement = mysqli_prepare($connection, 'UPDATE Users SET Password_hash = ? WHERE User_id = ?');
    mysqli_stmt_bind_param($statement, 'si', $hash, $userId);
    mysqli_stmt_execute($statement);
    mysqli_stmt_close($statement);
}

function authenticated_user(?mysqli $connection): ?array
{
    $userId = current_user_id();
    if (!$userId) {
        return null;
    }

    if (!($connection instanceof mysqli)) {
        return isset($_SESSION['user_name'])
            ? [
                'User_id' => $userId,
                'User_name' => (string) $_SESSION['user_name'],
            ]
            : null;
    }

    $user = find_user_by_id($connection, $userId);
    if ($user) {
        sync_user_session($user);
        return $user;
    }

    clear_auth_session();

    return null;
}

function password_is_valid(mysqli $connection, array $user, string $password): bool
{
    $storedPassword = (string) ($user['Password_hash'] ?? '');
    if ($storedPassword === '') {
        return false;
    }

    $passwordInfo = password_get_info($storedPassword);
    if (!empty($passwordInfo['algo'])) {
        return password_verify($password, $storedPassword);
    }

    $isValid = hash_equals($storedPassword, $password);
    if ($isValid) {
        update_user_password_hash($connection, (int) $user['User_id'], password_hash($password, PASSWORD_DEFAULT));
    }

    return $isValid;
}

function login_user(mysqli $connection, string $identity, string $password): array
{
    $identity = trim($identity);
    $password = trim($password);

    if ($identity === '' || $password === '') {
        return ['ok' => false, 'error' => 'Введите логин и пароль.', 'user' => null];
    }

    $user = find_user_by_identity($connection, $identity);
    if (!$user || !password_is_valid($connection, $user, $password)) {
        return ['ok' => false, 'error' => 'Неверный логин или пароль.', 'user' => null];
    }

    sync_user_session($user);

    return ['ok' => true, 'error' => null, 'user' => $user];
}

function register_user(mysqli $connection, string $userName, string $email, string $password, string $passwordConfirmation): array
{
    $userName = trim($userName);
    $email = trim($email);
    $password = trim($password);
    $passwordConfirmation = trim($passwordConfirmation);

    if ($userName === '' || $email === '' || $password === '' || $passwordConfirmation === '') {
        return ['ok' => false, 'error' => 'Заполните все поля.', 'user' => null];
    }

    if (strlen($userName) < 3 || strlen($password) < 3) {
        return ['ok' => false, 'error' => 'Логин и пароль должны быть не короче 3 символов.', 'user' => null];
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return ['ok' => false, 'error' => 'Введите корректный email.', 'user' => null];
    }

    if ($password !== $passwordConfirmation) {
        return ['ok' => false, 'error' => 'Пароли не совпадают.', 'user' => null];
    }

    if (find_user_by_identity($connection, $email) || find_user_by_identity($connection, $userName)) {
        return ['ok' => false, 'error' => 'Пользователь с таким email или логином уже существует.', 'user' => null];
    }

    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    $statement = mysqli_prepare($connection, 'INSERT INTO Users (Mail, Password_hash, User_name) VALUES (?, ?, ?)');
    mysqli_stmt_bind_param($statement, 'sss', $email, $passwordHash, $userName);
    mysqli_stmt_execute($statement);
    $userId = (int) mysqli_insert_id($connection);
    mysqli_stmt_close($statement);

    $user = find_user_by_id($connection, $userId);
    if (!$user) {
        return ['ok' => false, 'error' => 'Не удалось создать пользователя.', 'user' => null];
    }

    sync_user_session($user);

    return ['ok' => true, 'error' => null, 'user' => $user];
}
