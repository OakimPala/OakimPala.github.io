<?php
if (!defined('APP_BOOTSTRAPPED')) {
    http_response_code(403);
    exit('Forbidden');
}

try {
    $mysqli = db_connect();
} catch (Throwable $e) {
    echo '<div class="message error">Ошибка подключения к БД: ' . e($e->getMessage()) . '</div>';
    return;
}

$message = '';
$messageClass = '';

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];

    $res = mysqli_query($mysqli, 'SELECT surname FROM friends WHERE id = ' . $id . ' LIMIT 1');
    $row = $res ? mysqli_fetch_assoc($res) : null;

    if ($row) {
        if (mysqli_query($mysqli, 'DELETE FROM friends WHERE id = ' . $id)) {
            $message = 'Запись с фамилией ' . $row['surname'] . ' удалена';
            $messageClass = 'ok';
        } else {
            $message = 'Ошибка: запись не удалена';
            $messageClass = 'error';
        }
    } else {
        $message = 'Ошибка: запись не найдена';
        $messageClass = 'error';
    }
}

$listRes = mysqli_query(
    $mysqli,
    'SELECT id, surname, name, patronymic
     FROM friends
     ORDER BY surname ASC, name ASC, patronymic ASC, id ASC'
);

echo '<section class="panel">';
echo '<h2>Удаление записи</h2>';

if ($message !== '') {
    echo '<div class="message ' . e($messageClass) . '">' . e($message) . '</div>';
}

echo '<div class="record-links">';

if ($listRes) {
    while ($row = mysqli_fetch_assoc($listRes)) {
        $text = e($row['surname']) . ' ' . initials($row['name'], $row['patronymic']);

        echo '<a href="?p=delete&id=' . (int)$row['id'] . '" 
                 onclick="return confirm(\'Удалить: ' . addslashes(trim($text)) . '?\')">
                 ' . trim($text) . '
              </a>';
    }
}

echo '</div>';

if (!$listRes || mysqli_num_rows($listRes) === 0) {
    echo '<div class="message info">В таблице нет данных</div>';
}

echo '</section>';