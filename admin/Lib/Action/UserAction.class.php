<?php
// +----------------------------------------------------------------------
// | JieQiangCms 移动应用软件后台管理系统
// +----------------------------------------------------------------------
// | provide by ：jieqiang.com
// 
// +----------------------------------------------------------------------
// | Author: 1569501393@qq.com
// +----------------------------------------------------------------------
// 后台会员管理
class UserAction extends BaseAction{
	// 附件列表
	public function apply(){
		// var_dump($_SESSION);
        // $mod=D("user"); 
        $mod=D("File"); 
        $userMod=D("User"); 
        $pagesize=10;        
        import("ORG.Util.Page");
		$where=" 1=1 ";
		if(isset($_REQUEST['keyword'])){
			$keys = $_REQUEST['keyword'];
			$this->assign('keyword',$keys);
			$where.=" and fName like '%$keys%'";
		}
		$count=$mod->where($where)->count();		
		$p = new Page($count,$pagesize);		
		// $list=$mod->where($where)->order("last_time desc")->limit($p->firstRow.','.$p->listRows)->select();
		// $list=$mod->where($where)->order("fId desc")->limit($p->firstRow.','.$p->listRows)->select();
		
		$list=$mod->where($where)->order("fId desc")->limit($p->firstRow.','.$p->listRows)->select();
		$key = 1;
		foreach($list as $k=>$val){
			$list[$k]['key'] = ++$p->firstRow;
			$list[$k]['name'] = $userMod->field('name')->where('id='.$val['fUid'])->find();
			$list[$k]['vAdminer'] = D('Admin')->field('user_name')->where('id in ('.$val['vAdminer'].')')->select();
			// var_dump($list[$k]['vAdminer']);
			foreach ($list[$k]['vAdminer'] as $v){
				$list[$k]['vAdminers'] .= $v['user_name']."  ";
				// $list[$k]['vAdminers'][] = $v['user_name']." ";
			}
			// $adminers = implode(',', $list[$k]['vAdminers']);
			// var_dump($list[$k]['vAdminers']);
		}

		$page=$p->show();  
//		var_dump($list);
		$this->assign('list',$list);
		$this->assign('page',$page);
		$this->display();
	}

	// 附件编辑
	public function applyEdit(){
        if (isset($_POST['dosubmit'])) {
			$mod = D('File');		
			// $user_data = $mod->create();			
			// 审核状态
			$user_data['fStatus']=$_REQUEST['status'];
			if ($_REQUEST['status'] == 1) {
				// 单元被占领 0
				$user_data['eState']= 0;
				$result_info=D('Element')->where("eId=". $_POST['eId'])->save($user_data);
			}
			// 审核时间
			$user_data['vTime']=time();
			// 审核员
			$user_data['vAdmin']=$_SESSION['admin_info']['user_name'];
			// $result_info=$mod->where("fId=". $user_data['id'])->save($user_data);
			$result_info=$mod->where("fId=". $_POST['id'])->save($user_data);
			if(false !== $result_info){
				$this->success(L('operation_success'), '', '', 'edit');
			}else{				
				$this->success(L('operation_failure'));
			}
		} else {
			$mod = D('File');		
			if (isset($_GET['id'])) {
				$id = isset($_GET['id']) && intval($_GET['id']) ? intval($_GET['id']) : $this->error('请选择要编辑的链接');
			}
			$user = $mod->where('fId='. $id)->find();	
			// var_dump($user);	
			$this->assign('info', $user);
			$this->assign('show_header', false);
			$this->display();
		}
	}


	// 附件删除
	public function applyDelete(){
        $file_mod = D('File');
		if(!isset($_POST['id']) || empty($_POST['id'])) {
            $this->error('请选择要删除的数据！');
		}	
		if( isset($_POST['id'])&&is_array($_POST['id']) ){			
			foreach( $_POST['id'] as $val ){
				$file_mod->delete($val);				
			}			
		}else{
			$id = intval($_POST['id']);			
		    $file_mod->where('id='.$id)->delete();	
		}
		$this->success(L('operation_success'));
	}

