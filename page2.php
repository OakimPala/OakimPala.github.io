<?php
$page_title = 'AORUS Master 16 - Куманяев Никита 241-351';
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

<?php $link='page2.php'; $name='AORUS Master 16'; $current_page=true; ?>
<a href="<?php echo $link; ?>" <?php echo ($current_page ? 'class="selected_menu">'.$name : '>'.$name); ?></a>

<?php $link='page3.php'; $name='О нас'; $current_page=false; ?>
<a href="<?php echo $link; ?>" <?php echo ($current_page ? 'class="selected_menu">'.$name : '>'.$name); ?></a>

</nav>
</div>
</header>

<main class="container">
<div class="panel">

<h2>AORUS Master 16 BYH</h2>

<p>
AORUS Master 16 BYH — это сочетание мощности и инноваций для геймеров и профессионалов. 
Этот ноутбук оснащён процессором Intel Core Ultra 9 275HX и графикой NVIDIA RTX 50-й серии, 
что обеспечивает высокую производительность в играх и рабочих задачах. 
16-дюймовый дисплей с высокой частотой обновления гарантирует плавность изображения, 
а продвинутая система охлаждения поддерживает стабильность даже при максимальных нагрузках.
</p>

<div class="photo-box-st">
<img src="fotos/photo_static2.png" alt="AORUS logo">
</div>

<div class="photo-box">
<img src="fotos/Photo<?php echo (date('s') % 3 + 1); ?>.png" alt="AORUS Master 16">
</div>

<h2>Основные характеристики</h2>

<table>
<?php echo '<tr><td>Процессор</td><td>ОЗУ</td><td>SSD</td></tr>'; ?>
<tr>
<td><?php echo 'Intel Core Ultra 9 275HX'; ?></td>
<td><?php echo '32 ГБ DDR5'; ?></td>
<td><?php echo '2000 ГБ PCIe'; ?></td>
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
