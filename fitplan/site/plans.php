<?php
declare(strict_types=1);

function find_plan_by_id(mysqli $connection, int $planId, int $userId): ?array
{
    $statement = mysqli_prepare(
        $connection,
        'SELECT Plan_id, Plan_name
         FROM TrainingPlans
         WHERE Plan_id = ? AND User_id = ?
         LIMIT 1'
    );
    mysqli_stmt_bind_param($statement, 'ii', $planId, $userId);
    mysqli_stmt_execute($statement);
    $result = mysqli_stmt_get_result($statement);
    $plan = mysqli_fetch_assoc($result) ?: null;
    mysqli_stmt_close($statement);

    return $plan;
}

function list_user_plans(mysqli $connection, int $userId): array
{
    $statement = mysqli_prepare(
        $connection,
        'SELECT Plan_id, Plan_name
         FROM TrainingPlans
         WHERE User_id = ?
         ORDER BY Updated_at DESC, Plan_id DESC'
    );
    mysqli_stmt_bind_param($statement, 'i', $userId);
    mysqli_stmt_execute($statement);
    $result = mysqli_stmt_get_result($statement);

    $plans = [];
    while ($plan = mysqli_fetch_assoc($result)) {
        $plans[] = $plan;
    }

    mysqli_stmt_close($statement);

    return $plans;
}

function create_user_plan(mysqli $connection, int $userId, string $userName, ?string $planName = null): ?array
{
    $name = trim((string) $planName);
    if ($name === '') {
        $name = 'План ' . ($userName !== '' ? $userName : 'пользователя');
    }

    $timestamp = date('Y-m-d H:i:s');
    $statement = mysqli_prepare($connection, 'INSERT INTO TrainingPlans (User_id, Plan_name, Created_at, Updated_at) VALUES (?, ?, ?, ?)');
    mysqli_stmt_bind_param($statement, 'isss', $userId, $name, $timestamp, $timestamp);
    mysqli_stmt_execute($statement);
    $planId = (int) mysqli_insert_id($connection);
    mysqli_stmt_close($statement);

    return $planId > 0 ? find_plan_by_id($connection, $planId, $userId) : null;
}

function ensure_user_plan(mysqli $connection, array $user): array
{
    $plans = list_user_plans($connection, (int) $user['User_id']);
    if ($plans !== []) {
        return ['plan' => $plans[0], 'created' => false];
    }

    return [
        'plan' => create_user_plan($connection, (int) $user['User_id'], (string) $user['User_name']),
        'created' => true,
    ];
}

function touch_plan(mysqli $connection, int $planId): void
{
    $timestamp = date('Y-m-d H:i:s');
    $statement = mysqli_prepare($connection, 'UPDATE TrainingPlans SET Updated_at = ? WHERE Plan_id = ?');
    mysqli_stmt_bind_param($statement, 'si', $timestamp, $planId);
    mysqli_stmt_execute($statement);
    mysqli_stmt_close($statement);
}

function ensure_workout(mysqli $connection, int $planId, int $dayId): int
{
    $statement = mysqli_prepare($connection, 'SELECT Workout_id FROM Workouts WHERE Plan_id = ? AND Day_id = ? LIMIT 1');
    mysqli_stmt_bind_param($statement, 'ii', $planId, $dayId);
    mysqli_stmt_execute($statement);
    $result = mysqli_stmt_get_result($statement);
    $workout = mysqli_fetch_assoc($result) ?: null;
    mysqli_stmt_close($statement);

    if ($workout) {
        return (int) $workout['Workout_id'];
    }

    $timestamp = date('Y-m-d H:i:s');
    $statement = mysqli_prepare($connection, 'INSERT INTO Workouts (Plan_id, Day_id, Created_at) VALUES (?, ?, ?)');
    mysqli_stmt_bind_param($statement, 'iis', $planId, $dayId, $timestamp);
    mysqli_stmt_execute($statement);
    $workoutId = (int) mysqli_insert_id($connection);
    mysqli_stmt_close($statement);

    return $workoutId;
}

function normalize_workout_order(mysqli $connection, int $workoutId): void
{
    $statement = mysqli_prepare(
        $connection,
        'SELECT Workout_exercise_id
         FROM WorkoutsExercises
         WHERE Workout_id = ?
         ORDER BY Order_in_workout ASC, Workout_exercise_id ASC'
    );
    mysqli_stmt_bind_param($statement, 'i', $workoutId);
    mysqli_stmt_execute($statement);
    $result = mysqli_stmt_get_result($statement);

    $position = 0;
    $itemId = 0;
    $update = mysqli_prepare($connection, 'UPDATE WorkoutsExercises SET Order_in_workout = ? WHERE Workout_exercise_id = ?');
    mysqli_stmt_bind_param($update, 'ii', $position, $itemId);

    while ($row = mysqli_fetch_assoc($result)) {
        $position++;
        $itemId = (int) $row['Workout_exercise_id'];
        mysqli_stmt_execute($update);
    }

    mysqli_stmt_close($update);
    mysqli_stmt_close($statement);
}

