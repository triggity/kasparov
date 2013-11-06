<html>
<head>
</head>
<body>

<?php
$pic=ImageCreate(600,300);
$col1=ImageColorAllocate($pic, 150,100,75);
$col2=ImageColorAllocate($pic, 0, 0, 255);
ImageFilledRectangle($pic, 1, 1, 100, 100, $col1);
ImageFilledRectangle($pic, 2, 12, 125, 125, $col2);
ImagePNG($pic, "pic.png");
ImageDestroy($pic);
?>

<img src="pic.png" border=0>
</body>
</html>
