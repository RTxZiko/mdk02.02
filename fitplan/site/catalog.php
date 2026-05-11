<?php
declare(strict_types=1);

function find_exercise_by_id(mysqli $connection, int $exerciseId): ?array
{
    $statement = mysqli_prepare($connection, 'SELECT Exercise_id FROM Exercises WHERE Exercise_id = ? LIMIT 1');
    mysqli_stmt_bind_param($statement, 'i', $exerciseId);
    mysqli_stmt_execute($statement);
    $result = mysqli_stmt_get_result($statement);
    $exercise = mysqli_fetch_assoc($result) ?: null;
    mysqli_stmt_close($statement);

    return $exercise;
}

function fetch_exercises(mysqli $connection, array $filters = []): array
{
    $search = trim((string) ($filters['search'] ?? ''));
    $sql = 'SELECT e.Exercise_id, e.Exercise_name, e.Description,
                   primary_group.Group_name AS Primary_group_name,
                   secondary_group.Group_name AS Secondary_group_name
            FROM Exercises AS e
            LEFT JOIN MuscleGroups AS primary_group ON primary_group.Group_id = e.Primary_group_id
            LEFT JOIN MuscleGroups AS secondary_group ON secondary_group.Group_id = e.Secondary_group_id';

    $types = '';
    $params = [];

    if ($search !== '') {
        $sql .= ' WHERE e.Exercise_name LIKE ? OR e.Description LIKE ?';
        $types .= 'ss';
        $like = '%' . $search . '%';
        $params[] = $like;
        $params[] = $like;
    }

    $sql .= ' ORDER BY e.Exercise_name ASC';

    $statement = mysqli_prepare($connection, $sql);
    if ($types !== '') {
        mysqli_stmt_bind_param($statement, $types, ...$params);
    }
    mysqli_stmt_execute($statement);
    $result = mysqli_stmt_get_result($statement);

    $exercises = [];
    while ($exercise = mysqli_fetch_assoc($result)) {
        $exercises[] = $exercise;
    }

    mysqli_stmt_close($statement);

    return $exercises;
}
