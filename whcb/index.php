<!DOCTYPE html>
<html lang="zh-cn">
<head>
<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<title>外汇储备统计</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
<meta name="format-detection" content="telephone=no">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black">
<link rel="stylesheet" type="text/css" href="../css/style.css" />
<style>
.menu{margin:10px;text-align:center;}
.menu a.on{color:orange;}
.chart{padding:10px;}
</style>
</head>

<body>
<div class="container">
	<div class="menu" id="menu">
		<a href="javascript:void(0);" onclick="initData(0);">近10年（按月）</a> |
		<a href="javascript:void(0);" onclick="initData(1);">全部（按月）</a> |
		<a href="javascript:void(0);" onclick="initData(2);">全部（按年）</a> |
		<a href="javascript:void(0);" onclick="initData(3);">近10月（按月）</a>
	</div>
	<div class="chart" id="chart">
		
	</div>
</div>
<script type="text/javascript" src="../js/jquery-2.1.4.min.js"></script>
<script type="text/javascript" src="../js/public.js"></script>
<script type="text/javascript" src="../js/highcharts/highcharts.js"></script>
<script type="text/javascript" src="../js/highcharts/themes/grid.js"></script>
<script type="text/javascript" src="../js/highcharts/modules/exporting.js"></script>
<script type="text/javascript">
var chart;
var option = {
	chart: {
		renderTo: 'chart',
		type: 'line',
		marginRight: 130,
		marginBottom: 25
	},
	title: {
		text: 'chart',
		x: -20 //center
	},
	subtitle: {
		text: '',
		x: -20
	},
	xAxis: {
		categories: []
	},
	yAxis: {
		title: {
			text: '美元(亿)'
		},
		plotLines: [{
			value: 0,
			width: 1,
			color: '#808080'
		}]
	},
	tooltip: {
		formatter: function() {
				return '<b>'+ this.series.name +'</b><br/>'+
				this.x +': '+ this.y +'亿';
		}
	},
	legend: {
		layout: 'vertical',
		align: 'right',
		verticalAlign: 'top',
		x: -10,
		y: 100,
		borderWidth: 0
	},
	series: [{
		name: 'total',
		data: []
	}]
};

function initData(n) {
	$('#menu a').removeClass('on').eq(n).addClass('on');
	showToast('loading', '数据加载中...');
	setTimeout(function(){hideToast('loading');}, 2000);
	ajaxPost('sub/whcb_tb.php', {ac:'init',n:n}, function(data) {
		option.xAxis.categories = data.data.xaxis;
		option.series[0].data = data.data.series;
		chart = new Highcharts.Chart(option);
		hideToast('loading');
	}, getFuncName(arguments.callee));
}

$(function(){
	initData(0);
});
</script>
</body>
</html>