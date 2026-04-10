<?php
declare(strict_types=1);

if (function_exists('mb_internal_encoding')) {
    mb_internal_encoding('UTF-8');
}
header('Content-Type: text/html; charset=UTF-8');

function get_post_text(): string
{
    if (!isset($_POST['data'])) {
        return '';
    }

    $text = (string)$_POST['data'];
    return trim($text);
}

function split_chars(string $text): array
{
    if ($text === '') {
        return [];
    }

    return preg_split('//u', $text, -1, PREG_SPLIT_NO_EMPTY) ?: [];
}

function to_lower_utf8(string $text): string
{
    if (function_exists('mb_strtolower')) {
        return mb_strtolower($text, 'UTF-8');
    }
    return strtolower($text);
}

function count_letters(array $chars): int
{
    $count = 0;
    foreach ($chars as $ch) {
        if (preg_match('/^\p{L}$/u', $ch)) {
            $count++;
        }
    }
    return $count;
}

function count_upper_lower(array $chars): array
{
    $upper = 0;
    $lower = 0;

    foreach ($chars as $ch) {
        if (preg_match('/^\p{Lu}$/u', $ch)) {
            $upper++;
        } elseif (preg_match('/^\p{Ll}$/u', $ch)) {
            $lower++;
        }
    }

    return [$lower, $upper];
}

function count_punctuation(array $chars): int
{
    $count = 0;
    foreach ($chars as $ch) {
        if (preg_match('/^\p{P}$/u', $ch)) {
            $count++;
        }
    }
    return $count;
}

function count_digits(array $chars): int
{
    $count = 0;
    foreach ($chars as $ch) {
        if (preg_match('/^\p{N}$/u', $ch)) {
            $count++;
        }
    }
    return $count;
}

function get_words(string $text): array
{
    if ($text === '') {
        return [];
    }

    preg_match_all('/[\p{L}]+/u', $text, $matches);
    return $matches[0] ?? [];
}

function count_word_frequencies(array $words): array
{
    $freq = [];
    foreach ($words as $word) {
        $word = to_lower_utf8($word);
        $freq[$word] = ($freq[$word] ?? 0) + 1;
    }

    ksort($freq, SORT_STRING);
    return $freq;
}

function count_symbol_frequencies(array $chars): array
{
    $freq = [];
    foreach ($chars as $ch) {
        $key = to_lower_utf8($ch);
        $freq[$key] = ($freq[$key] ?? 0) + 1;
    }

    ksort($freq, SORT_STRING);
    return $freq;
}

function display_symbol(string $symbol): string
{
    return match ($symbol) {
        ' ' => '[пробел]',
        "\t" => '[табуляция]',
        "\n" => '[перенос строки]',
        "\r" => '[возврат каретки]',
        default => htmlspecialchars($symbol, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'),
    };
}

function render_table(array $rows): void
{
    echo '<div class="table-wrap"><table>';
    foreach ($rows as $row) {
        echo '<tr>';
        foreach ($row as $cellTag => $cellValue) {
            if ($cellTag === 'th') {
                echo '<th>' . $cellValue . '</th>';
            } else {
                echo '<td>' . $cellValue . '</td>';
            }
        }
        echo '</tr>';
    }
    echo '</table></div>';
}

$text = get_post_text();

?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Результат анализа</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <main class="container">
    <h1>Результат анализа текста</h1>

    <div class="result-block">
<?php if ($text !== ''): ?>
      <div class="src_text"><?= htmlspecialchars($text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></div>
<?php else: ?>
      <div class="src_error">Нет текста для анализа</div>
<?php endif; ?>
    </div>

<?php if ($text !== ''): ?>
<?php
    $chars = split_chars($text);
    $all_char_count = count($chars);
    $letters_count = count_letters($chars);
    [$lower_count, $upper_count] = count_upper_lower($chars);
    $punct_count = count_punctuation($chars);
    $digit_count = count_digits($chars);
    $words = get_words($text);
    $word_count = count($words);
    $word_freq = count_word_frequencies($words);
    $symbol_freq = count_symbol_frequencies($chars);

    $summary_rows = [
        ['th' => 'Показатель', 'th' => 'Значение'],
        ['th' => 'Количество символов (включая пробелы)', 'td' => (string)$all_char_count],
        ['th' => 'Количество букв', 'td' => (string)$letters_count],
        ['th' => 'Количество строчных букв', 'td' => (string)$lower_count],
        ['th' => 'Количество заглавных букв', 'td' => (string)$upper_count],
        ['th' => 'Количество знаков препинания', 'td' => (string)$punct_count],
        ['th' => 'Количество цифр', 'td' => (string)$digit_count],
        ['th' => 'Количество слов', 'td' => (string)$word_count],
    ];
?>

    <h2 class="section-title">Информация о тексте</h2>
    <?php render_table($summary_rows); ?>

    <h2 class="section-title">Количество вхождений каждого символа</h2>
    <div class="table-wrap">
      <table>
        <tr>
          <th>Символ</th>
          <th>Количество</th>
        </tr>
<?php foreach ($symbol_freq as $symbol => $count): ?>
        <tr>
          <td><?= display_symbol($symbol) ?></td>
          <td><?= (int)$count ?></td>
        </tr>
<?php endforeach; ?>
      </table>
    </div>

    <h2 class="section-title">Слова и количество их вхождений</h2>
    <div class="table-wrap">
      <table>
        <tr>
          <th>Слово</th>
          <th>Количество</th>
        </tr>
<?php foreach ($word_freq as $word => $count): ?>
        <tr>
          <td><?= htmlspecialchars($word, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></td>
          <td><?= (int)$count ?></td>
        </tr>
<?php endforeach; ?>
      </table>
    </div>
<?php endif; ?>

    <p>
      <a class="btn-link" href="index.html">Другой анализ</a>
    </p>
  </main>
</body>
</html>
