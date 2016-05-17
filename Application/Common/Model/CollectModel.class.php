<?php
namespace Common\Model;

class CollectModel extends CommonModel{
    
    //获取某个机构收藏的讲师id数组
    public function getTidsByiid($iid = 0){
        $idArr = array();
        $arr = $this->where(array('type'=>'1','uid'=>$iid))->select();
        foreach($arr as $v){
            $idArr[] = $v['teacher_id'];
        }
        return $idArr;
    }
    
    //获取某个机构收藏的讲师课程id数组
    public function getCidsByiid($iid = 0){
        $idArr = array();
        $arr = $this->where(array('type'=>'2','uid'=>$iid))->select();
        foreach($arr as $v){
            $idArr[] = $v['class_id'];
        }
        return $idArr;
    }



    //获取收藏某个讲师的所有机构
    public function getCollectIidRow($teacher_id = 0){
        $idArr = array();
        $arr = $this->where(array('type'=>'1','teacher_id'=>$teacher_id))->select();
        foreach($arr as $v){
            $idArr[] = $v['uid'];
        }
        return $idArr;
    }
    
}