	public function index(){
        $mod=D("user"); 
        $pagesize=10;        
        import("ORG.Util.Page");
		$where=" 1=1 ";
		if(isset($_REQUEST['keyword'])){
			$keys = $_REQUEST['keyword'];
			$this->assign('keyword',$keys);
			$where.=" and name like '%$keys%'";
		}
		$count=$mod->where($where)->count();		
		$p = new Page($count,$pagesize);		
		// $list=$mod->where($where)->order("last_time desc")->limit($p->firstRow.','.$p->listRows)->select();
		$list=$mod->where($where)->order("id desc")->limit($p->firstRow.','.$p->listRows)->select();
		$page=$p->show();  
		$this->assign('list',$list);
		$this->assign('page',$page);
		$this->display();
	}
	function edit(){
		if (isset($_POST['dosubmit'])) {
			$mod = D('user');		
			$user_data = $mod->create();			
			$pass=trim($_REQUEST['password']);
			
			if(!empty($pass)){
				$user_data['passwd']=md5(trim($_REQUEST['password']));
			}
			$result_info=$mod->where("id=". $user_data['id'])->save($user_data);
			if(false !== $result_info){
				$this->success(L('operation_success'), '', '', 'edit');
			}else{				
				$this->success(L('operation_failure'));
			}
		} else {
			$mod = D('user');		
			if (isset($_GET['id'])) {
				$id = isset($_GET['id']) && intval($_GET['id']) ? intval($_GET['id']) : $this->error('请选择要编辑的链接');
			}
			$user = $mod->where('id='. $id)->find();		
			$this->assign('info', $user);
			$this->assign('show_header', false);
			$this->display();
		}
	}
	//暂时注释掉
//	public function setscore(){
//		$setting_mod = M('setting');
//		if (isset($_POST['dosubmit'])) {
//			$setscore['user_register_score'] = isset($_POST['user_register_score']) && trim($_POST['user_register_score']) ? trim($_POST['user_register_score']) : $this->error('注册积分填写错误');
//			$setscore['user_login_score'] = isset($_POST['user_login_score']) && trim($_POST['user_login_score']) ? trim($_POST['user_login_score']) : $this->error('登陆积分填写错误');
//			$setscore['share_goods_score'] = isset($_POST['share_goods_score']) && trim($_POST['share_goods_score']) ? trim($_POST['share_goods_score']) : $this->error('分享商品积分填写错误');					
//			foreach( $setscore as $key=>$val ){				
//				$setting_mod->where("name='$key'")->save(array('data'=>$val));				
//			}			
//			$this->success('修改成功', U('user/setscore'));
//		}
//		$res = $setting_mod->where("name='user_register_score' OR name='user_login_score' OR name='share_goods_score' OR name='delete_share_goods_score'")->select();
//		foreach( $res as $val )
//		{
//			$setscore[$val['name']] = $val['data'];
//		}
//		$this->assign('setscore',$setscore);
//		$this->display();
//	}
	public function delete()
    {
		$user_mod = D('user');
		$user_platform=D('user_platform');
		
		if(!isset($_POST['id']) || empty($_POST['id'])) {
            $this->error('请选择要删除的数据！');
		}	
		if( isset($_POST['id'])&&is_array($_POST['id']) ){			
			foreach( $_POST['id'] as $val ){
				$user_mod->delete($val);
				$user_platform->where("user_id='{$val}'")->delete();					
			}			
		}else{
			$id = intval($_POST['id']);			
		    $user_mod->where('id='.$id)->delete();	
		    $user_platform->where("user_id='{$id}'")->delete();	
		}
		$this->success(L('operation_success'));
    }

    //修改状态
	function status()
	{
		$article_mod = D('File');
		$id 	= intval($_REQUEST['id']);
		$type 	= trim($_REQUEST['type']);
		$sql 	= "update ".C('DB_PREFIX')."file set fStatus=1 where id='$id'";
		$res 	= $article_mod->execute($sql);
		$values = $article_mod->field("id,".$type)->where('id='.$id)->find();
		$this->ajaxReturn($values[$type]);
	}
}