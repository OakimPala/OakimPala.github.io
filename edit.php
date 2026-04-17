<?php
if (!defined('APP_BOOTSTRAPPED')) {
    http_response_code(403);
    exit('Forbidden');
}

$message = '';
$messageClass = '';

try {
    $mysqli = db_connect();
} catch (Throwable $e) {
    echo '<div class="message error">Ошибка подключения к БД: ' . e($e->getMessage()) . '</div>';
    return;
}


if (isset($_GET['success'])) {
    $message = 'Данные изменены';
    $messageClass = 'ok';
}

if (isset($_GET['error'])) {
    $message = 'Ошибка: данные не изменены';
    $messageClass = 'error';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['button'] ?? '') === 'Изменить запись') {

    $id = (int)($_POST['id'] ?? 0);

    $surname = trim((string)($_POST['surname'] ?? ''));
    $name = trim((string)($_POST['name'] ?? ''));
    $patronymic = trim((string)($_POST['patronymic'] ?? ''));
    $sex = trim((string)($_POST['sex'] ?? 'М'));
    $birthDate = trim((string)($_POST['birth_date'] ?? ''));
    $phone = trim((string)($_POST['phone'] ?? ''));
    $address = trim((string)($_POST['address'] ?? ''));
    $email = trim((string)($_POST['email'] ?? ''));
    $comment = trim((string)($_POST['comment'] ?? ''));

    if (!in_array($sex, ['М', 'Ж'], true)) {
        $sex = 'М';
    }

    if ($birthDate === '') {
        $birthDate = date('Y-m-d');
    }

    $stmt = mysqli_prepare(
        $mysqli,
        'UPDATE friends
         SET surname=?, name=?, patronymic=?, sex=?, birth_date=?, phone=?, address=?, email=?, comment=?
         WHERE id=?'
    );

    if ($stmt) {
        mysqli_stmt_bind_param(
            $stmt,
            'sssssssssi',
            $surname,
            $name,
            $patronymic,
            $sex,
            $birthDate,
            $phone,
            $address,
            $email,
            $comment,
            $id
        );

        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);

            header('Location: ?p=edit&id=' . $id . '&success=1');
            exit;
        }

        mysqli_stmt_close($stmt);
    }

    header('Location: ?p=edit&id=' . $id . '&error=1');
    exit;
}



$currentRow = null;
$currentId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($currentId > 0) {
    $stmt = mysqli_prepare(
        $mysqli,
        'SELECT id, surname, name, patronymic, sex, birth_date, phone, address, email, comment
         FROM friends
         WHERE id = ?
         LIMIT 1'
    );

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 'i', $currentId);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        $currentRow = mysqli_fetch_assoc($res) ?: null;
        mysqli_stmt_close($stmt);
    }
}


if (!$currentRow) {
    $res = mysqli_query(
        $mysqli,
        'SELECT id, surname, name, patronymic, sex, birth_date, phone, address, email, comment
         FROM friends
         ORDER BY surname, name, patronymic, id
         LIMIT 1'
    );

    if ($res) {
        $currentRow = mysqli_fetch_assoc($res) ?: null;
    }
}


$listRes = mysqli_query(
    $mysqli,
    'SELECT id, surname, name, patronymic
     FROM friends
     ORDER BY surname, name, patronymic, id'
);



echo '<section class="panel">';
echo '<h2>Редактирование записи</h2>';

if ($message !== '') {
    echo '<div class="message ' . e($messageClass) . '">' . e($message) . '</div>';
}

echo '<div class="record-links">';

if ($listRes) {
    while ($row = mysqli_fetch_assoc($listRes)) {
        $label = e($row['surname']) . ' ' . e($row['name']);
        $initials = initials($row['name'], $row['patronymic']);
        $text = trim($label . ' ' . $initials);

        if ($currentRow && (int)$currentRow['id'] === (int)$row['id']) {
            echo '<div class="record-current">' . $text . '</div>';
        } else {
            echo '<a href="?p=edit&id=' . (int)$row['id'] . '">' . $text . '</a>';
        }
    }
}

echo '</div>';

if ($currentRow) {
    $fields = $currentRow;
?>
<form class="contact-form" method="post" action="?p=edit">
    <input type="hidden" name="id" value="<?= e($fields['id']) ?>">

    <label>Фамилия
        <input type="text" name="surname" value="<?= e($fields['surname']) ?>" required>
    </label>

    <label>Имя
        <input type="text" name="name" value="<?= e($fields['name']) ?>" required>
    </label>

    <label>Отчество
        <input type="text" name="patronymic" value="<?= e($fields['patronymic']) ?>">
    </label>

    <label>Пол
        <select name="sex">
            <option value="М" <?= $fields['sex'] === 'М' ? 'selected' : '' ?>>М</option>
            <option value="Ж" <?= $fields['sex'] === 'Ж' ? 'selected' : '' ?>>Ж</option>
        </select>
    </label>

    <label>Дата рождения
        <input type="date" name="birth_date" value="<?= e($fields['birth_date']) ?>">
    </label>

    <label>Телефон
        <input type="text" name="phone" value="<?= e($fields['phone']) ?>">
    </label>

    <label>Адрес
        <input type="text" name="address" value="<?= e($fields['address']) ?>">
    </label>

    <label>E-mail
        <input type="email" name="email" value="<?= e($fields['email']) ?>">
    </label>

    <label>Комментарий
        <textarea name="comment"><?= e($fields['comment']) ?></textarea>
    </label>

    <input type="submit" name="button" value="Изменить запись">
</form>
<?php
} else {
    echo '<div class="message info">В таблице нет данных</div>';
}

echo '</section>';