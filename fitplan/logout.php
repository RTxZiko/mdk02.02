<?php
declare(strict_types=1);

require_once __DIR__ . '/site/bootstrap.php';

clear_auth_session();
set_flash('info', 'Вы вышли из аккаунта.');
redirect('index.php');
