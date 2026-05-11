<?php
declare(strict_types=1);

require_once __DIR__ . '/site/bootstrap.php';

$connection = db_connection();
$currentUser = authenticated_user($connection);
$dbError = db_connection_error();

$successMessage = pull_flash('success');
$infoMessage = pull_flash('info');
$errorMessage = pull_flash('error');

$requestedPlanId = $currentUser ? query_int('plan_id') : null;
$exerciseSearch = query_string('q');

$planSnapshot = ['plan' => null, 'plans' => [], 'days' => []];
$exercises = [];

if ($connection instanceof mysqli) {
    $exercises = fetch_exercises($connection, ['search' => $exerciseSearch]);

    if ($currentUser) {
        $planSnapshot = fetch_plan_snapshot($connection, $currentUser, $requestedPlanId);
    }
}

$activePlan = $planSnapshot['plan'];
$userPlans = $planSnapshot['plans'];
$planDays = $planSnapshot['days'];
$activePlanId = $activePlan ? (int) $activePlan['Plan_id'] : null;
$currentPage = build_url('index.php', [
    'plan_id' => $activePlanId,
    'q' => $exerciseSearch !== '' ? $exerciseSearch : null,
]);
$alerts = [
    'success' => $successMessage,
    'info' => $infoMessage,
    'error' => $errorMessage,
];
$exerciseCount = 0;

foreach ($planDays as $day) {
    $exerciseCount += count($day['items']);
}

$pageTitle = APP_NAME;
$pageStyles = ['pages/index.css'];

