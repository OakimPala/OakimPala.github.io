<?php
session_start();
header('Content-Type: text/html; charset=UTF-8');

if (!isset($_SESSION['last'])) {
    $_SESSION['last'] = null;
}

if (!isset($_SESSION['history'])) {
    $_SESSION['history'] = array();
}
if (!isset($_SESSION['iteration'])) {
    $_SESSION['iteration'] = 0;
}
$_SESSION['iteration']++;

function is_whitespace_char($ch) {
    return $ch === " " || $ch === "\t" || $ch === "\n" || $ch === "\r";
}

function normalize_expression($s) {
    $out = '';
    $len = strlen($s);
    for ($i = 0; $i < $len; $i++) {
        $ch = $s[$i];
        if (!is_whitespace_char($ch)) {
            $out .= $ch;
        }
    }
    return $out;
}

function has_only_allowed_chars($s) {
    $len = strlen($s);
    for ($i = 0; $i < $len; $i++) {
        $ch = $s[$i];
        if (
            ($ch >= '0' && $ch <= '9') ||
            $ch === '+' || $ch === '-' || $ch === '*' ||
            $ch === '/' || $ch === ':' || $ch === '(' ||
            $ch === ')' || $ch === '.'
        ) {
            continue;
        }
        return false;
    }
    return true;
}

function is_digit_char($ch) {
    return $ch >= '0' && $ch <= '9';
}

function digit_value($ch) {
    if ($ch === '0') return 0;
    if ($ch === '1') return 1;
    if ($ch === '2') return 2;
    if ($ch === '3') return 3;
    if ($ch === '4') return 4;
    if ($ch === '5') return 5;
    if ($ch === '6') return 6;
    if ($ch === '7') return 7;
    if ($ch === '8') return 8;
    if ($ch === '9') return 9;
    return -1;
}

function is_number_value($v) {
    return is_int($v) || is_float($v);
}

function skip_spaces($s, &$i) {
    $len = strlen($s);
    while ($i < $len && is_whitespace_char($s[$i])) {
        $i++;
    }
}

function parse_number($s, &$i, &$error) {
    $len = strlen($s);
    skip_spaces($s, $i);

    if ($i >= $len) {
        $error = 'Ожидалось число';
        return 0;
    }

    if ($s[$i] === '.') {
        $error = 'Неправильная форма числа';
        return 0;
    }

    if (!is_digit_char($s[$i])) {
        $error = 'Ожидалось число';
        return 0;
    }

    $int_part = 0;
    $frac_part = 0;
    $scale = 1;
    $has_fraction = false;

    if ($s[$i] === '0') {
        $i++;
        if ($i < $len && is_digit_char($s[$i])) {
            $error = 'Неправильная форма числа';
            return 0;
        }
    } else {
        while ($i < $len && is_digit_char($s[$i])) {
            $int_part = $int_part * 10 + digit_value($s[$i]);
            $i++;
        }
    }

    if ($i < $len && $s[$i] === '.') {
        $has_fraction = true;
        $i++;
        if ($i >= $len || !is_digit_char($s[$i])) {
            $error = 'Неправильная форма числа';
            return 0;
        }
        while ($i < $len && is_digit_char($s[$i])) {
            $frac_part = $frac_part * 10 + digit_value($s[$i]);
            $scale *= 10;
            $i++;
        }
    }

    if ($has_fraction) {
        return $int_part + ($frac_part / $scale);
    }

    return $int_part;
}

function parse_primary($s, &$i, &$error) {
    skip_spaces($s, $i);
    $len = strlen($s);

    if ($i >= $len) {
        $error = 'Ожидалось число';
        return 0;
    }

    if ($s[$i] === '(') {
        $i++;
        $value = parse_expression($s, $i, $error);
        if ($error !== '') {
            return 0;
        }
        skip_spaces($s, $i);
        if ($i >= $len || $s[$i] !== ')') {
            $error = 'Неправильная расстановка скобок';
            return 0;
        }
        $i++;
        return $value;
    }

    return parse_number($s, $i, $error);
}

function parse_factor($s, &$i, &$error) {
    skip_spaces($s, $i);
    $sign = 1;
    $len = strlen($s);

    while ($i < $len && ($s[$i] === '+' || $s[$i] === '-')) {
        if ($s[$i] === '-') {
            $sign *= -1;
        }
        $i++;
        skip_spaces($s, $i);
    }

    $value = parse_primary($s, $i, $error);
    if ($error !== '') {
        return 0;
    }

    return $sign * $value;
}

function parse_term($s, &$i, &$error) {
    $value = parse_factor($s, $i, $error);
    if ($error !== '') {
        return 0;
    }

    $len = strlen($s);
    while (true) {
        skip_spaces($s, $i);
        if ($i >= $len) {
            break;
        }

        $op = $s[$i];
        if ($op !== '*' && $op !== '/' && $op !== ':') {
            break;
        }

        $i++;
        $rhs = parse_factor($s, $i, $error);
        if ($error !== '') {
            return 0;
        }

        if ($op === '*') {
            $value *= $rhs;
        } else {
            if ($rhs == 0) {
                $error = 'Деление на ноль';
                return 0;
            }
            $value /= $rhs;
        }
    }

    return $value;
}

