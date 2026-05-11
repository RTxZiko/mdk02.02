<?php
declare(strict_types=1);

require_once __DIR__ . '/site/bootstrap.php';

$connection = db_connection();
$currentUser = authenticated_user($connection);

if ($currentUser) {
    redirect('index.php');
}

$dbError = db_connection_error();
$formError = null;
$form = [
    'user_name' => post_string('user_name'),
    'email' => post_string('email'),
];

if (is_post_request()) {
    if (!($connection instanceof mysqli)) {
        $formError = 'База данных недоступна.';
    } else {
        $result = register_user(
            $connection,
            $form['user_name'],
            $form['email'],
            post_string('password'),
            post_string('password_confirmation')
        );

        if ($result['ok']) {
            $planState = ensure_user_plan($connection, $result['user']);
            set_flash('success', $planState['created'] ? 'Аккаунт создан.' : 'Аккаунт создан, существующий план загружен.');
            redirect('index.php');
        }

        $formError = $result['error'];
    }
}

$pageTitle = 'Регистрация';
$pageStyles = ['pages/register.css'];
require __DIR__ . '/site/components/head.php';
require __DIR__ . '/site/components/header.php';
?>
<main class="layout layout--narrow auth-page auth-page--register">
    <section class="panel">
        <p class="eyebrow">Регистрация</p>
        <h1>Создание аккаунта</h1>
        <p class="muted">После регистрации стартовый план будет создан автоматически.</p>

        <?php if ($dbError): ?>
            <div class="alert alert--error">Ошибка базы данных: <?= h($dbError) ?></div>
        <?php endif; ?>

        <?php if ($formError): ?>
            <div class="alert alert--error"><?= h($formError) ?></div>
        <?php endif; ?>

        <form class="stack" method="post">
            <input type="text" name="user_name" value="<?= h($form['user_name']) ?>" placeholder="Логин" required>
            <input type="email" name="email" value="<?= h($form['email']) ?>" placeholder="Email" required>
            <input type="password" name="password" placeholder="Пароль" required>
            <input type="password" name="password_confirmation" placeholder="Повторите пароль" required>
            <button class="button" type="submit">Зарегистрироваться</button>
        </form>

        <p class="muted">Уже зарегистрированы? <a href="login.php">Войти</a></p>
    </section>
</main>
<?php require __DIR__ . '/site/components/footer.php'; ?>
