<?php
if (!defined('APP_BOOTSTRAPPED')) {
    http_response_code(403);
    exit('Forbidden');
}

function renderMenu(): string
{
    $allowedPages = ['viewer', 'add', 'edit', 'delete'];
    $page = $_GET['p'] ?? 'viewer';
    if (!in_array($page, $allowedPages, true)) {
        $page = 'viewer';
    }
    $_GET['p'] = $page;

    $allowedSorts = ['byid', 'fam', 'birth'];
    $sort = $_GET['sort'] ?? 'byid';
    if (!in_array($sort, $allowedSorts, true)) {
        $sort = 'byid';
    }
    $_GET['sort'] = $sort;

    $mainItems = [
        'viewer' => 'Просмотр',
        'add' => 'Добавление записи',
        'edit' => 'Редактирование записи',
        'delete' => 'Удаление записи',
    ];

    $sortItems = [
        'byid' => 'По умолчанию',
        'fam' => 'По фамилии',
        'birth' => 'По дате рождения',
    ];

    $html = '<div class="menu">';
    foreach ($mainItems as $key => $label) {
        $class = 'menu-btn' . ($page === $key ? ' selected' : '');
        $html .= '<a class="' . $class . '" href="?p=' . $key . '">' . $label . '</a>';
    }
    $html .= '</div>';

    if ($page === 'viewer') {
        $html .= '<div class="submenu">';
        foreach ($sortItems as $key => $label) {
            $class = 'sub-btn' . ($sort === $key ? ' selected' : '');
            $html .= '<a class="' . $class . '" href="?p=viewer&amp;sort=' . $key . '">' . $label . '</a>';
        }
        $html .= '</div>';
    }

    return $html;
}
