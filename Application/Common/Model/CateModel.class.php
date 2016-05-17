<?php
namespace Common\Model;

class CateModel extends CommonModel{
    
    protected $_validate = array(
        array('name','require','名称不能为空'),
        array('name','','该名称已经存在',0,'unique',1), //在新增的时候验证name字段是否唯一
        array('order_num','number','请填写序号数值'),
    );
    
    //获取行业
    public function getIndustry($page = ''){
        $row = array();
        $arr = $page ? $this->where(array('pid'=>0))->order('order_num asc')->page($page)->select() : $this->where(array('pid'=>0))->order('order_num asc')->select();
        foreach($arr as $v){
            $row[$v['id']] = $v['name'];
        }
        return $row;
    }
    
    /**
     * 获取领域名组
     * @param string $ids
     * @param array $cateRow 所有行业领域的一维数组
     * @return string
     */
    public function getFieldByIds($ids = '', $cateRow = array()){
        if(empty($ids)) return;
        $nameArr = array();
        if(empty($cateRow)){
            $res = $this->where(array('id'=>array('in',trim($ids,','))))->select();
            foreach($res as $v){
                $nameArr[] = $v['name'];
            }
        }else{
            $idArr = explode(',',trim($ids,','));
            foreach($idArr as $v){
                $nameArr[] = $cateRow[$v];
            }
        }
        return implode(',',$nameArr);
    }
    
    //获取所有行业领域
    public function getAllCate(){
        $row = array();
        $arr = $this->order('order_num asc')->select();
        foreach($arr as $v){
            $row[$v['id']] = $v['name'];
        }
        return $row;
    }

}