<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8">
<title>Таблица умножения</title>
<link rel="stylesheet" href="style.css">
</head>
<body>

<header id="main_menu">
<?php
echo '<a href="?html_type=TABLE';

if(isset($_GET['content']))
echo '&content='.$_GET['content'];

echo '"';

if(!isset($_GET['html_type']) || (isset($_GET['html_type']) && $_GET['html_type']=="TABLE"))
    echo ' class="selected"';

echo '>Табличная верстка</a>';

echo '<a href="?html_type=DIV';

if(isset($_GET['content']))
echo '&content='.$_GET['content'];

echo '"';

if(isset($_GET['html_type']) && $_GET['html_type']=="DIV")
echo ' class="selected"';

echo '>Блочная верстка</a>';
?>
</header>


<div id="container">


<aside id="side_menu">
<?php
// Формируем базовую ссылку с сохранением типа верстки
$base_link = '';
if(isset($_GET['html_type'])) {
    $base_link = '?html_type=' . $_GET['html_type'];
}

// Ссылка "Всё" — без параметра content
echo '<a href="' . $base_link . '"';

// Проверяем, что параметр content НЕ передан (всё выделено)
if(!isset($_GET['content']))
    echo ' class="selected"';

echo '>Всё</a>';

// Цикл для цифр 2-9
for($i=2; $i<=9; $i++)
{
    // Формируем ссылку с сохранением типа верстки И добавлением content
    $link = $base_link;
    if($link == '') {
        $link = '?content=' . $i;
    } else {
        $link .= '&content=' . $i;
    }
    
    echo '<a href="' . $link . '"';
    
    // Проверяем, активен ли текущий пункт
    if(isset($_GET['content']) && $_GET['content'] == $i)
        echo ' class="selected"';
    
    echo '>' . $i . '</a>';
}
?>
</aside>


<main id="table_area">
<?php

function outNumAsLink($x)
{
    if($x<=9) {
        // Формируем ссылку с сохранением типа верстки
        $link = '?content=' . $x;
        
        // Если выбран тип верстки, сохраняем его
        if(isset($_GET['html_type'])) {
            $link = '?html_type=' . $_GET['html_type'] . '&content=' . $x;
        }
        
        return '<a href="' . $link . '">' . $x . '</a>';
    } else {
        return $x;
    }
}

function outRow($n)
{
    for($i=2;$i<=9;$i++)
    {
        echo '<div class="mult-row">';
        echo outNumAsLink($n).' x '.outNumAsLink($i).' = '.outNumAsLink($n*$i);
        echo '</div>';
    }
}

function outDivForm()
{

if(!isset($_GET['content']))
{
for($i=2;$i<=9;$i++)
{
echo '<div class="ttRow">';
outRow($i);
echo '</div>';
}
}
else
{
echo '<div class="ttSingleRow">';
outRow($_GET['content']);
echo '</div>';
}

}

function outTableForm()
{

if(!isset($_GET['content']))
{

echo '<table class="multTable"><tr>';

for($i=2;$i<=9;$i++)
{
echo '<td>';
outRow($i);
echo '</td>';
}

echo '</tr></table>';

}
else
{

echo '<table class="multTable">';
echo '<tr><td>';

outRow($_GET['content']);

echo '</td></tr>';
echo '</table>';

}

}



if(!isset($_GET['html_type']) || $_GET['html_type']=="TABLE")
outTableForm();
else
outDivForm();

?>
</main>

</div>


<footer id="footer">
<?php
date_default_timezone_set('Europe/Moscow');

if(!isset($_GET['html_type']) || $_GET['html_type']=="TABLE")
$s="Табличная верстка. ";
else
$s="Блочная верстка. ";

if(!isset($_GET['content']))
$s.="Полная таблица умножения. ";
else
$s.="Таблица умножения на ".$_GET['content'].". ";

echo $s."Дата и время: ".date("d.m.Y H:i:s");

?>
</footer>


</body>
</html>