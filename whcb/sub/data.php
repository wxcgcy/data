<?php
require __DIR__ . '/../../inc/global.php';

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
	ajaxCallback(-1, 'deny');
}

$db = new Mysqls();

if ($_POST['ac'] == 'chart') {
	$n = intval($_POST['n']);
	switch ($n) {
		case 1:
			$m = 10;
			$sql = "select * from whcb_tj where type='month' order by id asc";
			$rows = $db->getRows($sql);
			break;
		case 2:
			$m = 2;
			$sql = "select * from whcb_tj where type='year' order by id asc";
			$rows = $db->getRows($sql);
			break;
		case 3:
			$m = 1;
			$sql = "select * from whcb_tj where type='month' order by id desc limit 10";
			$rows = $db->getRows($sql);
			$rows = array_reverse($rows);
			break;
		default:
			$m = 05;
			$sql = "select * from whcb_tj where type='month' and dt>'".date('Ym', strtotime('-10 year'))."' order by id asc";
			$rows = $db->getRows($sql);
			break;
	}
	if (!$rows) {
		ajaxCallback(-1, 'no data');
	}
	$xaxis = array();
	$series = array();
	foreach ($rows as $k => $v) {
		$xaxis[] = $v['dt'] % $m == 0 ? strval($v['dt']) : '';
		$series[] = floatval($v['num']);
	}
	$data = array(
		'xaxis' => $xaxis,
		'series' => $series
	);
	ajaxCallback(0, '', $data);
}