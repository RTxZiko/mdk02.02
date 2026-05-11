<?php
$pageTitle = $pageTitle ?? APP_NAME;
$sharedStyles = ['base.css', 'layout.css', 'components.css'];
$pageStyles = $pageStyles ?? [];
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= h($pageTitle) ?></title>
    <?php foreach (array_merge($sharedStyles, $pageStyles) as $style): ?>
        <link rel="stylesheet" href="assets/css/<?= h($style) ?>">
    <?php endforeach; ?>
</head>
<body>
