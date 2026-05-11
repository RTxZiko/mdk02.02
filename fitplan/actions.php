<?php
declare(strict_types=1);

require_once __DIR__ . '/site/bootstrap.php';

if (!is_post_request()) {
    redirect('index.php');
}

$redirectTo = safe_redirect_target(post_string('redirect_to'), 'index.php');

if (!verify_csrf_request()) {
    set_flash('error', 'Сессия формы истекла.');
    redirect($redirectTo);
}

$connection = db_connection();
if (!($connection instanceof mysqli)) {
    set_flash('error', 'База данных недоступна.');
    redirect($redirectTo);
}

$currentUser = authenticated_user($connection);
if (!$currentUser) {
    set_flash('error', 'Необходимо войти в аккаунт.');
    redirect('login.php');
}

$action = post_string('action');
$successMessage = null;
$errorMessage = null;
$finalRedirect = $redirectTo;

mysqli_begin_transaction($connection);

switch ($action) {
    case 'plan_create':
        $result = create_plan($connection, $currentUser, post_string('plan_name'));
        if ($result['ok']) {
            $successMessage = 'План создан.';
            $finalRedirect = build_url('index.php', ['plan_id' => (int) $result['plan']['Plan_id']]);
        } else {
            $errorMessage = $result['error'];
        }
        break;

    case 'plan_update':
        $result = update_plan($connection, $currentUser, (int) post_int('plan_id'), post_string('plan_name'));
        if ($result['ok']) {
            $successMessage = 'План обновлён.';
            $finalRedirect = build_url('index.php', ['plan_id' => (int) $result['plan']['Plan_id']]);
        } else {
            $errorMessage = $result['error'];
        }
        break;

    case 'plan_delete':
        $planId = (int) post_int('plan_id');
        $result = delete_plan($connection, $currentUser, $planId);
        if ($result['ok']) {
            $remainingPlans = list_user_plans($connection, (int) $currentUser['User_id']);
            $successMessage = 'План удалён.';
            $finalRedirect = $remainingPlans ? build_url('index.php', ['plan_id' => (int) $remainingPlans[0]['Plan_id']]) : 'index.php';
        } else {
            $errorMessage = $result['error'];
        }
        break;

    case 'workout_item_add':
        $result = add_exercise_to_plan(
            $connection,
            $currentUser,
            (int) post_int('plan_id'),
            (int) post_int('exercise_id'),
            (int) post_int('day_id')
        );
        if ($result['ok']) {
            $successMessage = 'Упражнение добавлено в план.';
        } else {
            $errorMessage = $result['error'];
        }
        break;

    case 'workout_item_delete':
        $result = delete_workout_item($connection, $currentUser, (int) post_int('workout_exercise_id'));
        if ($result['ok']) {
            $successMessage = 'Упражнение удалено из плана.';
        } else {
            $errorMessage = $result['error'];
        }
        break;

    default:
        $errorMessage = 'Неизвестное действие.';
        break;
}

if ($errorMessage === null) {
    mysqli_commit($connection);
    if ($successMessage !== null) {
        set_flash('success', $successMessage);
    }
} else {
    mysqli_rollback($connection);
    set_flash('error', $errorMessage);
}

redirect($finalRedirect);
