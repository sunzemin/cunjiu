<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/29
 * Time: 15:28
 */

namespace app\admin\controller;
use app\admin\model\Admin;
use think\facade\Session;

use think\Controller;

class Login extends Controller
{
    //管理员登录
    /**
     * @return mixed|\think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function login(){
       if(request()->isPost()){
           $admin_name=input('param.admin_name');
           $admin_pwd=input('param.admin_pwd');
           $adminInfo=Admin::where(['admin_name'=>$admin_name,'admin_pwd'=>$admin_pwd])->find();
           if($adminInfo){
               session('adminid',$adminInfo['id']);
               session('admin_name',$adminInfo['admin_name']);
               $res=[
                   'code'=>1,
                   'msg'=>'登录成功'
               ];
               return json($res);
           }else{
               $res=[
                   'code'=>0,
                   'msg'=>'登录失败'
               ];
               return json($res);
           }
       };
       return $this->fetch();
   }
   //退出
    public function loginout(){
         session('adminid',null);
         session('admin_name',null);
         $this->redirect('/index.php/admin/Login/login');
    }
}