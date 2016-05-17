/***********
 * 提示气泡
 * @example:  
 ***********/
function note_info(msg,obj,type){
	var sel = '#'+$(obj).attr('id');
	var color = type=='warn'?'#F58323':'#009997';
	layer.tips(msg, sel, {
	    tips: [1, color] //还可配置颜色
	})
	setTimeout("layer.closeAll()",1500);
}