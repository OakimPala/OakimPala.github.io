<?php
$page_title = 'О нас - Куманяев Никита 241-351';
?>
<!doctype html>
<html lang="ru">
<head>
  <meta charset="utf-8">
  <title><?php echo $page_title; ?></title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<header>
  <div class="container">
    <h1>TechStore — Магазин ноутбуков</h1>
    <nav class="menu">
      <?php $link='index.php'; $name='Каталог'; $current_page=false; ?>
      <a href="<?php echo $link; ?>" <?php echo ($current_page ? 'class="selected_menu">'.$name : '>'.$name); ?></a>

      <?php $link='page2.php'; $name='AORUS Master 16'; $current_page=false; ?>
      <a href="<?php echo $link; ?>" <?php echo ($current_page ? 'class="selected_menu">'.$name : '>'.$name); ?></a>

      <?php $link='page3.php'; $name='О нас'; $current_page=true; ?>
      <a href="<?php echo $link; ?>" <?php echo ($current_page ? 'class="selected_menu">'.$name : '>'.$name); ?></a>
    </nav>
  </div>
</header>

<main class="container">
  <h2>О магазине TechStore</h2>
  <p>
    "TechStore — это современный интернет-магазин ноутбуков, ориентированный на геймеров, студентов и профессионалов. Мы предлагаем только оригинальную технику от официальных поставщиков, предоставляем гарантию 24 месяца и обеспечиваем техническую поддержку на протяжении всего срока эксплуатации. Наши специалисты помогают подобрать устройство под конкретные задачи: игры, программирование, графика, монтаж видео или офисная работа. "
  </p>

  <h2>Почему выбирают нас</h2>
  <table>
    <?php echo '<tr><td>Опыт работы</td><td>Ассортимент</td><td>Гарантия</td></tr>'; ?>
    <tr>
      <td><?php echo 'Более 5 лет на рынке'; ?></td>
      <td><?php echo 'Игровые и профессиональные модели'; ?></td>
      <td><?php echo 'Официальная 24 месяца'; ?></td>
    </tr>
  </table>

  <p>
    Мы постоянно обновляем каталог, следим за выходом новых моделей 2025 года и предлагаем конкурентные цены. Наша цель — предоставить клиентам мощные и надежные устройства для работы и развлечений.
  </p>
</main>

<footer>
  <div class="container">
    <?php date_default_timezone_set('Europe/Moscow'); echo 'Сформировано '.date('d.m.Y').' в '.date('H:i:s'); ?>
  </div>
</footer>
</body>
</html>
