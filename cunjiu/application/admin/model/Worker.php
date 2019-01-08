<?php
namespace app\admin\model;
use think\Model;
class Worker extends Model{
	//查询店铺信息
	public function selshop(){
		return $this->hasMany('Shop','id','shop_id');
	}
}
?>