<?php

function generate_countdown_frames(DateTime $future_date, DateTime $now, callable $render) {
	$frames = array();
	$delays = array();
	$loops = -1;

	$intervalSeconds = $future_date->getTimestamp() - $now->getTimestamp();

	if ($intervalSeconds <= 0) {
		$frames[] = $render('00:00:00:00');
		$delays[] = 100;
		$loops = 0;
		return array('frames' => $frames, 'delays' => $delays, 'loops' => $loops);
	}

	if ($intervalSeconds <= 60 || $intervalSeconds >= 600) {
		$step = 1;
	} else {
		$step = 2;
	}
	$delay = (int) round($step * 100);
	if ($delay < 100) {
		$delay = 100;
	}

	$S = $intervalSeconds;
	for ($i = 0; $i <= 60; $i++) {
		$remaining = $S - $i * $step;
		if ($remaining <= 0) {
			$frames[] = $render('00:00:00:00');
			$delays[] = $delay;
			$loops = 0;
			break;
		}
		$r = (int) round($remaining);
		$days = (int) floor($r / 86400);
		$r %= 86400;
		$hours = (int) floor($r / 3600);
		$r %= 3600;
		$mins = (int) floor($r / 60);
		$secs = $r % 60;
		$text = sprintf('%02d:%02d:%02d:%02d', $days, $hours, $mins, $secs);
		$frames[] = $render($text);
		$delays[] = $delay;
	}

	return array('frames' => $frames, 'delays' => $delays, 'loops' => $loops);
}