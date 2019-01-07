<?php
namespace app\admin\controller;
use think\Controller;
use app\admin\model\Adminrole;
use app\admin\model\Role;
use app\admin\model\Rolepower;
use app\admin\model\Power;
class Common extends Controller{
	//查询管理员信息
	public function seladmin(){
		$adminid=session('adminid');
		//查询该角色
		$adminRole=Adminrole::where('adminid',$adminid)->select();
		for($i=0;$i<count($adminRole);$i++){
			$roleInfo=Role::where('id',$adminRole[$i]['roleid'])->find();
			if($roleInfo['role']=='总管理员'){
				return true;
			}
		}
		return false;
	}
	//查询该用户拥有的权限
	public function selpower(){
		$adminid=session('adminid');
		//查询该角色
		$adminRole=Adminrole::where('adminid',$adminid)->select();
		$flag=array();
		for($i=0;$i<count($adminRole);$i++){
			$roleInfo=Role::where('id',$adminRole[$i]['roleid'])->find();
			if($roleInfo['role']=='总管理员'){
				return true;
			}
			//查询该角色拥有的权限
			$powerData=Rolepower::where('roleid',$roleInfo['id'])->select();
			for($j=0;$j<count($powerData);$j++){
				$powerInfo=Power::where('id',$powerData[$j]['powerid'])->find();
				if($powerInfo['power']=='增加'){
					$flag['add']=1;
				}else if($powerInfo['power']=='删除'){
					$flag['del']=2;
				}else if($powerInfo['power']=='修改'){
					$flag['update']=3;
				}else{
					$flag['other']=4;
				}
			}
		}
		return $flag;
	}
    //处理图片
    public function upload($file){
        $info = $file->move( './public/uploads');
        if ($info) {
            $result = [
                'code'     => 1,
                'msg'      => '上传成功',
                'filename' => '/uploads/' . str_replace('\\', '/', $info->getSaveName())
            ];
            return $result['filename'];
        } else {
            return false;
        }
    }
    //上传图片
    public function uploadimage(){
	    // 获取表单上传文件 例如上传了001.jpg
	    $file = request()->file('image');
	    // 移动到框架应用根目录/public/uploads/ 目录下
	    if($file){
	        $info = $file->move('./public/uploads');
	        if($info){
	        	$result=[
	        	    'code'=>1,
	        	    'msg'=>'上传成功',
	        	    'filename'=>'/uploads/'.str_replace('\\', '/', $info->getSaveName())
	        	];
	        	return $result['filename'];
	        }else{
	        	return 111;
	        }
	    }else{
	    	return false;
	    }
	    
	}
	
}
?>