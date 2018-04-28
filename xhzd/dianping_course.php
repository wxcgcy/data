<?php
include '../inc/global.php';

$ms = new Mysqls();
$table = 'dianping_course';

$sql = "SELECT * FROM dianping_firm WHERE id>=0 ORDER BY id ASC";
$rows = $ms->getRows($sql);
foreach ($rows as $k => $v) {
	$i_firm = $v['title'];
	echo "$v[id]\t$v[i_id]\t";

	if ($v['num_course'] <= 0) {
		echo "no course\n";
		continue;
	}

	$count = 0;
	$list_data = getList($v['i_id'], $v['num_course']);
	if (!$list_data) {
		echo "get fail\n\n";
		exit;
	}
	echo "get success\t".count($list_data)."\n";
	foreach ($list_data as $kk => $vv) {
		$count++;
		$i_id = $vv['i_id'];
		$detail_url = 'http://www.dianping.com/shop/'.$v['i_id'].'/wedding/product/'.$i_id;
		echo "\t$count\t$i_id\t";

		$sql = "SELECT * FROM $table WHERE i_id='$i_id' LIMIT 1";
		$row_course = $ms->getRow($sql);
		if ($row_course) {
			echo "exist\n";
			continue;
		}

		$detail_data = getDetail($detail_url);
		if (!$detail_data) {
			echo "fail\n";
			sleep(1);
			continue;
		}
		echo "success\t";
		$arr = array_merge($vv, $detail_data);
		$arr['i_firm'] = $i_firm;
		$arr['i_id_firm'] = $v['i_id'];
		$sql = "SELECT * FROM $table WHERE i_id='$i_id' LIMIT 1";
		$row_course = $ms->getRow($sql);
		if ($row_course) {
			$id = $row_course['id'];
			$ms->update($table, "id=$id", $arr);
			echo "$id,updated\t";
		} else {
			$id = $ms->insert($table, $arr, true);
			echo "$id,added\t";
		}
		echo "\n";
		sleep(mt_rand(1,2));
	}
	sleep(1);
}

function getList($shop_id, $num_course) {
	$url = 'http://www.dianping.com/wed/ajax/shopweb/product_products';
	$postdata = array(
		'shopId' => $shop_id,
		'tagValues' => '',
		'productCategoryId' => 0,
		'page' => 1,
		'fallPage' => 1
	);
	$data = array();
	$n = ceil($num_course / 15);
	while (1) {
		for ($i = 1; $i <= 3; $i++) {
			$result = json_decode(htmlutf8(httppost($url, $postdata)), true);
			$html = $result['msg']['html'];
			$ended = $result['msg']['isFallEnded'];
			phpQuery::newDocumentHTML($html);
			if (pq()->text()) {
				break;
			}
			sleep($i);
		}
		if (!pq()->text()) {
			break;
		}
		if (pq()->text() == '很抱歉，商户暂无产品') {
			break;
		}
		$tmp = pq('li');
		foreach ($tmp as $k => $v) {
			$i_id = preg_replace('/.*product\//', '', pq($v)->find('.pic a')->attr('href'));
			$logo = pq($v)->find('.pic img')->attr('src');
			$title = trim(pq($v)->find('.pic-name h3')->text());
			$prices = trim(pq($v)->find('.pic-name .originalPrice')->text());
			$data[] = array(
				'logo' => $logo,
				'title' => $title,
				'prices' => $prices,
				'i_id' => $i_id,
			);
		}
		if ($ended) {
			break;
		}
		$postdata['fallPage']++;
		sleep(1);
	}
	return $data;
}

function getDetail($detail_url) {
	for ($i = 1; $i <= 3; $i++) {
		phpQuery::newDocumentFile($detail_url);
		if (!pq('body')->text()) {
			$html = htmlutf8(httpget($detail_url));
			phpQuery::newDocumentHTML($html, 'utf-8');
		}
		if (pq('body')->text()) {
			break;
		}
		sleep($i);
	}
	if (!pq('body')->text()) {
		return false;
	}
	$pic = pq('.picshow .mainpic img')->attr('src');
	$pics = array();
	$tmp = pq('.picshow .slidephotos li img');
	foreach ($tmp as $k => $v) {
		$v = pq($v)->attr('data-large');
		$pics[] = $v;
	}
	$pics = implode(",", $pics);
	$specials = trim(str_replace(array("\t"," "), '', pq('.shopinfor .detail li span:eq(1)')->text()));
	$i_ages = '';
	$types = '';
	$hours = '';
	$cont = '';
	$details = array();
	$tmp = pq('#J_boxAgraph table td');
	foreach ($tmp as $k => $v) {
		$t_key = trim(str_replace('：', '', pq($v)->find('.tit')->text()));
		$t_val = trim(pq($v)->find('.cont')->text());
		if ($t_key == '适合年龄段(岁)') {
			$i_ages = $t_val;
		} elseif ($t_key == '课程类型') {
			$types = $t_val;
		} elseif ($t_key == '总课时数') {
			$hours = $t_val;
		} elseif ($t_key == '课程介绍') {
			$cont = $t_val;
			continue;
		}
		$details[$t_key] = $t_val;
	}
	$ages = turnage($i_ages);
	$desc = strip_tags($cont);
	$desc = mb_strlen($desc, 'utf-8') > 80 ? mb_substr($desc, 0, 80, 'utf-8').'...' : $desc;
	$details = serialize($details);
	$photos = array();
	$tmp = pq('.graph-box li img');
	foreach ($tmp as $k => $v) {
		$v = pq($v)->attr('data-lazyload');
		$photos[] = $v;
	}
	$photos = implode(",", $photos);

	$data = array(
		'desc' => $desc,
		'cont' => $cont,
		'pic' => $pic,
		'ages' => $ages,
		'types' => $types,
		'hours' => $hours,
		'specials' => $specials,
		'pics' => $pics,
		'details' => $details,
		'photos' => $photos,
		'i_ages' => $i_ages,
	);
	return $data;
}
?>
