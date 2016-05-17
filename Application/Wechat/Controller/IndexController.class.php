<?php

namespace Wechat\Controller;

/**
 * 微信
 */
class IndexController extends BaseController{
    function __construct() {
        parent::__construct();
        if (!$this->auth()) {
            header('Location: ' . U('common/login'));
        }
    }
    public function index() {
        $this->init();
        $this->display();
    }
    public function choose_w() {
        $list = M('WxConfig')->select();
        if (empty($list)) {
            header('Location: ' . U('add_wechat'));
        }
        $this->assign('list', $list);
        $this->display();
    }
    public function choose_w_do() {
        $id = I('get.id', '', 'intval');
        $info = M('WxConfig')->where(['id' => $id])->find();
        if (empty($info)) {
            $this->error('hack');
        }
        session('w_id', $id);
        header('Location: ' . U('index'));
    }
    public function add_wechat() {
        $this->display();
    }
    public function add_wechat_do() {
        $return = ['status' => 1, 'info' => 'error'];
        $data = I('post.');
        $resp = M('WxConfig')->add($data);
        if ($resp !== false) {
            $return['status'] = 0;
        } else {
            $return['info'] = M('WxConfig')->getError();
        }
        $this->ajaxReturn($return);
    }
    public function index_keyword(){
        $where['type'] = I('get.type');
        if(I('get.keywords')) $where['keyword'] = array('like','%'.I('get.keywords').'%');
        $dataPage = $this->getPager(M('WxCommon'),$where);
        $this->assign('list',$dataPage['data']);
        $this->assign('page',$dataPage['page']);
        $this->display();
    }
    
    public function save_keyword(){
        if(IS_POST){
            $postData = I('post.');
            $data = $postData['info'];
            //关键词过滤
            $data['keyword'] = trim(str_replace("，",",",$data['keyword']),',');
            $keywordArr = explode(',',$data['keyword']);
            $WxKeyword = M('WxKeyword');
            foreach($keywordArr as $v){
                $keywordRes = $WxKeyword->where(array('keyword'=>$v))->find();
                if(empty($data['id']) && $keywordRes){
                    $this->error("关键词 ".$v." 已存在，请更换");
                }elseif($data['id']){
                    if($kEditRes = $WxKeyword->where(array('common_id'=>array('neq',$data['id']),'keyword'=>$v))->find())
                        $this->error("关键词 ".$v." 已存在，请更换");
                }
            }
            if(empty($data['keyword'])) $this->error("请输入关键词");
            
            $res = $data['id'] ? M('WxCommon')->save($data) : M('WxCommon')->add($data);
            if($res === false){
                $this->error('保存失败');
            }else{
                //保存关键词
                $common_id = $data['id'] ? $data['id'] : $res;
                $typeStr = $data['type'] == '1' ? 'text' : 'single';
                $this->saveKeywords($WxKeyword, $common_id, $data['keyword'], $typeStr);
                $this->success('保存成功');
            }
        }else{
            $item = array();
            if(I('get.id')) $item = M('WxCommon')->find(I('get.id'));
            $this->assign('item',$item);
            $this->assign('upPageUrl',$_SERVER['HTTP_REFERER']);
            I('get.type')=='1' ? $this->display('save_text') : $this->display('save_single');
        }
    }
    
    public function commonDel($id){
        $id = (int)$id;
        if($res = M('WxCommon')->delete($id)){
            M('WxKeyword')->where(array('common_id'=>$id))->delete();
            $this->success('删除成功');
        }else{
            $this->error('删除失败');
        }
    }
    
    public function index_menu(){
        // echo "<pre>";
        // var_dump(C());exit;
        $list = M("WxMenu")->order("id desc")->select();
        foreach($list as $v){
            if($v['position'] == 1){
                $first[] = $v;
            }elseif($v['position'] == 2){
                $second[] = $v;
            }elseif($v['position'] == 3){
                $third[] = $v;
            }
        }
        $this->assign("first",$first);
        $this->assign("second",$second);
        $this->assign("third",$third);
        $this->display();
    }

    public function saveMenu(){
        $data = $_REQUEST;
        $menu = M('WxMenu')->where(array("position"=>I("position")))->find();
        if(empty($menu)){
            $data['pid'] = 0;
        }else{
            $data['pid'] = $menu['id'];
        }
        $res = I("id") ? M('WxMenu')->save($data) : M('WxMenu')->add($data);
        $res === false ? $this->error('保存失败') : $this->success('保存成功');
    }

