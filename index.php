<?php
$page_title = 'Каталог - Куманяев Никита 241-351';

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

<?php $link='index.php'; $name='Каталог'; $current_page=true; ?>
<a href="<?php echo $link; ?>" <?php echo ($current_page ? 'class="selected_menu">'.$name : '>'.$name); ?></a>

<?php $link='page2.php'; $name='AORUS Master 16'; $current_page=false; ?>
<a href="<?php echo $link; ?>" <?php echo ($current_page ? 'class="selected_menu">'.$name : '>'.$name); ?></a>

<?php $link='page3.php'; $name='О нас'; $current_page=false; ?>
<a href="<?php echo $link; ?>" <?php echo ($current_page ? 'class="selected_menu">'.$name : '>'.$name); ?></a>

</nav>
</div>
</header>

<main class="container">
<div class="panel">

<h2>Игровые и профессиональные ноутбуки 2025</h2>

<p>Наш магазин предлагает современные игровые и профессиональные ноутбуки с официальной гарантией и поддержкой. Мы сотрудничаем с ведущими мировыми производителями и предоставляем только оригинальную продукцию. В каталоге представлены модели с новейшими процессорами, мощной графикой и OLED-экранами высокого разрешения.</p>

<h2>Популярная модель</h2>

<div class="photo-box-st">
<img src="fotos/photo_static2.png" alt="AORUS logo">
</div>

<div class="photo-box">
<img src="fotos/Photo<?php echo (date('s') % 3 + 1); ?>.png" alt="AORUS Master 16">
</div>

<h2>Преимущества магазина</h2>

<table>
<?php echo '<tr><td>Гарантия</td><td>Доставка</td><td>Поддержка</td></tr>'; ?>
<tr>
<td><?php echo '24 месяца'; ?></td>
<td><?php echo 'По всей России'; ?></td>
<td><?php echo 'Онлайн консультация'; ?></td>
</tr>
</table>

</div>
</main>

<footer>
<div class="container">
<?php date_default_timezone_set('Europe/Moscow'); echo 'Сформировано '.date('d.m.Y').' в '.date('H:i:s'); ?>
</div>
</footer>

</body>
</html>
