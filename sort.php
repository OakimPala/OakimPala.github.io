<?php
header('Content-Type: text/html; charset=utf-8');

function h($v): string {
    return htmlspecialchars((string)$v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function is_not_number_string(string $arg): bool {
    $arg = trim($arg);
    if ($arg === '') {
        return true;
    }
    return !preg_match('/^[+-]?(?:\d+(?:\.\d+)?|\.\d+)$/', $arg);
}

function format_arr(array $arr): string {
    $parts = [];
    foreach ($arr as $i => $v) {
        $parts[] = '<span class="arr-item"><span class="idx">' . h($i) . '</span>: ' . h($v) . '</span>';
    }
    return implode('', $parts);
}

function log_step(array &$log, int &$iter, array $arr, string $note = ''): void {
    $iter++;
    $log[] = '<div class="step"><div><strong>Итерация ' . $iter . '</strong>' . ($note !== '' ? ' — ' . h($note) : '') . '</div><div class="array">' . format_arr($arr) . '</div></div>';
}

function selection_sort(array $arr, array &$log, int &$iter): array {
    $n = count($arr);
    for ($i = 0; $i < $n - 1; $i++) {
        $min = $i;
        for ($j = $i + 1; $j < $n; $j++) {
            if ($arr[$j] < $arr[$min]) {
                $min = $j;
            }
            log_step($log, $iter, $arr, "выбор минимума для позиции $i, сравнение с индексом $j");
        }
        if ($min !== $i) {
            [$arr[$i], $arr[$min]] = [$arr[$min], $arr[$i]];
            log_step($log, $iter, $arr, "обмен элементов $i и $min");
        }
    }
    return $arr;
}

function bubble_sort(array $arr, array &$log, int &$iter): array {
    $n = count($arr);
    for ($j = 0; $j < $n - 1; $j++) {
        for ($i = 0; $i < $n - 1 - $j; $i++) {
            if ($arr[$i] > $arr[$i + 1]) {
                [$arr[$i], $arr[$i + 1]] = [$arr[$i + 1], $arr[$i]];
                log_step($log, $iter, $arr, "обмен соседних элементов $i и " . ($i + 1));
            } else {
                log_step($log, $iter, $arr, "проверка пары $i и " . ($i + 1));
            }
        }
    }
    return $arr;
}

function shell_sort(array $arr, array &$log, int &$iter): array {
    $n = count($arr);
    for ($gap = (int)ceil($n / 2); $gap >= 1; $gap = (int)ceil($gap / 2)) {
        for ($i = $gap; $i < $n; $i++) {
            $val = $arr[$i];
            $j = $i - $gap;
            while ($j >= 0 && $arr[$j] > $val) {
                $arr[$j + $gap] = $arr[$j];
                $j -= $gap;
                log_step($log, $iter, $arr, "шаг Шелла с шагом $gap");
            }
            $arr[$j + $gap] = $val;
            log_step($log, $iter, $arr, "вставка элемента на шаге $gap");
        }
        if ($gap === 1) {
            break;
        }
    }
    return $arr;
}

function gnome_sort(array $arr, array &$log, int &$iter): array {
    $i = 1;
    $n = count($arr);
    while ($i < $n) {
        if ($i === 0 || $arr[$i - 1] <= $arr[$i]) {
            log_step($log, $iter, $arr, "шаг вперед к индексу $i");
            $i++;
        } else {
            [$arr[$i], $arr[$i - 1]] = [$arr[$i - 1], $arr[$i]];
            log_step($log, $iter, $arr, "обмен и шаг назад с индекса $i");
            $i--;
        }
    }
    return $arr;
}

function quick_sort(array &$arr, int $left, int $right, array &$log, int &$iter): void {
    $l = $left;
    $r = $right;
    $pivot = $arr[(int)floor(($left + $right) / 2)];

    do {
        while ($arr[$l] < $pivot) {
            $l++;
            log_step($log, $iter, $arr, "сдвиг левой границы к опорному значению $pivot");
        }
        while ($arr[$r] > $pivot) {
            $r--;
            log_step($log, $iter, $arr, "сдвиг правой границы к опорному значению $pivot");
        }
        if ($l <= $r) {
            [$arr[$l], $arr[$r]] = [$arr[$r], $arr[$l]];
            log_step($log, $iter, $arr, "обмен по опорному значению $pivot");
            $l++;
            $r--;
        }
    } while ($l <= $r);

    if ($left < $r) {
        quick_sort($arr, $left, $r, $log, $iter);
    }
    if ($l < $right) {
        quick_sort($arr, $l, $right, $log, $iter);
    }
}

function quick_sort_wrapper(array $arr, array &$log, int &$iter): array {
    if (count($arr) > 1) {
        quick_sort($arr, 0, count($arr) - 1, $log, $iter);
    }
    return $arr;
}

function builtin_sort(array $arr, array &$log, int &$iter): array {
    sort($arr);
    $iter = 0;
    return $arr;
}

$algorithmNames = [
    'selection' => 'Сортировка выбором',
    'bubble' => 'Пузырьковый алгоритм',
    'shell' => 'Алгоритм Шелла',
    'gnome' => 'Алгоритм садового гнома',
    'quick' => 'Быстрая сортировка',
    'builtin' => 'Встроенная функция PHP sort()'
];

$algorithm = $_POST['algorithm'] ?? '';
$arrLength = isset($_POST['arrLength']) ? (int)$_POST['arrLength'] : 0;

if ($arrLength <= 0 || !isset($_POST['element0'])) {
    echo '<!doctype html><html lang="ru"><head><meta charset="utf-8"><title>Результат сортировки</title></head><body>';
    echo '<p>Массив не задан, сортировка невозможна.</p>';
    echo '</body></html>';
    exit;
}

$input = [];
$invalid = null;
for ($i = 0; $i < $arrLength; $i++) {
    $key = 'element' . $i;
    $value = isset($_POST[$key]) ? trim((string)$_POST[$key]) : '';
    if (is_not_number_string($value)) {
        $invalid = $value;
        break;
    }
    $input[] = $value + 0;
}

$algorithmName = $algorithmNames[$algorithm] ?? 'Неизвестный алгоритм';

?><!doctype html>
<html lang="ru">
<head>
  <meta charset="utf-8">
  <title>Результат сортировки</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<?php if ($invalid !== null): ?>
  <h1><?= h($algorithmName) ?></h1>
  <div class="box warn">Элемент массива <code><?= h($invalid) ?></code> — не число. Сортировка не выполняется.</div>
</body>
</html>
<?php exit; endif; ?>

  <h1><?= h($algorithmName) ?></h1>
  <div class="box">
    <div class="meta"><strong>Входные данные:</strong></div>
    <div class="array">
      <?php foreach ($input as $i => $value): ?>
        <span class="arr-item"><span class="idx"><?= h($i) ?></span>: <?= h($value) ?></span>
      <?php endforeach; ?>
    </div>
  </div>

  <div class="box ok">Массив проверен, сортировка возможна.</div>

<?php
$log = [];
$iterations = 0;
$start = microtime(true);

switch ($algorithm) {
    case 'selection':
        $result = selection_sort($input, $log, $iterations);
        break;
    case 'bubble':
        $result = bubble_sort($input, $log, $iterations);
        break;
    case 'shell':
        $result = shell_sort($input, $log, $iterations);
        break;
    case 'gnome':
        $result = gnome_sort($input, $log, $iterations);
        break;
    case 'quick':
        $result = quick_sort_wrapper($input, $log, $iterations);
        break;
    case 'builtin':
        $result = builtin_sort($input, $log, $iterations);
        $log[] = '<div class="step"><div><strong>Встроенная функция PHP sort()</strong></div><div class="array">Результат получен без пошагового вывода алгоритма.</div></div>';
        break;
    default:
        echo '<div class="box warn">Не выбран или неизвестен алгоритм сортировки.</div>';
        echo '</body></html>';
        exit;
}

$elapsed = microtime(true) - $start;
?>

  <div class="box">
    <div class="meta"><strong>Ход сортировки:</strong></div>
    <?php foreach ($log as $line) echo $line; ?>
  </div>

  <div class="box ok">
    Сортировка завершена, проведено <?= h($iterations) ?> итераций. Сортировка заняла <?= h(number_format($elapsed, 6, '.', '')) ?> секунд.
  </div>

  <div class="box">
    <div class="meta"><strong>Результат:</strong></div>
    <div class="array">
      <?php foreach ($result as $i => $value): ?>
        <span class="arr-item"><span class="idx"><?= h($i) ?></span>: <?= h($value) ?></span>
      <?php endforeach; ?>
    </div>
  </div>
</body>
</html>
