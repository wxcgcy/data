<?php
/**
 * 拼音
 *
 * @author chenyong
 * @version 3.0
 * @date 2018/03/20
 */

require_once __DIR__.'/../inc/global.php';

$ms = new Mysqls();
$table = 'xhzd_py';

$url = 'http://xh.5156edu.com/pinyi.html';
echo "get $url<br>\n";
$data = getPy($url);
if (!$data) {
	ajaxCallback(-1, "get fail");
}
foreach ($data as $k => $v) {
	$arr = array(
		'name' => $v['name'],
		'initial' => $v['initial'],
		'created' => date('Y-m-d '),
		'src_link' => $v['src_link'],
	);
	$sql = "SELECT * FROM $table WHERE src_link='$arr[src_link]' LIMIT 1";
	$row = $ms->getRow($sql);
	if ($row) {
		$id = $row['id'];
		//$ms->update($table, "id=$id", $arr);
		echo "$v[name]\t$id\tupdated<br>\n";
	} else {
		$id = $ms->insert($table, $arr, true);
		echo "$v[name]\t$id\tadded<br>\n";
	}
}

/**
 * 抓取拼音
 *
 * @param string url
 * @return array
 */
function getPy($url) {
	$html = gb2utf8(htmlutf8(httpRequest($url)));
	$html = str_replace('charset=gb2312', 'charset=utf-8', $html);
	phpQuery::newDocumentHTML($html);
	if (!pq()->text()) {
		ajaxCallback(-1, 'get bs fail : '.$html);
	}
	$data = array();
	$tmp = pq('#table1:eq(1) tr');
	foreach ($tmp as $k => $v) {
		$initial = trim(pq($v)->find('p.font_14')->text());
		$tmp2 = pq($v)->find('td:eq(1) a');
		foreach ($tmp2 as $kk => $vv) {
			$name = trim(pq($vv)->text());
			$src_link = 'http://xh.5156edu.com'.pq($vv)->attr('href');
			$data[] = array(
				'name' => $name,
				'initial' => $initial,
				'src_link' => $src_link,
			);
		}
	}
	return $data;
}
