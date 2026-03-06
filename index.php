<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Виртуальная клавиатура</title>
<link rel="stylesheet" href="style.css">
</head>

<body>

<?php
if(!isset($_GET['store']))
    $_GET['store']='';

if(!isset($_GET['count']))
    $_GET['count']=0;

if(isset($_GET['key']))
{
    $_GET['store'] .= $_GET['key'];
    $_GET['count']++;
}
?>

<h2>Виртуальная клавиатура</h2>

<div class="result">
<?php echo $_GET['store']; ?>
</div>

<div class="keyboard">
    <div class="keyboard-row">
        <a class="key" href="?key=1&store=<?=$_GET['store']?>&count=<?=$_GET['count']?>">1</a>
        <a class="key" href="?key=2&store=<?=$_GET['store']?>&count=<?=$_GET['count']?>">2</a>
        <a class="key" href="?key=3&store=<?=$_GET['store']?>&count=<?=$_GET['count']?>">3</a>
        <a class="key" href="?key=4&store=<?=$_GET['store']?>&count=<?=$_GET['count']?>">4</a>
        <a class="key" href="?key=5&store=<?=$_GET['store']?>&count=<?=$_GET['count']?>">5</a>
    </div>
    <div class="keyboard-row">
        <a class="key" href="?key=6&store=<?=$_GET['store']?>&count=<?=$_GET['count']?>">6</a>
        <a class="key" href="?key=7&store=<?=$_GET['store']?>&count=<?=$_GET['count']?>">7</a>
        <a class="key" href="?key=8&store=<?=$_GET['store']?>&count=<?=$_GET['count']?>">8</a>
        <a class="key" href="?key=9&store=<?=$_GET['store']?>&count=<?=$_GET['count']?>">9</a>
        <a class="key" href="?key=0&store=<?=$_GET['store']?>&count=<?=$_GET['count']?>">0</a>
    </div>
</div>

<a class="reset" href="index.php">СБРОС</a>

<footer>
Нажатий кнопок: <?php echo $_GET['count']; ?>
</footer>

</body>
</html>