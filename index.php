<?php
/**
 * 首页
 */

require_once __DIR__.'/inc/global.php';

$data = array(
	array('title' => '外汇储备', 'url' => './whcb/'),
);
?>
<!DOCTYPE html>
<html lang="zh-cn">
<head>
<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<title>data</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
<meta name="format-detection" content="telephone=no">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black">
<link rel="shortcut icon" type="image/x-icon" href="images/favicon.ico">
<link rel="stylesheet" type="text/css" href="css/weui.0.4.0.min.css" />
</head>

<body>
<div class="container wbg">
	<div class="cell">
		<div class="hd">
			<h1 class="page_title">data</h1>
		</div>
		<div class="bd">
			<div class="weui_cells_title">list</div>
			<div class="weui_cells weui_cells_access">
<?php foreach ($data as $k => $v) {?>
				<a class="weui_cell" href="<?php echo $v['url'];?>">
					<div class="weui_cell_bd weui_cell_primary"><p><?php echo $v['title'];?></p></div>
					<div class="weui_cell_ft"></div>
				</a>
<?php }?>
			</div>
		</div>
	</div>
</div>
</body>
</html>