require __DIR__ . '/site/components/head.php';
require __DIR__ . '/site/components/header.php';
?>
<main class="layout">
    <?php foreach ($alerts as $type => $message): ?>
        <?php if ($message): ?>
            <div class="alert alert--<?= h($type) ?>"><?= h($message) ?></div>
        <?php endif; ?>
    <?php endforeach; ?>
    <?php if ($dbError): ?>
        <div class="alert alert--error">Ошибка базы данных: <?= h($dbError) ?></div>
    <?php endif; ?>

    <section class="panel hero">
        <div>
            <p class="eyebrow">Простой планировщик тренировок</p>
            <h1>Соберите недельный план тренировок из каталога упражнений.</h1>
        </div>
        <div class="hero__meta">
            <strong><?= $currentUser ? h($currentUser['User_name']) : 'Гость' ?></strong>
            <span><?= $currentUser ? count($userPlans) . ' план(ов)' : count($exercises) . ' упражнений' ?></span>
        </div>
    </section>

    <?php if (!$currentUser): ?>
        <section class="panel">
            <h2>Начните здесь</h2>
            <p class="muted">Войдите, чтобы создать личный план, или просмотрите каталог упражнений ниже.</p>
            <div class="actions">
                <a class="button" href="login.php">Войти</a>
                <a class="button button--ghost" href="register.php">Зарегистрироваться</a>
            </div>
        </section>
    <?php else: ?>
        <section class="panel">
            <div class="panel__header">
                <div>
                    <p class="eyebrow">Мой план</p>
                    <h2><?= $activePlan ? h($activePlan['Plan_name']) : 'Нет плана' ?></h2>
                    <p class="muted">В текущем плане: <?= $exerciseCount ?> упражнений.</p>
                </div>
                <div class="plan-links">
                    <?php foreach ($userPlans as $plan): ?>
                        <a class="<?= (int) $plan['Plan_id'] === $activePlanId ? 'chip is-active' : 'chip' ?>" href="<?= h(build_url('index.php', ['plan_id' => (int) $plan['Plan_id']])) ?>">
                            <?= h($plan['Plan_name']) ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="forms">
                <form class="inline-form" method="post" action="actions.php">
                    <?= csrf_input() ?>
                    <input type="hidden" name="action" value="plan_create">
                    <input type="hidden" name="redirect_to" value="<?= h($currentPage) ?>">
                    <input type="text" name="plan_name" placeholder="Название нового плана" required>
                    <button class="button" type="submit">Создать план</button>
                </form>

                <?php if ($activePlanId): ?>
                    <form class="inline-form" method="post" action="actions.php">
                        <?= csrf_input() ?>
                        <input type="hidden" name="action" value="plan_update">
                        <input type="hidden" name="plan_id" value="<?= $activePlanId ?>">
                        <input type="hidden" name="redirect_to" value="<?= h($currentPage) ?>">
                        <input type="text" name="plan_name" value="<?= h($activePlan['Plan_name']) ?>" required>
                        <button class="button" type="submit">Переименовать</button>
                    </form>

                    <form method="post" action="actions.php">
                        <?= csrf_input() ?>
                        <input type="hidden" name="action" value="plan_delete">
                        <input type="hidden" name="plan_id" value="<?= $activePlanId ?>">
                        <input type="hidden" name="redirect_to" value="<?= h($currentPage) ?>">
                        <button class="button button--danger" type="submit">Удалить план</button>
                    </form>
                <?php endif; ?>
            </div>
        </section>

        <section class="grid">
            <?php foreach ($planDays as $day): ?>
                <article class="panel">
                    <div class="day-head">
                        <h3><?= h($day['name']) ?></h3>
                        <span class="badge"><?= count($day['items']) ?></span>
                    </div>

                    <?php if ($day['items'] === []): ?>
                        <p class="muted">Пока нет упражнений.</p>
                    <?php else: ?>
                        <ul class="item-list">
                            <?php foreach ($day['items'] as $item): ?>
                                <li class="item">
                                    <div>
                                        <strong><?= h($item['name']) ?></strong>
                                        <?php if ($item['group'] !== ''): ?>
                                            <p class="muted"><?= h($item['group']) ?></p>
                                        <?php endif; ?>
                                    </div>
                                    <form method="post" action="actions.php">
                                        <?= csrf_input() ?>
                                        <input type="hidden" name="action" value="workout_item_delete">
                                        <input type="hidden" name="workout_exercise_id" value="<?= (int) $item['id'] ?>">
                                        <input type="hidden" name="redirect_to" value="<?= h($currentPage) ?>">
                                        <button class="button button--ghost" type="submit">Удалить</button>
                                    </form>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </article>
            <?php endforeach; ?>
        </section>
    <?php endif; ?>

    <section class="panel">
        <div class="panel__header">
            <div>
                <p class="eyebrow">Каталог</p>
                <h2>Упражнения</h2>
            </div>
            <form class="inline-form inline-form--search" method="get">
                <?php if ($activePlanId): ?>
                    <input type="hidden" name="plan_id" value="<?= $activePlanId ?>">
                <?php endif; ?>
                <input type="text" name="q" value="<?= h($exerciseSearch) ?>" placeholder="Поиск упражнения">
                <button class="button button--ghost" type="submit">Найти</button>
            </form>
        </div>

        <div class="catalog">
            <?php foreach ($exercises as $exercise): ?>
                <article class="card">
                    <h3><?= h($exercise['Exercise_name']) ?></h3>
                    <?php if (!empty($exercise['Description'])): ?>
                        <p class="muted"><?= h($exercise['Description']) ?></p>
                    <?php endif; ?>
                    <p class="muted"><?= h(trim(((string) ($exercise['Primary_group_name'] ?? '')) . ' ' . ((string) ($exercise['Secondary_group_name'] ?? '')))) ?></p>

                    <?php if ($currentUser && $activePlanId): ?>
                        <form class="inline-form" method="post" action="actions.php">
                            <?= csrf_input() ?>
                            <input type="hidden" name="action" value="workout_item_add">
                            <input type="hidden" name="plan_id" value="<?= $activePlanId ?>">
                            <input type="hidden" name="exercise_id" value="<?= (int) $exercise['Exercise_id'] ?>">
                            <input type="hidden" name="redirect_to" value="<?= h($currentPage) ?>">
                            <select name="day_id">
                                <?php foreach (PLAN_DAYS as $dayId => $dayName): ?>
                                    <option value="<?= $dayId ?>"><?= h($dayName) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <button class="button" type="submit">Добавить</button>
                        </form>
                    <?php endif; ?>
                </article>
            <?php endforeach; ?>
        </div>
    </section>
</main>
<?php require __DIR__ . '/site/components/footer.php'; ?>
