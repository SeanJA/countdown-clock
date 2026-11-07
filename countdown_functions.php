<?php

/**
 * Generate countdown animation frames.
 *
 * @param DateTime $future_date Target date/time.
 * @param DateTime $now        Current date/time (mutated, advanced 1s per frame).
 * @param callable $render     function(string $text): mixed
 * @return array{frames:array,delays:array,loops:int}
 */
function generate_countdown_frames(DateTime $future_date, DateTime $now, callable $render) {
	$frames = array();
	$delays = array();
	$delay = 100;
	$loops = 0;

	for ($i = 0; $i <= 60; $i++) {
		$interval = date_diff($future_date, $now);
		if ($future_date <= $now) {
			$frames[] = $render('00:00:00:00');
			$delays[] = $delay;
			$loops = 1;
			break;
		} else {
			$text = $interval->format('%a:%H:%I:%S');
			if (preg_match('/^[0-9]\:/', $text)) {
				$text = '0' . $text;
			}
			$frames[] = $render($text);
			$delays[] = $delay;
			$loops = 0;
		}
		$now->modify('+1 second');
	}

	return array('frames' => $frames, 'delays' => $delays, 'loops' => $loops);
}