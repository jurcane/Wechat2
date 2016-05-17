<?php
namespace Wechat\Controller;

use EasyWeChat\Foundation\Application;
use EasyWeChat\Message\Text;
// use EasyWeChat\Message\Image;
// use EasyWeChat\Message\Video;
// use EasyWeChat\Message\Voice;
use EasyWeChat\Message\News;

class ApiController extends \Think\Controller{
    public $easychatSdk;
    public $message;
    function __construct() {
        parent::__construct();
        $options = C('options');
        $this->easychatSdk = new Application($options);
        $this->user = $this->easychatSdk->user;
    }
    public function index(){
        $user = $this->user;
        $this->easychatSdk->server->setMessageHandler(function($message) use ($user) {
            $this->message = $message;
            $this->_beforeHandler();
            switch ($message->MsgType) {
                case 'event':
                    return $this->EventHandler();
                case 'text':
                    return $this->TextHandler();
                case 'location':
                    return $this->LocalHandler();
                default:
                    return $this->DefaultHandler();
            }
        });
        $this->easychatSdk->server->serve()->send();
    }
    public function EventHandler() {
        switch ($this->message->Event) {
            case 'subscribe':
                return $this->SubscribeHandler();
                break;
            case 'unsubscribe':
                return $this->UnsubscribeHandler();
                break;
            default:
                return $this->DefaultEventHandler();
                break;
        }
    }
    private function UnsubscribeHandler() {
        $memberRow =  M('WxMember')->where(["fromuser" => $this->message->FromUserName])->find();
        $rowMem['unsubscribe_time'] = time();
        $rowMem['status'] = '0';
        $rowMem['fromuser']    = $this->message->FromUserName;
        $rowMem['timesign']    = $this->message->timesign;
        $userInfo = $this->user->get($this->message->FromUserName);
        if (!isset($userInfo['errcode'])) {
            $rowMem['group_id'] = $userInfo['groupid'];
            $rowMem['nickname'] = $userInfo['nickname'];
            $rowMem['headimgurl'] = $userInfo['headimgurl'];
            $rowMem['sex'] = $userInfo['sex'];
            $rowMem['language'] = $userInfo['language'];
            $rowMem['country'] = $userInfo['country'];
            $rowMem['province'] = $userInfo['province'];
            $rowMem['city'] = $userInfo['city'];
        }
        empty($memberRow) ? $Member->add($rowMem) : $Member->where(array('id'=>$memberRow['id']))->save($rowMem);
    }
    private function SubscribeHandler() {
        //可以做cache
        $memberRow =  M('WxMember')->where(["fromuser" => $this->message->FromUserName])->find();
        $rowMem = [];
        $rowMem['subscribe_time'] = time();
        $rowMem['status'] = '1';
        $rowMem['fromuser']    = $this->message->FromUserName;
        $rowMem['timesign']    = $this->message->timesign;
        try {
            $userInfo = $this->user->get($this->message->FromUserName);
        } catch (Exception $e) {
            $userInfo = [];
        }
        if ( !empty($userInfo) ) {
            $rowMem['group_id'] = $userInfo['groupid'];
            $rowMem['nickname'] = $userInfo['nickname'];
            $rowMem['headimgurl'] = $userInfo['headimgurl'];
            $rowMem['sex'] = $userInfo['sex'];
            $rowMem['language'] = $userInfo['language'];
            $rowMem['country'] = $userInfo['country'];
            $rowMem['province'] = $userInfo['province'];
            $rowMem['city'] = $userInfo['city'];
        }
        empty($memberRow) ? M('WxMember')->add($rowMem) : M('WxMember')->where(['id'=>$memberRow['id']])->save($rowMem);
        $replyRow = M("WxAutoReply")->where(array('type'=>'1','state'=>'1'))->find();
        $return = new Text();
        if (!empty($replyRow)) {
            $return->content = $replyRow['content'];
        } else {
            $return->content = '谢谢关注';
        }
        return $return;
    }
    private function DefaultEventHandler() {

    }
    private function LocalHandler() {

    }
    private function DefaultHandler() {
        $return = new Text();
        if($replyRow = M("WxAutoReply")->where(array('type' => '2','state' => '1'))->find()){
            $return->content = $replyRow['content'];
        } else {
            $return->content = '';
        }
        return $return;
    }
    //获取回复内容
    private function TextHandler() {
        $keywordinfo = M('WxKeyword')->where(array('keyword' => $this->message->Content))->find();
        if( empty($keywordinfo) ){
            //无匹配回复
            $return = new Text();
            $replyRow = M("WxAutoReply")->where(array('type' => '2','state' => '1'))->find();
            if( $replyRow ){
                $return->content = $replyRow['content'];
            } else {
                $return->content = '';
            }
        } else {
            if($keywordinfo['type'] == 'text'){
                $return = $this->getRespText($keywordinfo['common_id']);
            }elseif($keywordinfo['type'] == 'single'){
                $return = $this->getRespSingle($keywordinfo['common_id']);
            }
        }
        return $return;
    }
    //TODO hook到客服消息
    private function _beforeHandler() {
        //存储记录
        $row = [];
        $row['fromuser']    = $this->message->FromUserName;
        $row['touser']      = $this->message->ToUserName;
        $row['msgtype']     = $this->message->MsgType;
        if ( isset($this->message->Content) ) {
            $row['msg_content'] = $this->message->Content;
        }
        $row['create_time'] = $this->message->CreateTime;
        M('WxMessage')->add($row);
    }

    //文字回复
    public function getRespText($id){
        $WxCommon = M('WxCommon');
        $inforesult = $WxCommon->where(array('id'=>$id,'state'=>'1'))->find();
        $WxCommon->where(array('id'=>$id))->setInc('push_num');
        $return = new Text();
        $return->content = $inforesult['content'];
        return $return;
    }

    //单图文回复
    public function getRespSingle($id){
        $WxCommon = M('WxCommon');
        $inforesult = $WxCommon->where(array('id'=>$id,'state'=>'1'))->find();
        $WxCommon->where(array('id'=>$id))->setInc('push_num');//推送量+1
        
        $return = new News([
            'title'       => $inforesult['title'],
            'description' => $inforesult['des'] ? $inforesult['des'] : cut_str($inforesult['info_intro'], 80),
            'url'         => $inforesult['url'],
            //'image'       => $image,
            'info_intro'  => htmlspecialchars_decode($inforesult['content']),

        ]);
        return $return;
    }
}
