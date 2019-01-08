<?php
namespace app\admin\controller;
use think\Controller;
use app\admin\model\Wineclassify;
use app\admin\model\Shop;
class Wine extends Common{
	//酒品分类管理
	public function wineclassify(){
		$wineClassifyList=Wineclassify::order('sort','asc')->paginate(10);
		$this->assign('wineClassifyList',$wineClassifyList);
		//数据页数
		$page=$wineClassifyList->render();
		$this->assign('page',$page);
		return $this->fetch();
	}
	//添加酒品分类信息
	public function wineClassifyAdd(){
		$shopList=Shop::all();
		$this->assign('shopList',$shopList);
		if(request()->isPost()){
			$classname=input('param.classname');
			$data['shop_id']=input('param.shop_id');
			$data['classname']=input('param.classname');
			$data['sort']=input('param.sort');
			$workflag=Wine::seladminpower(1);
			if($workflag==1){
				
				$WineclassifyM=new Wineclassify();
				//查询是否有该分类
				$wineClassifyInfo=$WineclassifyM->where('classname',$classname)->find();
				if($wineClassifyInfo){
					$res=[
					   'code'=>0,
					   'msg'=>'分类名称重复...'
					];
					return json($res);
				}else{
					$addres=$WineclassifyM->save($data);
				}
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
	//修改酒品分类信息
	public function wineClassifyUpdate(){
		$shopList=Shop::all();
		$this->assign('shopList',$shopList);
		//查询修改的酒品分类信息
		$id=input('param.id');
		$wineClassifyInfo=Wineclassify::with(['selshopinfo'=>function($query){
			$query->field('id,shop_name');
		}])->where('id',$id)->find();
		$this->assign('wineClassifyInfo',$wineClassifyInfo);
		if(request()->isPost()){
			$classifyid=input('param.id');
			$data['shop_id']=input('param.shop_id');
			$data['classname']=input('param.classname');
			$data['sort']=input('param.sort');
			$workflag=Wine::seladminpower(3);
			if($workflag==1){
				$WineclassifyM=new Wineclassify();
				$saveres=$WineclassifyM->where('id',$id)->update($data);
				if($saveres){
					$res=[
					   'code'=>1,
					   'msg'=>'修改成功'
					];
				}else{
					$res=[
					   'code'=>0,
					   'msg'=>'修改失败'
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
	//删除酒品分类信息
	public function wineClassifydel(){
		$workflag=Wine::seladminpower(2);
		if($workflag==1){
			$id=input('param.id');
			$WineclassifyM=new Wineclassify();
			$addres=$WineclassifyM->where('id',$id)->delete();
			if($addres){
				$res=[
				   'code'=>1,
				   'msg'=>'删除成功'
				];
			}else{
				$res=[
				   'code'=>0,
				   'msg'=>'删除失败'
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
	//判断管理员是否有
	public function seladminpower($status){
		$selpower=$this->selpower();
		if($selpower==true){
			$workflag=1;
		}else if($selpower!=''){
			if($status==1){
				$varbile='add';
			}else if($status==2){
				$varbile='del';
			}else if($status==3){
				$varbile='update';
			}
			if(isset($selpower[$varbile])){
				$workflag=1;
			}else{
				$workflag=0;
			}
		}else{
			$workflag=0;
		}
		return $workflag;
	}
}
?>