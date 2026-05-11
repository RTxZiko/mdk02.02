<header class="site-header">
    <div class="site-header__inner">
        <a class="brand" href="index.php"><?= h(APP_NAME) ?></a>

        <nav class="top-nav">
            <a href="index.php">Главная</a>
            <?php if (!empty($currentUser)): ?>
                <span><?= h($currentUser['User_name']) ?></span>
                <a href="logout.php">Выйти</a>
            <?php else: ?>
                <a href="login.php">Вход</a>
                <a href="register.php">Регистрация</a>
            <?php endif; ?>
        </nav>
    </div>
</header>
