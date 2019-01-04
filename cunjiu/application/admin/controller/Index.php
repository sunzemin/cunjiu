<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/29
 * Time: 14:34
 */

namespace app\admin\controller;
use think\facade\Session;
use think\Controller;

class Index extends Controller
{
    public function index(){
        $admin_name=session('admin_name');
        $this->assign('admin_name',$admin_name);
        if($admin_name==NULL){
            $this->redirect('/public/index.php/admin/Login/login');
        };
        return $this->fetch();
    }
}