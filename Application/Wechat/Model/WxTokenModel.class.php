<?php
namespace Wechat\Model;

class WxTokenModel extends \Think\Model{
    
    /**
     * 获取access_token
     */
    public function getToken($appid = '', $secret = ''){
        if(empty($appid) || empty($secret)){
            $this->error = '项目配置文件未设置appid或appsecret';
            return false;
        }
        
        $tokenRow = $this->where(array('id'=>1))->find();
        if(empty($tokenRow)) $this->add(array('id'=>1));//无记录则插入一条
        
        if($tokenRow && $tokenRow['expires_time'] > time()){//未过期
            return $tokenRow['access_token'];
        }else{
            $WxApi = D('WxApi');
            $token = $WxApi->getAccessToken($appid, $secret);
            if(empty($token)){
                $this->error = 'access_token获取失败';
                return false;
            }elseif($token['errcode'] > 0){
                $this->error = $WxApi->getErrText($token['errcode']);
                if(empty($this->error)) $this->error = $token['errmsg'];
                return false;
            }else{
                $this->save(array('id'=>1,'access_token'=>$token['access_token'],'expires_time'=>(time()+3600)));
                return $token['access_token'];
            }
        }
    }
    
}