    public function editMenu(){
        $item = M('WxMenu')->where(array("id"=>(int)I("id")))->find();
        $this->success($item);
    }

    /**
     * 删除菜单
     */
    public function deleteMenu(){
        $res = M('WxMenu')->delete((int)I("id"));
        $res == false ? $this->error('删除失败') : $this->success('删除成功');
    }

    /**
     * 生成菜单
     * @return string
     * "url" => "http://baixinxing.yunapply.com/wechat/",
     */
    public function createMenu() {
        $first = M('WxMenu')->where(array("position"=>1))->order("id desc")->select();
        if(count($first)>1){
            for($i=0;$i<count($first)-1;$i++){
                $keyName = $first[$i]['type'] == 'view' ? 'url' : 'key';
                $arr[] = array("type"=>$first[$i]['type'],"name"=>$first[$i]['title'],$keyName=>$first[$i]['url']);
            }
            $menu[0] = array("name"=>$first[count($first)-1]['title'],"sub_button"=>$arr);
        }elseif($first){
            $keyName = $first[0]['type'] == 'view' ? 'url' : 'key';
            $menu[0] = array("type"=>$first[0]['type'],"name"=>$first[0]['title'],$keyName=>$first[0]['url']);
        }

        $second = M('WxMenu')->where(array("position"=>2))->order("id desc")->select();
        if(count($second)>1){
            for($i=0;$i<count($second)-1;$i++){
                $keyName = $second[$i]['type'] == 'view' ? 'url' : 'key';
                $arr2[] = array("type"=>$second[$i]['type'],"name"=>$second[$i]['title'],$keyName=>$second[$i]['url']);
            }
            $menu[1] = array("name"=>$second[count($second)-1]['title'],"sub_button"=>$arr2);
        }elseif($second){
            $keyName = $second[0]['type'] == 'view' ? 'url' : 'key';
            $menu[1] = array("type"=>$second[0]['type'],"name"=>$second[0]['title'],$keyName=>$second[0]['url']);
        }

        $third = M('WxMenu')->where(array("position"=>3))->order("id desc")->select();
        if(count($third)>1){
            for($i=0;$i<count($third)-1;$i++){
                $keyName = $third[$i]['type'] == 'view' ? 'url' : 'key';
                $arr3[] = array("type"=>$third[$i]['type'],"name"=>$third[$i]['title'],$keyName=>$third[$i]['url']);
            }
            $menu[2] = array("name"=>$third[count($third)-1]['title'],"sub_button"=>$arr3);
        }elseif($third){
            $keyName = $third[0]['type'] == 'view' ? 'url' : 'key';
            $menu[2] = array("type"=>$third[0]['type'],"name"=>$third[0]['title'],$keyName=>$third[0]['url']);
        }
        $menu = array("button"=>$menu);
        
        //获取access_token
        $WxToken = D('WxToken');
        $access_token = $WxToken->getToken(C('APPID'),C('APPSECRET'));
        if(empty($access_token)) $this->error($WxToken->getError());
        //发布菜单
        if(empty($menu)) $this->error('未保存自定义菜单');
        $result = D('WxApi')->createMenu($access_token, $menu);
        if($result['errcode'] === 0){
            $this->success('发布成功');
        }else{
            $errText = D('WxApi')->getErrText($result['errcode']);
            if(empty($errText)) $errText = $result['errmsg'];
            $result['errcode'] > 0 ? $this->error($errText) : $this->error('发布失败');
        }
    }
    
    /**
     * 首次关注、无匹配回复
     */
    public function save_auto_reply(){
        if(IS_POST){
            $data = I('post.');
            $res = $data['info']['id'] ? M('WxAutoReply')->save($data['info']) : M('WxAutoReply')->add($data['info']);
            $res === false ? $this->error('保存失败') : $this->success('保存成功');
        }else{
            $this->assign('item', M('WxAutoReply')->where(array('type'=>I('get.type')))->find());
            $this->display();
        }
    }

    /**
     * url-token
     */
    public function url_token(){
        $this->assign('url','http://'.$_SERVER['HTTP_HOST'].U('Api/index'));
        $this->assign('token',C('TOKEN'));
        $this->display();
    }
    
    //粉丝列表
    public function member($content = ''){
        $where = array();
        //$where['nickname'] = $keywords ? array('like','%'.$keywords.'%') : array('neq','');
        if ($keywords) {
            $where['nickname'] = ['like','%'.$keywords.'%'];
        }
        $dataPage = $this->getPager(M('WxMember'),$where);
        $this->assign('list',$dataPage['data']);
        $this->assign('page',$dataPage['page']);
        $this->display();
    }

