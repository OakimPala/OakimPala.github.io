<?php

function randVal() {
    return mt_rand(1, 10000) / 100;
}

$isPost = isset($_POST['A']);
$view = $_POST['VIEW'] ?? ($_GET['VIEW'] ?? 'browser');

if ($isPost) {

    $fio = htmlspecialchars($_POST['FIO']);
    $group = htmlspecialchars($_POST['GROUP']);
    $about = htmlspecialchars($_POST['ABOUT']);
    $task = $_POST['TASK'];

    $A = floatval(str_replace(',', '.', $_POST['A']));
    $B = floatval(str_replace(',', '.', $_POST['B']));
    $C = floatval(str_replace(',', '.', $_POST['C']));

    $user_result = $_POST['RESULT'];

    switch ($task) {

        case 'mean':
            $result = round(($A + $B + $C) / 3, 2);
            $task_name = "Среднее арифметическое";
            break;

        case 'perimeter':
            $result = $A + $B + $C;
            $task_name = "Периметр треугольника";
            break;

        case 'area':
            if ($A + $B > $C && $A + $C > $B && $B + $C > $A) {
                $p = ($A + $B + $C) / 2;
                $result = round(sqrt($p * ($p - $A) * ($p - $B) * ($p - $C)), 2);//формула Герона
            } else {
                $result = "Ошибка: треугольник не существует";
            }
            $task_name = "Площадь треугольника";
            break;

        case 'volume':
            $result = round($A * $B * $C, 2);
            $task_name = "Объем параллелепипеда";
            break;

        case 'max':
            $result = max($A, $B, $C);
            $task_name = "Максимум";
            break;

        case 'min':
            $result = min($A, $B, $C);
            $task_name = "Минимум";
            break;

        default:
            $result = "Ошибка";
            $task_name = "Неизвестная задача";
    }

    // проверка результата
    if (!is_numeric($result)) {
        $check = $result;
        $class = "error";
    } elseif ($user_result === "") {
        $check = "Задача самостоятельно решена не была";
        $class = "error";
    } else {
        $user_val = floatval(str_replace(',', '.', $user_result));
        $epsilon = 0.01;

        if (abs($user_val - $result) <= $epsilon) {
            $check = "Тест пройден";
            $class = "success";
        } else {
            $check = "Ошибка: тест не пройден";
            $class = "error";
        }
    }

    // отчет
    $out = "";
    $out .= "ФИО: $fio<br>";
    $out .= "Группа: $group<br><br>";

    if ($about) {
        $out .= "Сведения о студенте:<br>$about<br><br>";
    }

    $out .= "Тип задачи: $task_name<br>";
    $out .= "Входные данные: A=$A, B=$B, C=$C<br>";

    if ($user_result === "") {
        $out .= "Задача самостоятельно решена не была<br>";
    } else {
        $out .= "Предполагаемый результат: $user_result<br>";
    }

    $out .= "Результат программы: $result<br><br>";
    $out .= "<b class='$class'>$check</b><br>";

    if (isset($_POST['send_mail']) && !empty($_POST['MAIL'])) {

        $mail = $_POST['MAIL'];

        mail(
            $mail,
            "Результат теста",
            str_replace("<br>", "\n", $out),
            "From: test@localhost\r\nContent-type: text/plain; charset=utf-8"
        );

        $out .= "<br>Результаты теста были автоматически отправлены на e-mail $mail";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>ЛР6</title>
<link rel="stylesheet" href="style.css">
</head>

<body class="<?= $view == 'print' ? 'print' : '' ?>">

<h1>Тест математических знаний</h1>

<?php if ($isPost): ?>

<div class="result">
    <?= $out ?>

    <?php if ($view == 'browser'): ?>
        <a class="btn" href="?FIO=<?= $fio ?>&GROUP=<?= $group ?>&VIEW=browser">
            Повторить тест
        </a>
    <?php endif; ?>
</div>

<?php else: ?>

<form method="post">

<div class="row">
<label>ФИО:</label>
<input type="text" name="FIO" value="<?= $_GET['FIO'] ?? '' ?>">
</div>

<div class="row">
<label>Группа:</label>
<input type="text" name="GROUP" value="<?= $_GET['GROUP'] ?? '' ?>">
</div>

<div class="row">
<label>A:</label>
<input type="text" name="A" value="<?= randVal() ?>">
</div>

<div class="row">
<label>B:</label>
<input type="text" name="B" value="<?= randVal() ?>">
</div>

<div class="row">
<label>C:</label>
<input type="text" name="C" value="<?= randVal() ?>">
</div>

<div class="row">
<label>Ваш ответ:</label>
<input type="text" name="RESULT">
</div>

<div class="row">
<label>О себе:</label>
<textarea name="ABOUT"></textarea>
</div>

<div class="row">
<label>Тип задачи:</label>
<select name="TASK">
<option value="mean">Среднее арифметическое</option>
<option value="perimeter">Периметр треугольника</option>
<option value="area">Площадь треугольника</option>
<option value="volume">Объем параллелепипеда</option>
<option value="max">Максимум</option>
<option value="min">Минимум</option>
</select>
</div>

<div class="row">
<label>Версия:</label>
<select name="VIEW">
<option value="browser">Для просмотра в браузере</option>
<option value="print">Для печати</option>
</select>
</div>

<div class="row">
<label>
<input type="checkbox" id="send_mail" name="send_mail"
onclick="document.getElementById('mailBlock').style.display = this.checked ? 'block':'none'">
Отправить результат на e-mail
</label>
</div>

<div id="mailBlock" style="display:none;">
<div class="row">
<label>Email:</label>
<input type="email" name="MAIL">
</div>
</div>

<button type="submit">Проверить</button>

</form>

<?php endif; ?>

</body>
</html>