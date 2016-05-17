<?php
namespace Common\Model;

class CityModel extends \Think\Model{
    
    //获取城市
    public function getCityRow(){
        $arr = $this->order('id asc')->select();
        foreach($arr as $v){
            $row[$v['id']] = $v['name'];
        }
        return $row;
    }

}