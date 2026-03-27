<?php
$cols = 6; 

$structures = array(
"A1*A2*A3#A4*A5*A6",
"B1*B2*B3#B4*B5*B6",
"C1*C2*C3#C4*C5*C6",
"D1*D2*D3#D4*D5*D6",
"E1*E2*E3#E4*E5*E6",
"F1*F2*F3#F4*F5*F6",
"G1*G2*G3#G4*G5*G6",
"H1*H2*H3#H4*H5*H6",
"I1*I2*I3#I4*I5*I6",
"J1*J2*J3#J4*J5*J6"
);

function getTR($data, $cols)
{
    $arr = explode('*', $data);

    if(count($arr) == 0 || $arr[0] == '') return '';

    $ret = "<tr>";

    for($i = 0; $i < $cols; $i++)
    {
        if(isset($arr[$i]))
            $ret .= "<td>".$arr[$i]."</td>";
        else
            $ret .= "<td></td>";
    }

    return $ret."</tr>";
}

function outTable($structure, $cols)
{
    $strings = explode('#', $structure);

    if(count($strings) == 0){
        echo "В таблице нет строк";
        return;
    }

    $datas = "";

    for($i = 0; $i < count($strings); $i++)
    {
        $row = getTR($strings[$i], $cols);
        if($row != "") $datas .= $row;
    }

    if($datas == ""){
        echo "В таблице нет строк с ячейками";
        return;
    }

    echo "<table>".$datas."</table>";
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Лабораторная работа PHP</title>
<link rel="stylesheet" href="style.css">
</head>

<body>

<h1>Лабораторная работа: Таблицы PHP</h1>

<?php

if($cols <= 0){
    echo "Неправильное число колонок";
}
else{

for($i = 0; $i < count($structures); $i++)
{
    echo "<h2>Таблица №".($i+1)."</h2>";
    outTable($structures[$i], $cols);
}

}

?>

</body>
</html>