<?php
namespace app\admin\controller;
use think\Controller;
use app\admin\model\Winerecord;
use app\admin\model\Shop;
use app\admin\model\Wineclassify;
class Record extends Common{
	//查询记录
	public function winerecord(){
		$winerecordData=Winerecord::with(['selshop'=>function($query){
			$query->field('id,shop_name');
		},'selclassify'=>function($query){
			$query->field('id,classname');
		}])->order('sort','asc')->paginate(10);
		$this->assign('winerecordData',$winerecordData);
		//dump($winerecordData);
		//数据页数
		$page=$winerecordData->render();
		$this->assign('page',$page);
		return $this->fetch();
	}
	//修改订单信息
	public function winerecordUpdate(){
		//查询需要修改的信息
		$id=input('param.id');
		$winerecordInfo=Winerecord::where('id',$id)->find();
		$this->assign('winerecordInfo',$winerecordInfo);
		//查询店铺信息
		$shopList=Shop::all();
		$this->assign('shopList',$shopList);
		//查询该店铺的分类信息
		$wineclassifyInfo=Wineclassify::where('shop_id',$winerecordInfo['shop_id'])->select();
		$this->assign('wineclassifyInfo',$wineclassifyInfo);
		if(request()->isPost()){
			$recordid=input('param.winerecordid');
			$data['classid']=input('param.classid');
			$data['putpeople']=input('param.putpeople');
			$data['putworker']=input('param.putworker');
			$data['shop_id']=input('param.shop_id');
			$data['takepeople']=input('param.takepeople');
			$data['takeworker']=input('param.takeworker');
			$data['winename']=input('param.winename');
			$data['sort']=input('param.sort');
			$workflag=$this->seladminpower(3);
			if($workflag==1){
				$saveres=Winerecord::where('id',$recordid)->update($data);
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
	//删除订单信息
	public function winerecordDel(){
		$recordid=input('param.recordid');
		$workflag=$this->seladminpower(2);
		if($workflag==1){
			$saveres=Winerecord::where('id',$recordid)->delete();
			if($saveres){
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