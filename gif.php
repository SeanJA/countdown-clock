<?php

date_default_timezone_set('America/Halifax');
include 'GIFEncoder.class.php';
include 'countdown_functions.php';


$time = $_GET['time'];
$future_date = new DateTime(date('r', strtotime($time)));
$time_now = time();
$now = new DateTime(date('r', $time_now));


$frames = array();
$delays = array();

$image = imagecreatefrompng('countdown.png');
$font = array(
	'size' => 40,
	'angle' => 0,
	'x-offset' => 10,
	'y-offset' => 70,
	'file' => __DIR__ . '/DIGITALDREAM.ttf',
	'color' => imagecolorallocate($image, 255, 255, 255),
);

$result = generate_countdown_frames($future_date, $now, function ($text) use ($font) {
	$image = imagecreatefrompng('countdown.png');
	imagettftext($image, $font['size'], $font['angle'], $font['x-offset'], $font['y-offset'], $font['color'], $font['file'], $text);
	ob_start();
	imagegif($image);
	$frame = ob_get_contents();
	ob_end_clean();
	return $frame;
});

$frames = $result['frames'];
$delays = $result['delays'];
$loops = $result['loops'];

//expire this image instantly
header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');
$gif = new AnimatedGif($frames, $delays, $loops);
$gif->display();