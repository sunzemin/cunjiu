<?php
namespace app\admin\controller;
use think\Controller;
use app\admin\model\Shop;
use app\admin\model\Admin;
use app\admin\model\Shopinfo;
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
			$workflag=Shoplist::seladminpower(1);
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
	//修改店铺信息
	public function shopUpdate(){
		//管理员信息
		$adminData=Admin::all();
		$this->assign('adminData',$adminData);
		//查询需要修改的店铺信息
		$shopid=input('param.id');
		$shopInfo=Shop::with(['shopadmin'=>function($query){
			$query->field('id,admin_name');
		}])->where('id',$shopid)->find();
		$this->assign('shopInfo',$shopInfo);
		if(request()->isPost()){
			$shopid=input('param.shopid');
			$data['shop_name']=input('param.shop_name');
			$data['adminid']=input('param.adminid');
			$data['sort']=input('param.sort');
			$workflag=Shoplist::seladminpower(3);
			if($workflag==1){
				$shopM=new Shop();
				$saveres=$shopM->where('id',$shopid)->update($data);
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
	public function shopDel(){
		$shopid=input('param.shopid');
		$workflag=Shoplist::seladminpower(2);
			if($workflag==1){
				$delres=Shop::where('id',$shopid)->delete();
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
	//查询店铺详情信息
	public function shopInfoData(){
		//订单详情数据
		$shopInfoData=Shopinfo::with(['selshop'=>function($query){
			$query->field('id,shop_name');
		}])->order('sort','asc')->paginate(10);
		$this->assign('shopInfoData',$shopInfoData);
		//dump($shopInfoData[0]['selshop']);
		//数据页数
		$page=$shopInfoData->render();
		$this->assign('page',$page);
		return $this->fetch();
		
	}
	//添加店铺详情信息
	public function shopInfoAdd(){
		//查询 所有的店铺信息
		$shopData=Shop::all();
		$this->assign('shopData',$shopData);
		if(request()->isPost()){
			$start_time=input('param.start_time');
			$end_time=input('param.over_time');
			$startTime=$this->insertTime($start_time);
			$overTime=$this->insertTime($end_time);
			$data['start_time']=$startTime;
			$data['over_time']=$overTime;
			$data['address']=input('param.address');
			$data['logo']=input('param.imagepath');
			$data['introduce']=input('param.introduce');
			$data['latitude']=input('param.latitude');
			$data['longitude']=input('param.longitude');
			$data['phone']=input('param.phone');
			$data['shopid']=input('param.shopid');
			$data['sort']=input('param.sort');
			//return json($data);
			$shopinfoid=input('param.shopinfoid');
			if($shopinfoid==''){
				//说明没有此店详情进行添加操作
				$shopInfoM=new Shopinfo();
				$addres=$shopInfoM->save($data);
			}else{
				//说明此数据存在进行修改
				$addres=Shopinfo::where('id',$shopinfoid)->update($data);	
			}
			if($addres){
				$res=[
				    'code'=>1,
				    'msg'=>'保存成功'
				];
			}else{
				$res=[
				    'code'=>0,
				    'msg'=>'保存失败'   
				];
			}
			return json($res);
		}
		return $this->fetch();
	}
	//查询该店铺的详情
	public function selectShopInfo(){
		
		$shopid=input('post.shopid');
		$shopInfo=Shopinfo::where('shopid',$shopid)->find();
        $shopInfo['start_time']=date('H:i:s',$shopInfo['start_time']);
        $shopInfo['over_time']=date('H:i:s',$shopInfo['over_time']);
		return $shopInfo;
	}
	public function shop_add(){
		return $this->fetch();
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