function cleanup_empty_workout(mysqli $connection, int $workoutId): void
{
    $statement = mysqli_prepare($connection, 'SELECT COUNT(*) AS total FROM WorkoutsExercises WHERE Workout_id = ?');
    mysqli_stmt_bind_param($statement, 'i', $workoutId);
    mysqli_stmt_execute($statement);
    $result = mysqli_stmt_get_result($statement);
    $row = mysqli_fetch_assoc($result);
    mysqli_stmt_close($statement);

    if ((int) ($row['total'] ?? 0) > 0) {
        return;
    }

    $delete = mysqli_prepare($connection, 'DELETE FROM Workouts WHERE Workout_id = ?');
    mysqli_stmt_bind_param($delete, 'i', $workoutId);
    mysqli_stmt_execute($delete);
    mysqli_stmt_close($delete);
}

function create_plan(mysqli $connection, array $user, string $planName): array
{
    $planName = trim($planName);
    if ($planName === '') {
        return ['ok' => false, 'error' => 'Введите название плана.', 'plan' => null];
    }

    $plan = create_user_plan($connection, (int) $user['User_id'], (string) $user['User_name'], $planName);

    return ['ok' => $plan !== null, 'error' => $plan ? null : 'Не удалось создать план.', 'plan' => $plan];
}

function update_plan(mysqli $connection, array $user, int $planId, string $planName): array
{
    if (!find_plan_by_id($connection, $planId, (int) $user['User_id'])) {
        return ['ok' => false, 'error' => 'План не найден.', 'plan' => null];
    }

    $planName = trim($planName);
    if ($planName === '') {
        return ['ok' => false, 'error' => 'Введите название плана.', 'plan' => null];
    }

    $timestamp = date('Y-m-d H:i:s');
    $statement = mysqli_prepare($connection, 'UPDATE TrainingPlans SET Plan_name = ?, Updated_at = ? WHERE Plan_id = ? AND User_id = ?');
    $userId = (int) $user['User_id'];
    mysqli_stmt_bind_param($statement, 'ssii', $planName, $timestamp, $planId, $userId);
    mysqli_stmt_execute($statement);
    mysqli_stmt_close($statement);

    return ['ok' => true, 'error' => null, 'plan' => find_plan_by_id($connection, $planId, $userId)];
}

function delete_plan(mysqli $connection, array $user, int $planId): array
{
    $userId = (int) $user['User_id'];

    if (!find_plan_by_id($connection, $planId, $userId)) {
        return ['ok' => false, 'error' => 'План не найден.'];
    }

    $deleteItems = mysqli_prepare(
        $connection,
        'DELETE we
         FROM WorkoutsExercises AS we
         INNER JOIN Workouts AS w ON w.Workout_id = we.Workout_id
         WHERE w.Plan_id = ?'
    );
    mysqli_stmt_bind_param($deleteItems, 'i', $planId);
    mysqli_stmt_execute($deleteItems);
    mysqli_stmt_close($deleteItems);

    $deleteWorkouts = mysqli_prepare($connection, 'DELETE FROM Workouts WHERE Plan_id = ?');
    mysqli_stmt_bind_param($deleteWorkouts, 'i', $planId);
    mysqli_stmt_execute($deleteWorkouts);
    mysqli_stmt_close($deleteWorkouts);

    $deletePlan = mysqli_prepare($connection, 'DELETE FROM TrainingPlans WHERE Plan_id = ? AND User_id = ?');
    mysqli_stmt_bind_param($deletePlan, 'ii', $planId, $userId);
    mysqli_stmt_execute($deletePlan);
    mysqli_stmt_close($deletePlan);

    return ['ok' => true, 'error' => null];
}

function find_workout_item_by_id(mysqli $connection, int $workoutExerciseId): ?array
{
    $statement = mysqli_prepare(
        $connection,
        'SELECT we.Workout_exercise_id, we.Workout_id, we.Exercise_id, w.Plan_id
         FROM WorkoutsExercises AS we
         INNER JOIN Workouts AS w ON w.Workout_id = we.Workout_id
         WHERE we.Workout_exercise_id = ?
         LIMIT 1'
    );
    mysqli_stmt_bind_param($statement, 'i', $workoutExerciseId);
    mysqli_stmt_execute($statement);
    $result = mysqli_stmt_get_result($statement);
    $item = mysqli_fetch_assoc($result) ?: null;
    mysqli_stmt_close($statement);

    return $item;
}

