<?php
return array(
    //'配置项'              =>  '配置值'
    'PRO_URL'               =>  'http://'.$_SERVER['HTTP_HOST'].'', //项目所在url地址
    'URL_MODEL'             =>  0,           // URL访问模式--普通模式
    'SHOW_PAGE_TRACE'       =>  false,
    'TMPL_L_DELIM'          =>  '<{',        // 模板引擎普通标签开始标记
    'TMPL_R_DELIM'          =>  '}>',        // 模板引擎普通标签结束标记
    'ADMIN_SESSION'         =>  'suser',     // 后台登陆用户session名
    'MESSAGETYPE'           =>  array(
        1 => '系统消息',
        2 => '课程消息',
        3 => '投标消息',
        4 => '邀标消息',
    ), //消息类别
    'LOAD_EXT_CONFIG' => 'db',
);