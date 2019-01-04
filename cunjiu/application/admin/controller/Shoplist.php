<?php
namespace app\admin\controller;
use think\Controller;
use app\admin\model\Shop;
use app\admin\model\Admin;
class Shoplist extends Common{
	//店铺信息
	public function shopList(){
		$shopData=Shop::with(['shopadmin'=>function($query){
			$query->field('id,admin_name');
		}])->order('sort','asc')->paginate(10);
		$this->assign('shopData',$shopData);
		//数据页数
		$page=$shopData->render();
		$this->assign('page',$page);
		return $this->fetch();
	}
	//增加店铺信息
	public function shopAdd(){
		//查询管理员信息
		$adminData=Admin::all();
		$this->assign('adminData',$adminData);
		if(request()->isPost()){
			$data['shop_name']=input('param.shop_name');
			$data['adminid']=input('param.adminid');
			$data['sort']=input('param.sort');
			$selpower=$this->selpower();
			if($selpower==true){
				$workflag=1;
			}else if($selpower!=''){
				if(isset($selpower['add'])){
					$workflag=1;
				}else{
					$workflag=0;
				}
			}else{
				$workflag=0;
			}
			if($workflag==1){
				$shopM=new Shop();
				$addres=$shopM->save($data);
				if($addres){
					$res=[
					   'code'=>1,
					   'msg'=>'添加成功'
					];
				}else{
					$res=[
					   'code'=>0,
					   'msg'=>'添加失败'
					];
				}
			}else{
				$res=[
					   'code'=>0,
					   'msg'=>'sorry,您没有权限...'
					];
			}
			return json($res);
		}
		return $this->fetch();
	}
	//判断管理员是否有
}
?>