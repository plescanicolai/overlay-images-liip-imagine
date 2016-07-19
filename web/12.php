<?php
// исходное изображение
$img="images/1234.jpg";

// imagecreatefrompng - создаёт новое изображение из файла или URL
// водяной знак
$wm=imagecreatefrompng('images/123.png');

// imagesx - получает ширину изображения
$wmW=imagesx($wm);

// imagesy - получает высоту изображения
$wmH=imagesy($wm);

// imagecreatetruecolor - создаёт новое изображение true color
$image=imagecreatetruecolor($wmW, $wmH);

// выясняем расширение изображения на которое будем накладывать водяной знак
if(preg_match("/.gif/i",$img)):
    $image=imagecreatefromgif($img);
elseif(preg_match("/.jpeg/i",$img) or preg_match("/.jpg/i",$img)):
    $image=imagecreatefromjpeg($img);
elseif(preg_match("/.png/i",$img)):
    $image=imagecreatefrompng($img);
else:
    die("Ошибка! Неизвестное расширение изображения");
endif;
// узнаем размер изображения
$size=getimagesize($img);

// указываем координаты, где будет располагаться водяной знак
/*
* $size[0] - ширина изображения
* $size[1] - высота изображения
* - 10 -это расстояние от границы исходного изображения
*/
$cx=$size[0]-$wmW-10;
$cy=$size[1]-$wmH-10;

/* imagecopyresampled - копирует и изменяет размеры части изображения
* с пересэмплированием
*/
imagecopyresampled ($image, $wm, $cx, $cy, 0, 0, $wmW, $wmH, $wmW, $wmH);

/* imagejpeg - создаёт JPEG-файл filename из изображения image
* третий параметр - качество нового изображение
* параметр является необязательным и имеет диапазон значений
* от 0 (наихудшее качество, наименьший файл)
* до 100 (наилучшее качество, наибольший файл)
* По умолчанию используется значение по умолчанию IJG quality (около 75)
*/
imagejpeg($image,$img,90);

// imagedestroy - освобождает память
imagedestroy($image);

imagedestroy($wm);

// на всякий случай
unset($image,$img);