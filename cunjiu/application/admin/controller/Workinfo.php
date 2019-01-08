<?php
namespace app\admin\controller;
use think\Controller;
use app\admin\model\Worker;
use app\admin\model\Shop;
class WorkInfo extends Common{
	//店铺信息
	public function workerlist(){
		$workerData=Worker::with(['selshop'=>function($query){
			$query->field('id,shop_name');
		}])->order('sort','asc')->paginate(10);
		$this->assign('workerData',$workerData);
		//数据页数
		$page=$workerData->render();
		$this->assign('page',$page);
		return $this->fetch();
	}
	//增加店铺信息
	public function workerAdd(){
		//查询管理员信息
		$shopData=Shop::all();
		$this->assign('shopData',$shopData);
		if(request()->isPost()){
			$data['worker']=input('param.worker');
			$data['phone']=input('param.phone');
			$data['shop_id']=input('param.shop_id');
			$data['sort']=input('param.sort');
			$workflag=$this->seladminpower(1);
			if($workflag==1){
				$workerM=new Worker();
				$addres=$workerM->save($data);
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
	//修改店铺信息
	public function workerUpdate(){
		//查询管理员信息
		$shopData=Shop::all();
		$this->assign('shopData',$shopData);
		//查询需要修改的店铺信息
		$workid=input('param.id');
		$workerInfo=Worker::with(['selshop'=>function($query){
			$query->field('id,shop_name');
		}])->where('id',$workid)->find();
		$this->assign('workerInfo',$workerInfo);
		//dump($workerInfo);
		if(request()->isPost()){
			$id=input('param.id');
			$data['worker']=input('param.worker');
			$data['phone']=input('param.phone');
			$data['shop_id']=input('param.shop_id');
			$data['sort']=input('param.sort');
			$workflag=$this->seladminpower(3);
			if($workflag==1){
				$workerM=new Worker();
				$saveres=$workerM->where('id',$id)->update($data);
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
	//删除店铺信息
	public function workerDel(){
		$workerid=input('param.workerid');
		$workflag=$this->seladminpower(2);
			if($workflag==1){
				$delres=Worker::where('id',$workerid)->delete();
				if($delres){
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
}
?>