function add_exercise_to_plan(mysqli $connection, array $user, int $planId, int $exerciseId, int $dayId): array
{
    if (!isset(PLAN_DAYS[$dayId])) {
        return ['ok' => false, 'error' => 'Выберите корректный день.'];
    }

    if (!find_plan_by_id($connection, $planId, (int) $user['User_id'])) {
        return ['ok' => false, 'error' => 'План не найден.'];
    }

    if (!find_exercise_by_id($connection, $exerciseId)) {
        return ['ok' => false, 'error' => 'Упражнение не найдено.'];
    }

    $workoutId = ensure_workout($connection, $planId, $dayId);

    $statement = mysqli_prepare($connection, 'SELECT COALESCE(MAX(Order_in_workout), 0) AS max_order FROM WorkoutsExercises WHERE Workout_id = ?');
    mysqli_stmt_bind_param($statement, 'i', $workoutId);
    mysqli_stmt_execute($statement);
    $result = mysqli_stmt_get_result($statement);
    $row = mysqli_fetch_assoc($result);
    mysqli_stmt_close($statement);

    $nextOrder = (int) ($row['max_order'] ?? 0) + 1;
    $insert = mysqli_prepare($connection, 'INSERT INTO WorkoutsExercises (Workout_id, Exercise_id, Order_in_workout) VALUES (?, ?, ?)');
    mysqli_stmt_bind_param($insert, 'iii', $workoutId, $exerciseId, $nextOrder);
    mysqli_stmt_execute($insert);
    mysqli_stmt_close($insert);

    touch_plan($connection, $planId);

    return ['ok' => true, 'error' => null];
}

function delete_workout_item(mysqli $connection, array $user, int $workoutExerciseId): array
{
    $item = find_workout_item_by_id($connection, $workoutExerciseId);
    if (!$item || !find_plan_by_id($connection, (int) $item['Plan_id'], (int) $user['User_id'])) {
        return ['ok' => false, 'error' => 'Элемент тренировки не найден.'];
    }

    $delete = mysqli_prepare($connection, 'DELETE FROM WorkoutsExercises WHERE Workout_exercise_id = ?');
    mysqli_stmt_bind_param($delete, 'i', $workoutExerciseId);
    mysqli_stmt_execute($delete);
    mysqli_stmt_close($delete);

    normalize_workout_order($connection, (int) $item['Workout_id']);
    cleanup_empty_workout($connection, (int) $item['Workout_id']);
    touch_plan($connection, (int) $item['Plan_id']);

    return ['ok' => true, 'error' => null];
}

function fetch_plan_snapshot(mysqli $connection, array $user, ?int $requestedPlanId = null): array
{
    $state = ensure_user_plan($connection, $user);
    $plans = list_user_plans($connection, (int) $user['User_id']);
    $activePlan = $state['plan'];

    if ($requestedPlanId !== null) {
        $requestedPlan = find_plan_by_id($connection, $requestedPlanId, (int) $user['User_id']);
        if ($requestedPlan) {
            $activePlan = $requestedPlan;
        }
    }

    $days = [];
    foreach (PLAN_DAYS as $dayId => $dayName) {
        $days[$dayId] = [
            'name' => $dayName,
            'items' => [],
        ];
    }

    if ($activePlan) {
        $statement = mysqli_prepare(
            $connection,
            'SELECT w.Day_id, we.Workout_exercise_id, e.Exercise_name,
                    primary_group.Group_name AS Primary_group_name,
                    secondary_group.Group_name AS Secondary_group_name
             FROM Workouts AS w
             INNER JOIN WorkoutsExercises AS we ON we.Workout_id = w.Workout_id
             INNER JOIN Exercises AS e ON e.Exercise_id = we.Exercise_id
             LEFT JOIN MuscleGroups AS primary_group ON primary_group.Group_id = e.Primary_group_id
             LEFT JOIN MuscleGroups AS secondary_group ON secondary_group.Group_id = e.Secondary_group_id
             WHERE w.Plan_id = ?
             ORDER BY w.Day_id ASC, we.Order_in_workout ASC, we.Workout_exercise_id ASC'
        );
        $planId = (int) $activePlan['Plan_id'];
        mysqli_stmt_bind_param($statement, 'i', $planId);
        mysqli_stmt_execute($statement);
        $result = mysqli_stmt_get_result($statement);

        while ($row = mysqli_fetch_assoc($result)) {
            $dayId = (int) $row['Day_id'];
            $groups = trim(((string) ($row['Primary_group_name'] ?? '')) . ' ' . ((string) ($row['Secondary_group_name'] ?? '')));
            $days[$dayId]['items'][] = [
                'id' => (int) $row['Workout_exercise_id'],
                'name' => (string) $row['Exercise_name'],
                'group' => $groups,
            ];
        }

        mysqli_stmt_close($statement);
    }

    return [
        'plan' => $activePlan,
        'plans' => $plans,
        'days' => array_values($days),
    ];
}
