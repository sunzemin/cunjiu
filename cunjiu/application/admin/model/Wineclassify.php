<?php
namespace app\admin\model;
use think\Model;
class Wineclassify extends Model{
	//查新店铺信息
	public function selshopinfo(){
		return $this->hasMany('Shop','id','shop_id');
	}
}
?>