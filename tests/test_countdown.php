<?php

require __DIR__ . '/../countdown_functions.php';

function run_case(int $secondsAhead): array {
	$now = new DateTime('2026-01-01 12:00:00');
	$future = clone $now;
	$future->modify("+{$secondsAhead} seconds");
	$captured = array();
	$result = generate_countdown_frames($future, clone $now, function ($text) use (&$captured) {
		$captured[] = $text;
		return $text;
	});
	// recompute using a fresh now since function mutates; but captured already
	return array('result' => $result, 'texts' => $captured);
}

$failures = 0;
function assert_eq($name, $actual, $expected) {
	global $failures;
	if ($actual !== $expected) {
		echo "FAIL: $name — expected " . var_export($expected, true) . " got " . var_export($actual, true) . "\n";
		$failures++;
	} else {
		echo "PASS: $name\n";
	}
}

// Case 1: future exactly now (0 seconds)
$r = run_case(0);
assert_eq('0s: frame count', count($r['texts']), 1);
assert_eq('0s: last text', end($r['texts']), '00:00:00:00');
assert_eq('0s: loops', $r['result']['loops'], 1);

// Case 2: 5 seconds ahead
$r = run_case(5);
assert_eq('5s: frame count', count($r['texts']), 6);
assert_eq('5s: first text', $r['texts'][0], '00:00:00:05');
assert_eq('5s: last text', end($r['texts']), '00:00:00:00');
assert_eq('5s: second-to-last', $r['texts'][count($r['texts'])-2], '00:00:00:01');
assert_eq('5s: loops', $r['result']['loops'], 1);

// Case 3: 60 seconds (1 minute) ahead — the reported bug case
$r = run_case(60);
assert_eq('60s: frame count', count($r['texts']), 61);
assert_eq('60s: first text', $r['texts'][0], '00:00:01:00');
assert_eq('60s: last text', end($r['texts']), '00:00:00:00');
assert_eq('60s: second-to-last', $r['texts'][count($r['texts'])-2], '00:00:00:01');
assert_eq('60s: loops', $r['result']['loops'], 1);

// Case 4: 120 seconds (2 minutes) ahead — exceeds 60-frame cap
$r = run_case(120);
assert_eq('120s: frame count', count($r['texts']), 61);
assert_eq('120s: loops', $r['result']['loops'], 0);
assert_eq('120s: last text', end($r['texts']), '00:00:01:00');

// Case 5: past (future before now) — single zero frame
$now = new DateTime('2026-01-01 12:00:00');
$past = clone $now;
$past->modify('-10 seconds');
$captured = array();
$res = generate_countdown_frames($past, clone $now, function ($t) use (&$captured) { $captured[] = $t; return $t; });
assert_eq('past: frame count', count($captured), 1);
assert_eq('past: text', $captured[0], '00:00:00:00');
assert_eq('past: loops', $res['loops'], 1);

echo "\n" . ($failures === 0 ? "ALL TESTS PASSED" : "$failures FAILURE(S)") . "\n";
exit($failures === 0 ? 0 : 1);