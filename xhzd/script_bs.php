<?php
/**
 * 部首
 *
 * @author chenyong
 * @version 3.0
 * @date 2018/03/20
 */

require_once __DIR__.'/../inc/global.php';

$ms = new Mysqls();
$table = 'xhzd_bs';

$url = 'http://xh.5156edu.com/bs.html';
echo "get $url<br>\n";
$data = getBs($url);
if (!$data) {
	ajaxCallback(-1, "get fail");
}
foreach ($data as $k => $v) {
	$arr = array(
		'name' => $v['name'],
		'stroke_num' => $v['stroke_num'],
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
 * 抓取部首
 *
 * @param string url
 * @return array
 */
function getBs($url) {
	$html = gb2utf8(htmlutf8(httpRequest($url)));
	$html = str_replace('charset=gb2312', 'charset=utf-8', $html);
	phpQuery::newDocumentHTML($html);
	if (!pq()->text()) {
		ajaxCallback(-1, 'get bs fail : '.$html);
	}
	$cn_num = array('零','一','二','三','四','五','六','七','八','九','十','十一','十二','十三','十四','十五','十六','十七','十八','十九','二十');
	$data = array();
	$tmp = pq('#table1:eq(1) tr');
	foreach ($tmp as $k => $v) {
		$stroke_num = preg_replace('/[^\d]+/', '', str_replace(array_reverse($cn_num), array_reverse(array_keys($cn_num)), pq($v)->find('p.font_14')->text()));
		$tmp2 = pq($v)->find('td:eq(1) a');
		foreach ($tmp2 as $kk => $vv) {
			$name = trim(pq($vv)->text());
			$src_link = 'http://xh.5156edu.com'.pq($vv)->attr('href');
			$data[] = array(
				'name' => $name,
				'stroke_num' => $stroke_num,
				'src_link' => $src_link,
			);
		}
	}
	return $data;
}
