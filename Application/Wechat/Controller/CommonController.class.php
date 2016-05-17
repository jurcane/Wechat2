<?php
namespace Wechat\Controller;

class CommonController extends BaseController{
    function __construct() {
        parent::__construct();
    }
    public function login() {
        if ($this->auth()) {
            header('Location: ' . U('index/index'));
        }
        $this->display();
    }
    public function login_do() {
        $return = ['status' => 1, 'info' => 'error'];
        $data['username'] = I('post.name');
        $data['password'] = I('post.pass', '', 'md5');
        $info = M('Admin')->where($data)->find();
        if (empty($info)) {
            $return['info'] = '账号或密码错误';
            $this->ajaxReturn($return);
        } else {
            $return['status'] = '0';
            session('is_login', true);
            $info['last_login_time'] = time();
            $info['last_login_ip'] = get_client_ip();
            unset($info['password']);
            session('user', $info);//用户登录session创建
            session('usernamea', $info['username']);//用户登录session创建
            M('admin')->where(['id' => $info['id']])->save(['last_login_time' => $info['last_login_time'], 'last_login_ip' => $info['last_login_ip']]);
            $this->ajaxReturn($return);
        }
    }
    public function login_out() {
        session(null);
        header('Location: ' . U('login'));
    }
	public function up() {
		$upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize   =     3145728 ;// 设置附件上传大小
        $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
        $upload->rootPath  =     './Uploads/'; // 设置附件上传根目录
        $upload->savePath  =     ''; // 设置附件上传（子）目录
        // 上传文件 
        $info   =   $upload->upload();
        if(!$info) {// 上传错误提示错误信息
        		$return = ['status' => '0', 'info' => $upload->getError()];
            $this->ajaxReturn($return);
        }else{// 上传成功
        		$return = ['status' => '1', 'info' => '/Uploads/'.$info['pic']['savepath'].$info['pic']['savename']];
            $this->ajaxReturn($return);
        }
	}
}