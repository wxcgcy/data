<?php
/**
 * @abstract 函数
 */

function getIp(){
	$ip = '';
	if ($_SERVER['HTTP_X_FORWARDED_FOR']) {
		$ips = explode(', ', $_SERVER['HTTP_X_FORWARDED_FOR']);
		foreach ($ips as $v) {
			$v = trim($v);
			if(!preg_match('/^(10|172\.16|192\.168|127\.0)\./', $v)) {
				if (strtolower($v) != 'unknown') {
					$ip = $v;
					break;
				}
			}
		}
	} elseif ($_SERVER['HTTP_CLIENT_IP']) {
		$ip = $_SERVER['HTTP_CLIENT_IP'];
	} else {
		$ip = $_SERVER['REMOTE_ADDR'];
	}
	if (!preg_match('/[\d\.]{7,15}/', $ip)) {
		$ip = '';
	} elseif (preg_match('/^(10|172\.16|192\.168|127\.0)\./', $ip)) {
		$ip = '';
	}
	return $ip;
}

function randIp() {
	$ip_long = array(
		array('607649792', '608174079'),//36.56.0.0-36.63.255.255
		array('1038614528', '1039007743'),//61.232.0.0-61.237.255.255
		array('1783627776', '1784676351'),//106.80.0.0-106.95.255.255
		array('2035023872', '2035154943'),//121.76.0.0-121.77.255.255
		array('2078801920', '2079064063'),//123.232.0.0-123.235.255.255
		array('-1950089216', '-1948778497'),//139.196.0.0-139.215.255.255
		array('-1425539072', '-1425014785'),//171.8.0.0-171.15.255.255
		array('-1236271104', '-1235419137'),//182.80.0.0-182.92.255.255
		array('-770113536', '-768606209'),//210.25.0.0-210.47.255.255
		array('-569376768', '-564133889'),//222.16.0.0-222.95.255.255
	);
	$rand_key = mt_rand(0, 9);
	$ip = long2ip(mt_rand($ip_long[$rand_key][0], $ip_long[$rand_key][1]));
	return $ip;
}

function httpRequest($url, $header = array(), $post_data = array()) {
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_TIMEOUT, 10);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
	curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 Chrome/35.0.1916.153 Safari/537.36");
	if ($header) {
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
	}
	if ($post_data) {
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
	}
	$response = curl_exec($ch);
	curl_close($ch);
	return $response;
}

function htmlutf8($html) {
	$html = str_replace(preg_replace('/([\x09\x0A\x0D\x20-\x7E]|[\xC2-\xDF][\x80-\xBF]|\xE0[\xA0-\xBF][\x80-\xBF]|[\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}|\xED[\x80-\x9F][\x80-\xBF]|\xF0[\x90-\xBF][\x80-\xBF]{2}|[\xF1-\xF3][\x80-\xBF]{3}|\xF4[\x80-\x8F][\x80-\xBF]{2})/xs','',$html),'',$html);
	$html = str_replace('', '', $html);
	return $html;
}

//gb->utf-8
function gb2utf8($content) {
	return mb_convert_encoding($content, 'utf-8', 'gbk');
}

function formatSize($size, $decimals = 0) {
	$size = intval($size);
	if (!$size) {
		return 0;
	}
	$unit = array('B', 'K', 'M', 'G', 'T', 'P', 'E', 'Z', 'Y');
	$i = 0;
	while ($size >= 1024 && $i < 8) {
		$size /= 1024;
		$i++;
	}
	$sizei = intval($size);
	$sizef = number_format($size, $decimals);
	$sizez = ($sizef == $sizei) ? $sizei : $sizef;
	return $sizez.$unit[$i];
}

function isHoliday($date = '') {
	$date = $date ? $date : date('Y-m-d');
	$time = strtotime($date);
	$week = date('w', $time);
	if ($week == 0 || $week == 6) {
		return true;
	}
	$month = date('n', $time);
	$day = date('j', $time);
	if ($month == 1 && $day == 1) {
		return true;
	} elseif ($month == 5 && ($day >= 1 && $day <= 3)) {
		return true;
	} elseif ($month == 10 && ($day >= 1 && $day <= 7)) {
		return true;
	}
	return false;
}

function getWeek($date = '') {
	$date = $date ? $date : date('Y-m-d');
	$weeks = array('日', '一', '二', '三', '四', '五', '六');
	return $weeks[date('w', strtotime($date))];
}

