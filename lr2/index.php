<!doctype html>
<html lang="ru">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>ЛР2 — Вариант 3 — Куманяев Никита Романович — 241-351</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>
  <header>
    <img src="https://static.ucheba.ru/pix/logo_cache/5868.upto100x100.png" alt="Логотип университета" class="logo">
    <div class="title">
      <div>ФИО: <strong>Куманяев Никита Романович</strong></div>
      <div>Группа: <strong>241-351</strong></div>
      <div>Лабораторная работа 2 — Вариант 3</div>
    </div>
  </header>

  <main>
<?php

$start_value = -5;     
$encounting   = 40;    
$step         = 1;    
$type         = 'C';   
$min_value    = -1e9;  
$max_value    =  1e9;  

$sum = 0.0;
$count_numeric = 0;
$min_found = null;
$max_found = null;

$x = $start_value;

if ($type === 'B') echo "<ul>\n";
if ($type === 'C') echo "<ol>\n";
if ($type === 'D') {
    echo "<table class=\"table-results\">\n<thead><tr><th>#</th><th>Аргумент x</th><th>f(x)</th></tr></thead>\n<tbody>\n";
}
if ($type === 'E') {
    echo "<div class=\"block-row\">\n";
}

function fmt($v){
    if (!is_numeric($v)) return $v;
    return number_format(round($v,3), 3, '.', '');
}

for ($i = 0; $i < $encounting; $i++, $x += $step) {

    $f = null;
    if ($x <= 10) {
        $f = 3 * pow($x, 3) + 2;
    } elseif ($x < 20) {
        $f = 5 * $x + 7;
    } else { 
        if (abs(22 - $x) < 1e-12) {
            $f = 'error'; 
        } else {
            $f = $x / (22 - $x) - $x;
        }
    }

    if (is_numeric($f)) {
        $sum += $f;
        $count_numeric++;
        if ($min_found === null || $f < $min_found) $min_found = $f;
        if ($max_found === null || $f > $max_found) $max_found = $f;
    }

    $label = "f(" . fmt($x) . ")=" . (is_numeric($f) ? fmt($f) : $f);

    switch ($type) {
        case 'A':
            echo $label;
            if ($i < $encounting - 1) echo "<br>\n";
            break;
        case 'B':
            echo "<li>" . htmlspecialchars($label) . "</li>\n";
            break;
        case 'C':
            echo "<li>" . htmlspecialchars($label) . "</li>\n";
            break;
        case 'D':
            $rownum = $i + 1;
            $xout = fmt($x);
            $fout = is_numeric($f) ? fmt($f) : $f;
            echo "<tr><td>{$rownum}</td><td>{$xout}</td><td>{$fout}</td></tr>\n";
            break;
        case 'E':
            $fout = is_numeric($f) ? fmt($f) : $f;
            echo "<div class=\"block-item\">" . htmlspecialchars("f(" . fmt($x) . ")=" . $fout) . "</div>\n";
            break;
        default:
            echo $label;
            if ($i < $encounting - 1) echo "<br>\n";
    }

    if (is_numeric($f)) {
        if ($f >= $max_value || $f < $min_value) {
            break;
        }
    }
}


if ($type === 'B') echo "</ul>\n";
if ($type === 'C') echo "</ol>\n";
if ($type === 'D') echo "</tbody></table>\n";
if ($type === 'E') echo "</div>\n";

echo "<hr>\n";
echo "<h3>Статистика по вычисленным значениям (исключая 'error'):</h3>\n";
if ($count_numeric > 0) {
    $avg = $sum / $count_numeric;
    echo "<p>Количество числовых значений: <strong>{$count_numeric}</strong></p>\n";
    echo "<p>Сумма: <strong>" . fmt($sum) . "</strong></p>\n";
    echo "<p>Минимум: <strong>" . fmt($min_found) . "</strong></p>\n";
    echo "<p>Максимум: <strong>" . fmt($max_found) . "</strong></p>\n";
    echo "<p>Среднее арифметическое: <strong>" . fmt($avg) . "</strong></p>\n";
} else {
    echo "<p>Числовых значений не получено.</p>\n";
}
?>
  </main>

  <footer>
    <?php
      $type_names = ['A'=>'Простая строковая верстка (A)','B'=>'Маркированный список (B)','C'=>'Нумерованный список (C)','D'=>'Табличная верстка (D)','E'=>'Блочная верстка (E)'];
      $tn = isset($type_names[$type]) ? $type_names[$type] : "Неизвестный тип ({$type})";
      echo "Тип верстки: <strong>{$tn}</strong>";
    ?>
  </footer>
</body>
</html>