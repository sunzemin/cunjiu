<?php
namespace app\admin\model;
use think\Model;

class Shop extends Model
{
   //查询管理员信息
   public function shopadmin(){
   	   return $this->hasOne('Admin','id','adminid');
   }
}
?>