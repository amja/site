<?php
$pre = urldecode($_GET['i']);
$i = str_replace('"', "", $pre);
//echo $i;
$image = imagecreatefromjpeg($i);
$resized = imagecreatetruecolor(384, 384);
imagecopyresampled($resized, $image, 0, 0, 0, 0, 384, 384, $_GET['w'], $_GET['w']);
header('Content-Type: image/jpeg');
imagejpeg($resized);
imagedestroy($image);
imagedestroy($resized);
?>
