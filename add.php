<?php
if (!defined('APP_BOOTSTRAPPED')) {
    http_response_code(403);
    exit('Forbidden');
}

$fields = [
    'surname' => '',
    'name' => '',
    'patronymic' => '',
    'sex' => 'М',
    'birth_date' => '',
    'phone' => '',
    'address' => '',
    'email' => '',
    'comment' => '',
];

$message = '';
$messageClass = '';

if (isset($_GET['success'])) {
    $message = 'Запись добавлена';
    $messageClass = 'ok';
}

if (isset($_GET['error'])) {
    $message = 'Ошибка: запись не добавлена';
    $messageClass = 'error';
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['button'] ?? '') === 'Добавить запись') {

    foreach ($fields as $key => $_) {
        $fields[$key] = trim((string)($_POST[$key] ?? ''));
    }

    if (!in_array($fields['sex'], ['М', 'Ж'], true)) {
        $fields['sex'] = 'М';
    }

    if ($fields['birth_date'] === '') {
        $fields['birth_date'] = date('Y-m-d');
    }

    try {
        $mysqli = db_connect();

        $stmt = mysqli_prepare(
            $mysqli,
            'INSERT INTO friends (surname, name, patronymic, sex, birth_date, phone, address, email, comment)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );

        if ($stmt) {
            mysqli_stmt_bind_param(
                $stmt,
                'sssssssss',
                $fields['surname'],
                $fields['name'],
                $fields['patronymic'],
                $fields['sex'],
                $fields['birth_date'],
                $fields['phone'],
                $fields['address'],
                $fields['email'],
                $fields['comment']
            );

            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_close($stmt);


                header('Location: ?p=add&success=1');
                exit;
            }

            mysqli_stmt_close($stmt);
        }

    } catch (Throwable $e) {

    }


    header('Location: ?p=add&error=1');
    exit;
}
?>

<section class="panel">
    <h2>Добавление записи</h2>

    <?php if ($message !== ''): ?>
        <div class="message <?= e($messageClass) ?>">
            <?= e($message) ?>
        </div>
    <?php endif; ?>

    <form class="contact-form" method="post" action="?p=add">

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

        <input type="submit" name="button" value="Добавить запись">
    </form>
</section>