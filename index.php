<?php
define('APP_BOOTSTRAPPED', true);
date_default_timezone_set('Europe/Moscow');
require_once __DIR__ . '/menu.php';

function db_connect(): mysqli
{
    $mysqli = mysqli_connect('localhost', "root", "", 'friends');
    if (!$mysqli) {
        throw new RuntimeException('Ошибка подключения к БД: ' . mysqli_connect_error());
    }

    mysqli_set_charset($mysqli, 'utf8mb4');
    return $mysqli;
}

function e($value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function firstLetter($value): string
{
    $value = trim((string)$value);
    if ($value === '') {
        return '';
    }

    if (function_exists('mb_substr')) {
        return mb_substr($value, 0, 1, 'UTF-8');
    }

    if (preg_match('/^./us', $value, $m)) {
        return $m[0];
    }

    return '';
}

function initials(?string $name, ?string $patronymic): string
{
    $out = '';
    $name = trim((string)$name);
    $patronymic = trim((string)$patronymic);

    if ($name !== '') {
        $out .= firstLetter($name) . '.';
    }
    if ($patronymic !== '') {
        $out .= firstLetter($patronymic) . '.';
    }

    return $out;
}
?>
<!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Записная книжка</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="app">
        <header class="app-header">
            <h1>Записная книжка</h1>
            <p>Лабораторная работа по PHP и MySQL</p>
        </header>

        <nav class="app-nav">
            <?php echo renderMenu(); ?>
        </nav>

        <main class="app-main">
            <?php
            $page = $_GET['p'] ?? 'viewer';

            switch ($page) {
                case 'viewer':
                    require_once __DIR__ . '/viewer.php';
                    $sort = $_GET['sort'] ?? 'byid';
                    $pageNum = isset($_GET['pg']) ? (int)$_GET['pg'] : 0;
                    if ($pageNum < 0) {
                        $pageNum = 0;
                    }
                    echo getFriendsList($sort, $pageNum);
                    break;

                case 'add':
                    require __DIR__ . '/add.php';
                    break;

                case 'edit':
                    require __DIR__ . '/edit.php';
                    break;

                case 'delete':
                    require __DIR__ . '/delete.php';
                    break;

                default:
                    echo '<div class="message error">Ошибка: неизвестный раздел</div>';
                    break;
            }
            ?>
        </main>
    </div>
</body>
</html>
