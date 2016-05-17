<?php
namespace Common\Model;

class MessageModel extends CommonModel{

	//发送课程消息
	public function sendCourseMessage($data){
		$iidRow = D('Collect')->getCollectIidRow($data['teacher_id']);//获取收藏该讲师的所有机构
		if(empty($iidRow)) return;
		foreach($iidRow as $v){
			$content = "发布了新课程 ".$data['name'];
			$this->add(array('type'=>2,'sid'=>$data['teacher_id'],'uid'=>$v,'content'=>$content));
		}
	}

	//发送投标消息
	public function sendToubiaoMessage($teacher_id,$institution_id,$require_id){
		$require_title = M('Require')->where(array('id'=>$require_id))->getField('title');
		$content = "投标了您的需求 ".$require_title;
		$this->add(array('type'=>3,'sid'=>$teacher_id,'uid'=>$institution_id,'content'=>$content));
	}

	//发送邀标消息
	public function sendYaobiaoMessage($institution_id,$teacher_id,$require_id){
		$require_title = M('Require')->where(array('id'=>$require_id))->getField('title');
		$content = "的需求 ".$require_title.' 邀请您投标';
		$this->add(array('type'=>4,'sid'=>$institution_id,'uid'=>$teacher_id,'content'=>$content));
	}

	//发送邀标消息--讲师对邀标的回复
	public function sendYaobiaoResponse($bidRow,$status){
		$require_title = M('Require')->where(array('id'=>$bidRow['require_id']))->getField('title');
		$statusName = $status == 2 ? '同意' : '拒绝';
		$content = $statusName."了您的需求 ".$require_title.' 的邀标';
		$this->add(array('type'=>4,'sid'=>$bidRow['teacher_id'],'uid'=>$bidRow['institution_id'],'content'=>$content));
	}

	//发送系统消息--中标
	public function sendSetBid($bid){
		$bidRow = M('Bid')->find($bid);
		$require_title = M('Require')->where(array('id'=>$bidRow['require_id']))->getField('title');
		$content = "您中标了 ".$require_title;
		$this->add(array('type'=>1,'sid'=>0,'uid'=>$bidRow['teacher_id'],'content'=>$content));
	}

}