<?php
if (!defined('APP_BOOTSTRAPPED')) {
    http_response_code(403);
    exit('Forbidden');
}

function getFriendsList(string $type, int $page): string
{
    try {
        $mysqli = db_connect();
    } catch (Throwable $e) {
        return '<div class="message error">Ошибка подключения к БД: ' . e($e->getMessage()) . '</div>';
    }

    $orderBy = 'id ASC';
    $sortCaption = 'по умолчанию';

    if ($type === 'fam') {
        $orderBy = 'surname ASC, name ASC, patronymic ASC, id ASC';
        $sortCaption = 'по фамилии';
    } elseif ($type === 'birth') {
        $orderBy = 'birth_date ASC, surname ASC, name ASC, id ASC';
        $sortCaption = 'по дате рождения';
    }

    $countRes = mysqli_query($mysqli, 'SELECT COUNT(*) AS cnt FROM friends');
    if (!$countRes) {
        return '<div class="message error">Ошибка базы данных</div>';
    }

    $countRow = mysqli_fetch_assoc($countRes);
    $total = (int)($countRow['cnt'] ?? 0);

    if ($total === 0) {
        return '<div class="message info">В таблице нет данных</div>';
    }

    $pageSize = 10;
    $pages = (int)ceil($total / $pageSize);

    if ($page < 0) {
        $page = 0;
    }
    if ($page >= $pages) {
        $page = $pages - 1;
    }

    $offset = $page * $pageSize;

    $sql = "SELECT id, surname, name, patronymic, sex, birth_date, phone, address, email, comment
            FROM friends
            ORDER BY {$orderBy}
            LIMIT {$offset}, {$pageSize}";

    $res = mysqli_query($mysqli, $sql);
    if (!$res) {
        return '<div class="message error">Ошибка базы данных</div>';
    }

    $html = '<section class="panel">';
    $html .= '<h2>Просмотр контактов</h2>';
    $html .= '<div class="hint">Сортировка: ' . e($sortCaption) . '</div>';

    $html .= '<div class="table-wrap">';
    $html .= '<table class="contacts-table">';
    $html .= '<thead><tr>';
    $html .= '<th>№</th>'; 
    $html .= '<th>ID</th><th>Фамилия</th><th>Имя</th><th>Отчество</th><th>Пол</th><th>Дата рождения</th><th>Телефон</th><th>Адрес</th><th>E-mail</th><th>Комментарий</th>';
    $html .= '</tr></thead><tbody>';

    
    $counter = $offset + 1;

    while ($row = mysqli_fetch_assoc($res)) {
        $html .= '<tr>';

    
        $html .= '<td>' . $counter++ . '</td>';


        $html .= '<td>' . e($row['surname']) . '</td>';
        $html .= '<td>' . e($row['name']) . '</td>';
        $html .= '<td>' . e($row['patronymic']) . '</td>';
        $html .= '<td>' . e($row['sex']) . '</td>';
        $html .= '<td>' . e($row['birth_date']) . '</td>';
        $html .= '<td>' . e($row['phone']) . '</td>';
        $html .= '<td>' . e($row['address']) . '</td>';
        $html .= '<td>' . e($row['email']) . '</td>';
        $html .= '<td>' . e($row['comment']) . '</td>';

        $html .= '</tr>';
    }

    $html .= '</tbody></table>';
    $html .= '</div>';

    if ($pages > 1) {
        $html .= '<div class="pagination">';
        $html .= '<span class="pagination-label">Страницы:</span>';

        for ($i = 0; $i < $pages; $i++) {
            if ($i === $page) {
                $html .= '<span class="page current">' . ($i + 1) . '</span>';
            } else {
                $html .= '<a class="page" href="?p=viewer&amp;sort=' . e($type) . '&amp;pg=' . $i . '">' . ($i + 1) . '</a>';
            }
        }

        $html .= '</div>';
    }

    $html .= '</section>';

    return $html;
}