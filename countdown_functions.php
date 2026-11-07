<?php

/**
 * Generate countdown animation frames.
 *
 * Behaviour by remaining interval (S = seconds until target):
 *   - S <= 60 or S >= 600: count down by seconds (1s per frame, 1s delay).
 *   - 60 < S < 600: compress the whole interval into 60 frames so the
 *     countdown lands on 0 when the time is up (step = S/60 s per frame).
 *
 * @param DateTime $future_date Target date/time.
 * @param DateTime $now        Current date/time (not mutated).
 * @param callable $render     function(string $text): mixed
 * @return array{frames:array,delays:array,loops:int}
 */
function generate_countdown_frames(DateTime $future_date, DateTime $now, callable $render) {
	$frames = array();
	$delays = array();
	$loops = 0;

	$intervalSeconds = $future_date->getTimestamp() - $now->getTimestamp();

	// Past or exactly now: single zero frame.
	if ($intervalSeconds <= 0) {
		$frames[] = $render('00:00:00:00');
		$delays[] = 100;
		$loops = 1;
		return array('frames' => $frames, 'delays' => $delays, 'loops' => $loops);
	}

	// Decide step (seconds decremented per frame) and delay (in 1/100s, GIF units).
	if ($intervalSeconds <= 60 || $intervalSeconds >= 600) {
		$step = 1;
	} else {
		$step = $intervalSeconds / 60.0;
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
			$loops = 1;
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
		$loops = 0;
	}

	return array('frames' => $frames, 'delays' => $delays, 'loops' => $loops);
}