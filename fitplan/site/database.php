<?php
declare(strict_types=1);

function db_connection(): ?mysqli
{
    static $connection;
    static $initialized = false;

    if ($initialized) {
        return $connection instanceof mysqli ? $connection : null;
    }

    $initialized = true;
    $connection = @mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);

    if ($connection instanceof mysqli) {
        mysqli_set_charset($connection, 'utf8mb4');
        $GLOBALS['db_connection_error'] = null;
        return $connection;
    }

    $GLOBALS['db_connection_error'] = mysqli_connect_error() ?: 'Не удалось подключиться к базе данных.';

    return null;
}

function db_connection_error(): ?string
{
    db_connection();

    return $GLOBALS['db_connection_error'] ?? null;
}
