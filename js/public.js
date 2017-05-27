var tpl_toast = '<div class="toast" id="toast"><div id="toast_success" style="display:none;"><div class="weui_mask_transparent"></div><div class="weui_toast"><i class="weui_icon_toast"></i><p class="weui_toast_content toast_msg">已完成</p></div></div><div id="toast_loading" class="weui_loading_toast" style="display:none;"><div class="weui_mask_transparent"></div><div class="weui_toast"><div class="weui_loading"><div class="weui_loading_leaf weui_loading_leaf_0"></div><div class="weui_loading_leaf weui_loading_leaf_1"></div><div class="weui_loading_leaf weui_loading_leaf_2"></div><div class="weui_loading_leaf weui_loading_leaf_3"></div><div class="weui_loading_leaf weui_loading_leaf_4"></div><div class="weui_loading_leaf weui_loading_leaf_5"></div><div class="weui_loading_leaf weui_loading_leaf_6"></div><div class="weui_loading_leaf weui_loading_leaf_7"></div><div class="weui_loading_leaf weui_loading_leaf_8"></div><div class="weui_loading_leaf weui_loading_leaf_9"></div><div class="weui_loading_leaf weui_loading_leaf_10"></div><div class="weui_loading_leaf weui_loading_leaf_11"></div></div><p class="weui_toast_content toast_msg">数据加载中</p></div></div><div id="toast_error" style="display:none;"><div class="weui_toptips js_tooltips toast_err toast_msg" style="display:block;">注意</div></div><div id="toast_tip" style="display:none;"><div class="weui_toptips js_tooltips toast_tip toast_msg" style="display:block;">提示</div></div></div>';
var tpl_dialog = '<div class="dialog" id="dialog"><div id="dialog_confirm" style="display:none;"><div class="weui_mask"></div><div class="weui_dialog"><div class="weui_dialog_hd"><strong class="weui_dialog_title">确认</strong></div><div class="weui_dialog_bd">确定吗？</div><div class="weui_dialog_ft"><a href="javascript:;" class="weui_btn_dialog default">取消</a><a href="javascript:;" class="weui_btn_dialog primary">确定</a></div></div></div><div id="dialog_alert" style="display:none;"><div class="weui_mask"></div><div class="weui_dialog"><div class="weui_dialog_hd"><strong class="weui_dialog_title">提示</strong></div><div class="weui_dialog_bd">提示信息</div><div class="weui_dialog_ft"><a href="javascript:;" class="weui_btn_dialog primary">确定</a></div></div></div></div>';

function goUrl(url) {
	window.location = url;
}

function showTip(flag, message) {
	alert(message);
}

function hlTabbar(n) {
	$('#tabbar .weui_tabbar_item').removeClass('weui_bar_item_on');
	$('#tabbar .weui_tabbar_item:eq('+n+')').addClass('weui_bar_item_on');
}

function showToast(mold, message) {// success loading error tip
	if(!$('#toast').length) {
		$('body').append(tpl_toast);
	}
	if (message) {
		$('#toast_'+mold+' .toast_msg').html(message);
	}
	$('#toast_'+mold).show();
}

function hideToast(mold) {
	$('#toast_'+mold).hide();
}

function showDialog(mold, title, message) {// alert confirm
	if(!$('#dialog').length) {
		$('body').append(tpl_dialog);
	}
	if (title) {
		$('#dialog_'+mold+' .weui_dialog_title').html(title);
	}
	if (message) {
		$('#dialog_'+mold+' .weui_dialog_bd').html(message);
	}
	if (mold == 'confirm') {
		$('#dialog_'+mold+' .weui_dialog_ft .default').unbind('click').bind('click',function(){hideDialog(mold);});
	}
	$('#dialog_'+mold).show();
}

function bindDialog(mold, func) {
	$('#dialog_'+mold+' .primary').unbind('click').bind('click',func);
}

function hideDialog(mold) {
	$('#dialog_'+mold).hide();
}

function getFuncName(_callee) {
	var _text = _callee.toString();
	if (/^function\s*\(.*\).*\r\n/.test(_text)) {
		var _tempArr = _scriptArr[i].text.substr(0, _start).split('\r\n');
		alert(_scriptArr[i].text);
		return _tempArr[_tempArr.length - 1].replace(/(var)|(\s*)/g, '').replace(/=/g, '');
	} else {
		return _text.match(/^function\s*([^\(]+).*\s*\n/)[1];
	}
}

var ajax_post = {};
function ajaxPost(url, param, callback, fname) {
	if (ajax_post[fname]) {
		return false;
	}
	ajax_post[fname] = 1;
	$.post(url, param, function(data) {
		if (!data || !data.hasOwnProperty('status')) {
			showTip(0, '系统繁忙');
		} else {
			if (data.status == '0') {
				callback(data);
			} else if (data.message == 'need login') {
				window.location = 'login.php';
			} else if (data.message == 'no power') {
				showTip(0, '无权限操作');
			} else {
				showTip(0, data.message);
			}
		}
		ajax_post[fname] = 0;
	}, 'json');
}

var ajax_get = {};
function ajaxGet(url, callback, fname) {
	if (ajax_get[fname]) {
		return false;
	}
	ajax_get[fname] = 1;
	$.get(url, {}, function(data) {
		if (!data || !data.hasOwnProperty('status')) {
			showTip(0, '系统繁忙');
		} else {
			if (data.status == '0') {
				callback(data);
			} else if (data.message == 'need login') {
				window.location = 'login.php';
			} else if (data.message == 'no power') {
				showTip(0, '无权限操作');
			} else {
				showTip(0, data.message);
			}
		}
		ajax_get[fname] = 0;
	}, 'json');
}