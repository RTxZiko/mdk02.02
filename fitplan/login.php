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
$identity = post_string('identity');

if (is_post_request()) {
    if (!($connection instanceof mysqli)) {
        $formError = 'База данных недоступна.';
    } else {
        $result = login_user($connection, $identity, post_string('password'));

        if ($result['ok']) {
            $planState = ensure_user_plan($connection, $result['user']);
            set_flash('success', $planState['created'] ? 'Аккаунт готов. Первый план создан.' : 'Вход выполнен.');
            redirect('index.php');
        }

        $formError = $result['error'];
    }
}

$pageTitle = 'Вход';
$pageStyles = ['pages/login.css'];
require __DIR__ . '/site/components/head.php';
require __DIR__ . '/site/components/header.php';
?>
<main class="layout layout--narrow auth-page auth-page--login">
    <section class="panel">
        <p class="eyebrow">Вход</p>
        <h1>Войдите в аккаунт</h1>
        <p class="muted">Используйте логин или email, чтобы продолжить.</p>

        <?php if ($dbError): ?>
            <div class="alert alert--error">Ошибка базы данных: <?= h($dbError) ?></div>
        <?php endif; ?>

        <?php if ($formError): ?>
            <div class="alert alert--error"><?= h($formError) ?></div>
        <?php endif; ?>

        <form class="stack" method="post">
            <input type="text" name="identity" value="<?= h($identity) ?>" placeholder="Логин или email" required>
            <input type="password" name="password" placeholder="Пароль" required>
            <button class="button" type="submit">Войти</button>
        </form>

        <p class="muted">Нет аккаунта? <a href="register.php">Зарегистрируйтесь</a></p>
    </section>
</main>
<?php require __DIR__ . '/site/components/footer.php'; ?>