function parse_expression($s, &$i, &$error) {
    $value = parse_term($s, $i, $error);
    if ($error !== '') {
        return 0;
    }

    $len = strlen($s);
    while (true) {
        skip_spaces($s, $i);
        if ($i >= $len) {
            break;
        }

        $op = $s[$i];
        if ($op !== '+' && $op !== '-') {
            break;
        }

        $i++;
        $rhs = parse_term($s, $i, $error);
        if ($error !== '') {
            return 0;
        }

        if ($op === '+') {
            $value += $rhs;
        } else {
            $value -= $rhs;
        }
    }

    return $value;
}

function calculate($val) {
    $val = normalize_expression($val);

    if ($val === '') {
        return 'Выражение не задано!';
    }

    if (!has_only_allowed_chars($val)) {
        return 'Недопустимые символы в выражении';
    }

    if (strpos($val, '(') !== false || strpos($val, ')') !== false) {
        return 'Недопустимые скобки в выражении';
    }

    $i = 0;
    $error = '';
    $result = parse_expression($val, $i, $error);
    if ($error !== '') {
        return $error;
    }

    skip_spaces($val, $i);
    if ($i !== strlen($val)) {
        return 'Ошибка синтаксиса выражения';
    }

    return $result;
}

function SqValidator($val) {
    $open = 0;
    $len = strlen($val);
    for ($i = 0; $i < $len; $i++) {
        if ($val[$i] === '(') {
            $open++;
        } elseif ($val[$i] === ')') {
            $open--;
            if ($open < 0) {
                return false;
            }
        }
    }
    return $open === 0;
}

function calculateSq($val) {
    $val = normalize_expression($val);

    if ($val === '') {
        return 'Выражение не задано!';
    }

    if (!has_only_allowed_chars($val)) {
        return 'Недопустимые символы в выражении';
    }

    if (!SqValidator($val)) {
        return 'Неправильная расстановка скобок';
    }

    $start = strpos($val, '(');
    if ($start === false) {
        return calculate($val);
    }

    $end = $start + 1;
    $open = 1;
    $len = strlen($val);

    while ($open && $end < $len) {
        if ($val[$end] === '(') {
            $open++;
        } elseif ($val[$end] === ')') {
            $open--;
        }
        $end++;
    }

    if ($open !== 0) {
        return 'Неправильная расстановка скобок';
    }

    $inner = substr($val, $start + 1, $end - $start - 2);
    $inner_result = calculateSq($inner);
    if (!is_number_value($inner_result)) {
        return $inner_result;
    }

    $new_val = substr($val, 0, $start) . $inner_result . substr($val, $end);
    return calculateSq($new_val);
}

$result = null;
$show_result = false;
$submitted_expr = '';

if (isset($_POST['val'])) {
    $submitted_expr = $_POST['val'];
    $result = calculateSq($submitted_expr);
    $show_result = true;

    if ($_SESSION['last'] !== null) {
        $_SESSION['history'][] = $_SESSION['last'];
    }

    $expr_safe = htmlspecialchars($submitted_expr, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    $history_res = is_numeric($result)
        ? $result
        : htmlspecialchars($result, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

    $_SESSION['last'] = $expr_safe . ' = ' . $history_res;
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Калькулятор — ЛР В-2</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="wrap">
    <div class="card">
        <h1>Арифметический калькулятор</h1>
        <p class="desc">Поддерживаются целые числа, десятичные дроби, скобки, а также операции <b>+</b>, <b>-</b>, <b>*</b>, <b>/</b> и <b>:</b>.</p>

        <?php if ($show_result): ?>
            <div class="result">
                <?php
                $expr_safe = htmlspecialchars($submitted_expr, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
                if (is_number_value($result)) {
                    echo 'Значение выражения: ' . $expr_safe . ' = ' . $result;
                } else {
                    echo 'Ошибка вычисления выражения: ' . htmlspecialchars($result, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
                }
                ?>
            </div>
        <?php endif; ?>

        <form method="post">
            <label for="val">Введите выражение</label>
            <input
                type="text"
                id="val"
                name="val"
                value="<?php echo htmlspecialchars($submitted_expr, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?>"
                autocomplete="off"
            >
            <input type="hidden" name="iteration" value="<?php echo $_SESSION['iteration']; ?>">
            <button type="submit">Вычислить</button>
        </form>

        <div class="hint">Примеры: <code>2+3*4</code>, <code>(2+3)*4</code>, <code>7:2</code>, <code>-5+(3*2)</code>.</div>

<footer>
    <div class="history-title">История вычислений</div>

    <?php
    for ($i = 0; $i < count($_SESSION['history']); $i++) {
        echo '<div class="history-item">' . $_SESSION['history'][$i] . '</div>';
    }
    ?>
</footer>
    </div>
</div>
</body>
</html>