    public function message($content = ''){
        $where = array();
        $where['msg_content'] = $content ? array('like','%'.$content.'%') : array('neq','');
        $dataPage = $this->getPager(M('WxMessage'),$where);
        $this->assign('list',$dataPage['data']);
        $this->assign('page',$dataPage['page']);
        $this->display();
    }
    //文章列表
    public function article_list($content = ''){
        $where = array();
        $keywords=$_POST['keywords'];
        //$where['title'] = $keywords ? array('like','%'.$keywords.'%') : array('neq','');
        if ($keywords) {
            $where['title'] = array('like','%'.$keywords.'%');
        }
        $dataPage = $this->getPager(M('WxArticle'),$where);
        $this->assign('list',$dataPage['data']);
        $this->assign('page',$dataPage['page']);
        $this->display();
    }
    //文章分类列表
    public function article_sort_list($content = ''){
        $where = array();
        $keywords=$_POST['keywords'];
        //$where['title'] = $keywords ? array('like','%'.$keywords.'%') : array('neq','');
        if ($keywords) {
            $where['tag_name'] = array('like','%'.$keywords.'%');
        }
        $dataPage = $this->getPager(M('WxArtisort'),$where);
        $this->assign('list',$dataPage['data']);
        $this->assign('page',$dataPage['page']);
        $this->display();
    }
    //评论列表
    public function article_discuss_list(){
        $where = array();
        $dataPage = $this->getPager(M('WxArticle_discuss'),$where);
        $this->assign('list',$dataPage['data']);
        $this->assign('page',$dataPage['page']);
        $this->display();

    }
    //删除评论
    public function article_discuss_delete($content = ''){

        $del=M('WxArticle_discuss')->delete($_GET['id']);
        $del == false ? $this->error('删除失败') : $this->success('删除成功');

        // $res = M('WxArticle')->delete($_GET['id']);
        // $res == false ? $this->error('删除失败') : $this->success('删除成功');

    }
    //添加文章
    public function article_adds(){
        $Asort = M('WxArtisort')->select();
        $this->assign('sortlist',$Asort);
        $this->display();
    }
    public function article_adds_ok(){        
        if(IS_POST){
            $data = array(
                'title' => $_POST['title'],
                'content' => $_POST['content'],
                'add_time' => time(),
                'modify_time' => time(),
                'status'=>$_POST['arti_status'],
                'article_sort'=>$_POST['article_sort'],
                'author'=>session('usernamea')
                );
            //判断标题是否已存在
            $arti_name=(bool)(M('WxArticle')->where(array('title'=>$_POST['title']))->select());
            if($arti_name==false||$arti_name==""){
            //找出文章的标题，增加它的分类数量,存入
             $sortdata['tag_name']=$data['article_sort'];
             $sort=M('WxArtisort')->where($sortdata)->select();
             $sortnum=(int)($sort[0]['article_num']);
             $sort[0]['article_num']=(string)($sortnum+1);
             $arr = array(
                'Id' => $sort[0]['id'],
                'tag_name' => $sortdata['tag_name'],
                'article_num' => $sort[0]['article_num']
                );
             
             $fin=M('WxArtisort')->data($arr)->save();
            if(M('WxArticle')->data($data)->add()) {
                    $this -> success('发布成功!');
            }else{
                $this -> error('发布失败!请重试...');
                }
            }else{
                $this -> error('发布失败!文章标题已存在！');
            }
        }
    }
    //修改文章
    public function article_revise($content = ''){
        
        $keywords['Id']=$_GET['id'];
        //$where['title'] = $keywords ? array('like','%'.$keywords.'%') : array('neq','');
        $Arti = M('WxArticle')->where($keywords)->select();
        $this->assign('list',$Arti);
        //分类模型实例化
        $Asort = M('WxArtisort')->select();
        $this->assign('sortlist',$Asort);
        $this->display();
        
    }
    public function article_revise_ok($content = ''){
        if(IS_POST){
            $data = array(
                'Id' => $_POST['arti_id'],
                'title' => $_POST['title'],
                'content' => $_POST['content'],
                'status'=>$_POST['arti_status'],
                'modify_time' => time(),
                'article_sort'=>$_POST['article_sort'],
                'author'=>session('usernamea')
                );
            //判断标题是否已存在
            $arti_name=(bool)(M('WxArticle')->where(array('title'=>$_POST['title']))->select());
            if($arti_name==false||$arti_name==""){
               if(M('WxArticle')->data($data)->save()) {
                        $this -> success('更新成功!');
                        //var_dump($data['content']);
                }else{
                    $this -> error('更新失败!请重试...');
                }
            }else{
                $this -> error('发布失败!文章标题已存在！');
            }
        }
    }
    //删除文章
    public function article_delete($content = ''){
        $res = M('WxArticle')->delete($_GET['id']);
        $res == false ? $this->error('删除失败') : $this->success('删除成功');
    }
    //添加分类
    public function article_sort_adds($content = ''){
        $this->display();
    }
    public function article_sort_adds_ok($content = ''){
        if(IS_POST){
            $data = array(
                'tag_name' => $_POST['tag_name'],
                'add_time' => time(),
                'modify_time' => time(),
                'article_num'=> 0,
                'author'=>session('usernamea')
                );
            // dump($data);
            // die();
        $sort_name=(bool)(M('WxArtisort')->where(array('tag_name'=>$_POST['tag_name']))->select());
            if($sort_name==false||$sort_name==""){
                if(M('WxArtisort')->data($data)->add()) {
                        $this -> success('添加成功!');
                }else{
                    $this -> error('添加失败!请重试...');
                }
            }else{
                 $this -> error('添加失败!类名已存在！请重试...');
            }
        }
    }
    //修改分类名
    public function article_sort_revise($content = ''){
        $keywords['Id']=$_GET['id'];
        $Arti = M('WxArtisort')->where($keywords)->select();
        $this->assign('list',$Arti);
        $this->display();
    }
    public function article_sort_revise_ok($content = ''){
    if(IS_POST){
            $data = array(
                'Id' => $_POST['sort_id'],
                'tag_name' => $_POST['tag_name'],
                'modify_time' => time(),
                'author'=>session('usernamea')
                );
            $sort_name=(bool)(M('WxArtisort')->where(array('tag_name'=>$_POST['tag_name']))->select());
            if($sort_name==false||$sort_name==""){
               if(M('WxArtisort')->data($data)->save()) {
                    $this -> success('更新成功!');
                }else{
                    $this -> error('更新失败!请重试...');
                }
            }else{
                 $this -> error('添加失败!类名已存在！请重试...');
            }
        }
    }
    //删除分类名
    public function article_sort_delete($content = ''){
        $kee['Id']=$_GET['id'];
        $data=M('WxArtisort')->where($kee)->select();
        //echo $data[0]['article_num'];
        if($data[0]['article_num']==''||$data[0]['article_num']==0){
        $res = M('WxArtisort')->where($kee)->delete($_GET['id']);
        $res == false ? $this->error('删除失败!') : $this->success('删除成功!');
        }else{
            $this->error('删除失败,请先清空该类内数据！');
        }
    }
    //分类查看文章列表
    public function article_sortlist_cont($content = ''){
        $where = array();
        $where['Id']=$_GET['id'];
        $data=M('WxArtisort')->where($where)->select();

        $keywords=$_POST['keywords'];
        if ($keywords) {
            $arti['title'] = array('like','%'.$keywords.'%');
            // echo $arti; 
        }
        $arti['article_sort']=$data[0]['tag_name'];
        $dataPage = $this->getPager(M('WxArticle'),$arti);
        // $aa=M('WxArticle')->getlastSql();
        $this->assign('list',$dataPage['data']);
        $this->assign('page',$dataPage['page']);
        $this->display();
    }
    /**
     * 数据列表及分页
     * @param mixed $where 查询条件
     * @param int $pageSize 页大小
     * @param string $order 排序
     * @return array array('data'=>数据列表,'page'=>分页)
     */
    private function getPager($model,$where=array(),$pageSize=10,$order='id desc'){
        I('get.p') ? $p=I('get.p') : $p=1;
        $list = $model->where($where)->order($order)->page($p.','.$pageSize)->select();
        $count = $model->where($where)->count();
        $Page = new \Think\Page($count,$pageSize);
        $show = $Page->show();
        return array(
            'data' => $list,
            'page' => $show,
        );
    }
    
    private function saveKeywords($model, $common_id, $keyword, $type = 'text'){
        $model->where(array('common_id'=>$common_id,'type'=>$type))->delete();
        $wordArr = explode(',',$keyword);
        foreach($wordArr as $word){
            $model->add(array('common_id'=>$common_id,'keyword'=>trim($word),'type'=>$type));
        }
    }
    
}
