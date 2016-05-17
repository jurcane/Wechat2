<?php

namespace Wechat\Controller;

class BaseController extends \Think\Controller{
	protected $w_id = 1;
	protected $user = [];
	protected function init() {
		$wid = session('w_id');
		if (empty($wid)) {
			header('Location: ' . U('index/choose_w'));
		}
		$this->w_id = $wid;
	}
	protected function auth() {
		$is_login = session('is_login');
		if ($is_login) {
			$this->user = session('user');
		}
		return isset($is_login) ? $is_login : FALSE;
	}
}