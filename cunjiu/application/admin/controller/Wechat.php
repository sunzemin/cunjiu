<?php
namespace app\admin\controller;
use think\Controller;
use app\admin\model\Weixin;
class Wechat extends Common{
	//酒品分类管理
	public function weixin(){
		$weixininfo=Weixin::find();
		$this->assign('weixininfo',$weixininfo);
		if(request()->isPost()){
		   $data['appid']=input('param.appid');
		   $data['appsecret']=input('param.appsecret');
		   $selres=Weixin::where('id',1)->find();
		   if($selres){
		   	  //数据存在进行修改
		   	  $workflag=$this->seladminpower(3);
		   	  if($workflag==1){
					$weixinM=new Weixin();
			   	    $saveres=$weixinM->where('id',1)->update($data);
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
		   }else{
		   	    $workflag=$this->seladminpower(1);
			   	  if($workflag==1){
						$weixinM=new Weixin();
				   	    $addres=$weixinM->save($data);
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
		   }
		   return json($res);
		}
		return $this->fetch();
	}
}
?>