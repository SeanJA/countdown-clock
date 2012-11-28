<?php

header ('Content-type:image/gif');

date_default_timezone_set('America/Halifax');
include 'GIFEncoder.class.php';
include 'php52-fix.php';


$time = $_GET['time'];
$future_date = new DateTime(date('r',strtotime($time)));
$time_now = time();
$now = new DateTime(date('r', $time_now));


$frames = array();
$delays = array();


$image = imagecreatefrompng('countdown.png');
$delay = 100; // milliseconds
$font = array(
	'size'=>40,
	'angle'=>0,
	'x-offset'=>10,
	'y-offset'=>70,
	'file'=>'DIGITALDREAM.ttf',
	'color'=>imagecolorallocate($image, 255, 255, 255),
);
for($i = 0; $i <= 60; $i++){
	$interval = date_diff($future_date, $now);
	if($future_date < $now){
		// Open the first source image and add the text.
		$image = imagecreatefrompng('countdown.png');;
		$text = $interval->format('00:00:00:00');
		imagettftext ($image , $font['size'] , $font['angle'] , $font['x-offset'] , $font['y-offset'] , $font['color'] , $font['file'], $text );
		ob_start();
		imagegif($image);
		$frames[]=ob_get_contents();
		$delays[]=$delay;
		ob_end_clean();
		break;
	} else {
		// Open the first source image and add the text.
		$image = imagecreatefrompng('countdown.png');;
		$text = $interval->format('%D:%H:%I:%S');
		imagettftext ($image , $font['size'] , $font['angle'] , $font['x-offset'] , $font['y-offset'] , $font['color'] , $font['file'], $text );
		ob_start();
		imagegif($image);
		$frames[]=ob_get_contents();
		$delays[]=$delay;
		ob_end_clean();
	}
	$now->modify('+1 second');
}
// $GIF_src, $GIF_dly, $GIF_lop, $GIF_dis,
// 							$GIF_red, $GIF_grn, $GIF_blu, $GIF_mod
// Generate the animated gif and output to screen.
$gif = new GIFEncoder($frames,$delays,-1,2,0,0,0,'bin');
echo $gif->GetAnimation();