<?php
/**
 * Кроп 
 * /thumb.php?src=pic/test.jpg&width=500&height=500&crop=1
 * 
 * 
 * Без кропа. Добавить белые поля 
 * /thumb.php?src=pic/test.jpg&width=500&height=500
 */
error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
ini_set("display_errors", 1);		

require __DIR__ . '/../vendor/autoload.php';
use ASweb\Imageresizer\Imageresizer;

$ftypes = array(1 => "gif", 2 => "jpg", 3 => "png", 4 => "swf", 5 => "psd", 6 => "bmp");
$ims = getimagesize("./".$_GET['src']);
$ftype = $ftypes[$ims[2]];

header("Expires: ".date("D, d M Y H:i:s", time()+30+86400));
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-control: public");
header('Pragma: public');

if($ftype == "jpg") {
	header("Content-type: image/jpeg");
} elseif($ftype == "gif") {
	header("Content-type: image/gif");
} elseif($ftype == "png") {
	header("Content-type: image/png");
}

$width = 0 + $_GET['width'];
$height = 0 + $_GET['height'];
$crop = 0 + $_GET['crop'];
$optimized = 0 + $_GET['optimized'];

$ir = new Imageresizer();
$ir->src = "./".$_GET['src'];
$ir->type = $ir->outtype = $ftype;
$ir->optimized = $optimized;

$sz = getimagesize($ir->src);

if ($width && $height && $crop == 1) {
	$ir->dstimgw = $width;
	$ir->dstimgh = $height;

	if (round($sz[1] * $width / $sz[0]) < $height) {
		$ratio = $sz[1] / $height;
		$ir->srcx = round(($sz[0] - $width * $ratio)/2);
		$ir->srcw = $sz[0] - 2 * $ir->srcx;
		$ir->srch = $sz[1];
		$ir->srcy = 0;
	} else {
		$ratio = $sz[0] / $width;
		$ir->srcx = 0;
		$ir->srcy = round(($sz[1] - $height * $ratio)/2);
		$ir->srcw = $sz[0];
		$ir->srch = $sz[1] - 2 * $ir->srcy;
	}
} elseif($width && $height && $crop == 0) {
	$ir->dstimgw = $width;
	$ir->dstimgh = $height;

	if (round($sz[1] * $width / $sz[0]) < $height) {	// картинка горизонтальная
		$ratio = $sz[0] / $width;
		$ir->dstw = $width;
		$ir->dsth = $sz[1] / $ratio;
		$ir->srcx = 0;
		$ir->srcw = $sz[0];
		$ir->srch = $sz[1];
		$ir->srcy = 0;
		$ir->dsty = round(($ir->dstimgh - $ir->dsth)/2);
		$ir->dstx = 0;
	} else {											// картинка вертикальная
		$ratio = $sz[1] / $height;
		$ir->dstw = $sz[0] / $ratio;
		$ir->dsth = $height;
		$ir->srcx = 0;
		$ir->srcw = $sz[0];
		$ir->srch = $sz[1];
		$ir->srcy = 0;
		$ir->dsty = 0;
		$ir->dstx = round(($ir->dstimgw - $ir->dstw)/2);
	}
} elseif($width && $height == 0) {
	$ir->dstimgw = min($width, $sz[0]);
	$ir->dstimgh = round($sz[1] * $ir->dstimgw / $sz[0]);
} elseif($height && $width == 0) {
	$ir->dstimgh = min($height, $sz[1]);
	$ir->dstimgw = round($sz[0] * $ir->dstimgh / $sz[1]);
} elseif($height == 0 && $width == 0) {
	$ir->dstimgw = $sz[0];
	$ir->dstimgh = $sz[1];
}

$ir->dst = '';
$ir->resize();