function isMobile($uag=''){
	$uag = $uag?$uag:$_SERVER['HTTP_USER_AGENT'];
	$regex_match="/(nokia|iphone|coolpadwebkit|android|motorola|^mot\-|softbank|foma|docomo|kddi|up\.browser|up\.link|320\*480|480\*|240\*|SHARP|";
	$regex_match.="htc|dopod|blazer|netfront|helio|hosin|huawei|novarra|coolpad|webos|techfaith|palmsource|BBK|LG|NEC|";
	$regex_match.="blackberry|alcatel|amoi|ktouch|nexian|samsung|^sam\-|s[cg]h|^lge|ericsson|philips|sagem|wellcom|bunjalloo|maui|";
	$regex_match.="symbian|smartphone|midp|wap|phone|windows ce|iemobile|^spice|^bird|^zte\-|longcos|pantech|gionee|^sie\-|portalmmm|";
	$regex_match.="jig\s browser|hiptop|^ucweb|^benq|haier|^lct|opera\s*mobi|opera\*mini|320x320|240x320|176x220";
	$regex_match.=")/i";
	if(preg_match($regex_match, strtolower($uag))){
		return 1;
	}
	return 0;
}

function ajaxCallback($status, $message = '', $data = array()) {
	$return = array(
		'status' => strval($status),//状态 成功返回0 失败返回其他数字
		'message' => strval($message),//信息，失败时包含说明
		'data' => $data,//数据，成功时返回的数据
	);
	header('Content-type: text/html; charset=utf-8');
	echo json_encode($return);
	exit;
}

function jsRedirect($url = '', $message = '') {
	if ($url == '' || $url == 'back') {
		$js = 'history.back();';
	} else {
		$js = "location.href='{$url}';";
	}
	if (!empty($message)) {
		$js = "alert('{$message}');".$js;
	}
	echo "<script>{$js}</script>";
	exit;
}

function getPager($page,$limit,$total,$param=NULL){
	$page_count = intval($total / $limit);
	if ($total % $limit)
		$page_count++;
	if ($page>5){
		$start = $page-4;
	}else{
		$start = 1;
	}
	if ($start+8>$page_count){
		$end = $page_count;
	}else{
		$end=$start+8;
	}
	$url = $_SERVER['REQUEST_URI'];
	$url = preg_replace('/[?|&]page=[0-9]+/', '', $url);
	$url .= (false === strpos($url, '?') ? '?' : '&')."page=";
	if ($end>1){
		$page_list="<a href='".$url."1'>首页</a>  ";
		for ($i=$start;$i<=$end;$i++){
			$page_list .= $i!=$page ? "<a href='".$url.$i."'>第".$i ."页</a>  " : "{$page} ";
		}
		$page_list=$page_list."<a href='".$url.$page_count."'>尾页</a>  ";
	}else {
		$page_list="";
	}
	return $page_list;
}

function getAdmin() {
	if (!isset($_COOKIE[COOKIE_NAME])) {
		return false;
	}
	$ck = base64_decode($_COOKIE[COOKIE_NAME]);
	list($A, $C) = split(';', $ck);
	if(md5($A.';'.COOKIE_PKEY) != $C) {
		return false;
	}
	$key = $A;
	if (!isset($_SESSION[$key])) {
		return false;
	}
	$data = $_SESSION[$key];
	if (time() - $data['time'] > 3600 * 2) {
		return false;
	}
	$ip = getIp();
	if ($ip != $data['ip']) {
		return false;
	}
	return $data;
}

function setAdmin($name) {
	$time = time();
	$ip = getIp();
	$key = md5(uniqid());
	$_SESSION[$key] = array(
		'name' => $name,
		'time' => $time,
		'ip' => $ip,
	);
	$value = base64_encode($key.md5($key.';'.COOKIE_KEY));
	$expire = 3600 * 2;
	setcookie(COOKIE_NAME, $value, $expire, '/', '.ci123.com', 0);
}

function checkAdmin($username, $password) {
	$_admins = array(
		'5ijim' => '',
		'cztest' => '',
	);
	if (!in_array($_admins, $username)) {
		return false;
	}
	if (md5($password) != $_admins[$username]) {
		return false;
	}
	return true;
}

function turnAge($ages) {
	$ages = str_replace(array(',6岁以上','岁'), '', $ages);
	$ages = str_replace(array('-1,1-','-2,2-','-3,3-','-4,4-','-5,5-'), '-', $ages);
	return $ages;
}
?>