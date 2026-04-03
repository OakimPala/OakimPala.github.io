<!doctype html>
<html lang="ru">
<head>
  <meta charset="utf-8">
  <title>Лабораторная работа № А-7 — ввод массива</title>
  <link rel="stylesheet" href="style.css">
  <script>
    function setHTML(element, txt) {
      if (typeof element.innerHTML !== 'undefined') {
        element.innerHTML = txt;
      } else {
        var range = document.createRange();
        range.selectNodeContents(element);
        range.deleteContents();
        var fragment = range.createContextualFragment(txt);
        element.appendChild(fragment);
      }
    }

    function addElement(tableName, amount) {
      var table = document.getElementById(tableName);

      for (var i = 0; i < amount; i++) {
        var index = table.rows.length;
        var row = table.insertRow(index);

        var numCell = row.insertCell(0);
        numCell.className = 'num';
        setHTML(numCell, String(index));

        var inputCell = row.insertCell(1);
        inputCell.className = 'inputcell';
        setHTML(inputCell, '<input type="text" name="element' + index + '" placeholder="Введите число">');
      }

      document.getElementById('arrLength').value = table.rows.length;
    }

    function syncLength() {
      document.getElementById('arrLength').value = document.getElementById('elements').rows.length;
    }

    window.addEventListener('load', syncLength);
  </script>
</head>
<body>
  <h1>Лабораторная работа № А-7</h1>
  <div class="hint">Введите элементы массива, добавляйте строки кнопкой ниже и выберите алгоритм сортировки.</div>

  <form action="sort.php" method="post" target="_blank" autocomplete="off">
    <table id="elements">
      <tr>
        <td class="num">0</td>
        <td class="inputcell"><input type="text" name="element0" placeholder="Введите число"></td>
      </tr>
    </table>

    <input type="hidden" id="arrLength" name="arrLength" value="1">

    <div class="controls">
      <label for="algorithm">Алгоритм сортировки:</label>
      <select id="algorithm" name="algorithm">
        <option value="selection">Сортировка выбором</option>
        <option value="bubble">Пузырьковый алгоритм</option>
        <option value="shell">Алгоритм Шелла</option>
        <option value="gnome">Алгоритм садового гнома</option>
        <option value="quick">Быстрая сортировка</option>
        <option value="builtin">Встроенная функция PHP sort()</option>
      </select>

      <button type="button" onclick="addElement('elements', 1)">Добавить еще один элемент</button>
      <button type="submit">Сортировать массив</button>
    </div>
  </form>
</body>
</html>
