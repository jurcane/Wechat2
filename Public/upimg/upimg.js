var upload_switch=0;//上传开始开关
function up_img_fun(pic_deal_url){
    if(!pic_deal_url) pic_deal_url='./index.php?m=Wechat&c=Common&a=up';
    $('.ke-dialog').remove();$('.ke-dialog-mask').remove();
    var upImgStr='<div class="ke-dialog-default ke-dialog" style="width:400px;height:90px;position:fixed;display:block;"><div class="ke-dialog-content" ><div class="ke-dialog-header">本地图片<span class="ke-dialog-icon-close" onclick="$(\'.ke-dialog\').remove();$(\'.ke-dialog-mask\').remove();"></span></div><div class="ke-dialog-body" style="height:60px;"><div style="padding:20px;"><div class="tab2"><iframe name="upload_iframe" id="upload_iframe" onload="iframe_load_fun()" style="display:none;"></iframe>';
    upImgStr+='<form class="ke-upload-area ke-form" name="upload_from" method="post" enctype="multipart/form-data" target="upload_iframe" action="'+pic_deal_url+'">';
    upImgStr+='<div class="ke-dialog-row"><label>上传文件</label><input type="text" name="localUrl" class="ke-input-text" tabindex="-1" style="width:200px;" readonly="true">&nbsp;<div class="ke-inline-block ke-upload-button"><div class="ke-upload-area" style="width: 60px;"><span class="ke-button-common"><input type="button" class="ke-button-common ke-button" value="浏览..."></span>';
    upImgStr+='<input type="file" class="ke-upload-file" name="pic" tabindex="-1" onchange="file_input_change()" />';
    upImgStr+='</div></div></div></form></div></div></div></div><div class="ke-dialog-shadow"></div></div>';
    upImgStr+='<div style="width:100%;position:fixed;z-index:811212;display:block;" class="ke-dialog-mask"></div>';
    $('body').append(upImgStr);
    var bodyWidth = document.documentElement.clientWidth;
    var bodyHeight = document.documentElement.clientHeight;
    //图片框和蒙版定位
    var dialogTop=parseInt(bodyHeight/2)-50;
    var dialogLeft=parseInt(bodyWidth/2)-200;
    $('.ke-dialog-default').css({top:dialogTop+'px',left:dialogLeft+'px'});
    $('.ke-dialog-mask').css({height:bodyHeight+'px'});
    upload_switch=1;
}

function file_input_change(){
	document.upload_from.submit();
}
function iframe_load_fun(){
    //图片处理
    if(upload_switch==1){
        var resHtml = $('#upload_iframe').contents().find('body').html();
        resHtml=$.trim(resHtml.replace(/<[^>]+>/g,""));//去除html标签和多余空格
        resHtml=$.parseJSON(resHtml);
        if(resHtml.status==1){
            up_img_notify(resHtml.info);
        }else{
            alert(resHtml.info);
        }
        $('.ke-dialog').remove();$('.ke-dialog-mask').remove();upload_switch=0;
    }
}