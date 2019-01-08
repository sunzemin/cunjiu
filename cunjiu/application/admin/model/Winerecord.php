<?php
namespace app\admin\model;
use think\Model;
class Winerecord extends Model{
	//查询店铺信息
	public function selshop(){
		return $this->hasMany('Shop','id','shop_id');
	}
	//查询分类信息
	public function selclassify(){
		return $this->hasMany('Wineclassify','id','classid');
	}
}
?>