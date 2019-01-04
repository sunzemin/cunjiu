<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/2
 * Time: 14:16
 */

namespace app\admin\controller;
use think\Controller;
use app\admin\model\Admin;
use app\admin\model\Role;
use app\admin\model\Adminrole;
use app\admin\model\Shop;
use app\admin\model\Power;
use app\admin\model\Rolepower;
class Adminstror extends Common
{
     //管理员信息
    public function adminData(){
        $adminM=new Admin();
        $adminRes=$adminM->order('id','asc')->paginate(10);
        $this->assign('adminRes',$adminRes);
        //数据页数
        $page=$adminRes->render();
        $this->assign('page',$page);
        return $this->fetch();
    }
    //增加管理员信息
    public function adminAdd(){
    	//查询店铺信息
    	$shopList=Shop::all();
    	$this->assign('shopList',$shopList);
    	//查询所有角色信息
    	$roleData=Role::all();
    	$this->assign('roleData',$roleData);
    	if(request()->isPost()){
    		$admin_name=input('param.admin_name');
        	$admin_pwd=input('param.admin_pwd');
        	$md5adminpwd=md5($admin_name.md5($admin_pwd));
        	$data['admin_name']=$admin_name;
        	$data['admin_pwd']=$md5adminpwd;
        	$data['shop_id']=input('param.shop_id');
        	$data['register_time']=time();
        	$result=$this->seladmin();
        	if($result){
    		    $adminM=new Admin();
	        	$selres=$adminM->where('admin_name',$admin_name)->find();
	        	if($selres){
	        		$res=[
	        		    'code'=>0,
	        		    'msg'=>'用户名已被使用'
	        		];
	        		return json($res);
	        	}
	        	$saveres=$adminM->save($data);
	        	$adminInfo=$adminM->where('admin_name',$admin_name)->find();
	        	//管理员的角色信息
	        	for($i=1;$i<count($roleData)+1;$i++){
	            	$roleid=input('param.role'.$i);
	            	if($roleid==null){	
	            	}else{
	            		$roleidlist[]=[
	            		   'adminid'=>$adminInfo['id'],
	            		   'roleid'=>$roleid
	            		];
	            	}
	        	}
	        	$adminroleM=new Adminrole();
	        	$addres=$adminroleM->saveAll($roleidlist);
	        	if($saveres!=''&&$addres!=''){
	        		$res=[
	        		    'code'=>1,
	        		    'msg'=>'添加成功'
	        		];
	        		return json($res);
	        	}else{
	        		//删除添加的管理信息
	        		$delres=$adminM->where('id',$adminInfo['id'])->delete();
	        		$res=[
	        		    'code'=>0,
	        		    'msg'=>'添加失败'
	        		];
	        		return json($res);
	        	}
    	    }else{
    	    	$res=[
	        		    'code'=>0,
	        		    'msg'=>'您不是总管理员...'
	        		];
	        		return json($res);
    	    } 
        	
    	}
    	return $this->fetch();
    	
    }
    //管理员信息的修改
    public  function adminUpdate(){
        $id=input('param.id');
        $adminInfo=Admin::where('id',$id)->find();
        $this->assign('adminInfo',$adminInfo);
        //查询该用户拥有的所有角色
        $adminRole=Adminrole::where('adminid',$id)->select();
        $this->assign('adminRole',$adminRole);
        //查询所有的角色信息
        $roleData=Role::all();
        foreach ($roleData as $key=>$role){
            foreach ($adminRole as $adminR){
                if($role['id']==$adminR['roleid']){
                    $role['flag']=1;
                    break;
                }
            }
        }
        $this->assign('roleData',$roleData);
        if(request()->isPost()){
        	$adminid=input('param.adminid');
        	$admin_name=input('param.admin_name');
        	$admin_pwd=input('param.admin_pwd');
        	$md5adminpwd=md5($admin_name.md5($admin_pwd));
        	$data['admin_name']=$admin_name;
        	$data['admin_pwd']=$md5adminpwd;
        	$result=$this->seladmin();
        	if($result){
        		$saveres1=Admin::where('id',$adminid)->update($data);
	        	//先删除角色再进行添加
	        	$adminroleM=new Adminrole();
	        	$delres=$adminroleM->where('adminid',$adminid)->delete();
	            //为该管理员添加角色信息
	            for($i=1;$i<count($roleData)+1;$i++){
	            	$roleid=input('param.role'.$i);
	            	if($roleid==null){	
	            	}else{
	            		$roleidlist[]=[
	            		   'adminid'=>$adminid,
	            		   'roleid'=>$roleid
	            		];
	            	}
	        	}
	        	$addres=$adminroleM->saveAll($roleidlist);
	        	$res=[
	        		    'code'=>1,
	        		    'msg'=>'添加成功'
	        		];
	        		return json($res);
        	}else{
        		$res=[
	        		    'code'=>0,
	        		    'msg'=>'您不是管理员....'
	        		];
	        		return json($res);
        	}
        }
        return $this->fetch();
    }
    //删除管理员信息
    public function adminDel(){
    	$adminid=input('param.adminid');
    	$result=$this->seladmin();
    	if($result){
    		$delAdmin=Admin::where('id',$adminid)->delete();
	    	$delAdminRole=Adminrole::where('adminid',$adminid)->delete();
	    	if($delAdmin!=''&&$delAdminRole!=''){
	    		$res=[
	    		  'code'=>1,
	    		  'msg'=>'删除成功'
	    		];
	    		return json($res);
	    	}else{
	    		$res=[
	    		   'code'=>0,
	    		   'msg'=>'删除失败'
	    		];
	    		return json($res);
	    	}
    	}else{
    		$res=[
	    		   'code'=>0,
	    		   'msg'=>'您不是管理员...'
	    		];
	    		return json($res);
    	}
    }
    //角色管理
    public  function role(){
    	//角色数据
        $roleData=Role::order('id','asc')->paginate(10);
        $this->assign('roleData',$roleData);
        //数据页数
        $page=$roleData->render();
        $this->assign('page',$page);
        return $this->fetch();
    }
    //添加角色信息
    public function roleAdd(){
    	//查询权限信息
        $powerData=Power::all();
        $this->assign('powerData',$powerData);   
    	if(request()->isPost()){
    		$role=input('param.role');
    		$data['role']=input('param.role');
    		$data['sort']=input('param.sort');
    		$result=$this->seladmin();
    		if($result){
    			$roleM=new Role();
	    		$addres=$roleM->save($data);
	    		//查询刚插入角色信息
	    		$roleInfo=Role::where('role',$role)->find();
	    		//添加角色权限
	    		for($i=1;$i<count($powerData)+1;$i++){
	    			$powerid=input('param.power'.$i);
	    			if($powerid!=null){
	    				$rolepower[]=[
	    				   'roleid'=>$roleInfo['id'],
	    				   'powerid'=>$powerid
	    				];
	    			}
	    		}
	    		//添加角色权限信息
	    		$rolepowerM=new Rolepower();
	    		$addresrole=$rolepowerM->saveAll($rolepower);
	    		if($addres!=''&&$addresrole!=''){
	    			$res=[
	    			   'code'=>1,
	    			   'msg'=>'添加成功'
	    			];
	    			return json($res);
	    		}else{
	    			//删除添加的角色信息
	    			Role::where('role',$role)->delete();
	    			$res=[
	    			   'code'=>0,
	    			   'msg'=>'添加失败'
	    			];
	    			return json($res);
	    		}
    		}else{
    			$res=[
	    		   'code'=>0,
	    		   'msg'=>'您不是管理员...'
	    		];
	    		return json($res);
    		}
    		
    	} 	
    	return $this->fetch();
    }
    //修改角色信息
    public function roleUpdate(){
    	//查询权限信息
        $powerData=Power::all();
        //查询需要修改的角色信息
    	$id=input('param.id');
    	$roleInfo=Role::where('id',$id)->find();
    	$this->assign('roleInfo',$roleInfo);
    	//查询该角色拥有的权限
    	$rolepowerData=Rolepower::where('roleid',$id)->select();
    	for($i=0;$i<count($powerData);$i++){
    		for($j=0;$j<count($rolepowerData);$j++){
    			if($powerData[$i]['id']==$rolepowerData[$j]['powerid']){
    				$powerData[$i]['flag']=1;
    			}
    		}
    	}
    	$this->assign('powerData',$powerData);  
    	if(request()->isPost()){
    		$roleid=input('param.roleid');
    		$data['role']=input('param.role');
    		$data['sort']=input('param.sort');
    		$result=$this->seladmin();
    		if($result){
    			$saveres=Role::where('id',$roleid)->update($data);
	    		//先删除该角色的所有权限
	    		$delres=Rolepower::where('roleid',$roleid)->delete();
	    		//获取选择的权限信息
	    		for($j=1;$j<count($powerData)+1;$j++){
	    			$powerid=input('param.power'.$j);
	    			if($powerid!=null){
	    				$rolepowerData[]=[
	    				   'roleid'=>$roleid,
	    				   'powerid'=>$powerid
	    				];
	    			}
	    		}
	    		//为该角色添加权限
	    		$rolepowerM=new Rolepower();
	    		$addrolpow=$rolepowerM->saveAll($rolepowerData);
	    		if($saveres!=''&&$addrolpow!=''){
	    			$res=[
	    			   'code'=>1,
	    			   'msg'=>'添加成功'
	    			];
	    			return json($res);
	    		}else{
	    			$res=[
	    			   'code'=>0,
	    			   'msg'=>'添加失败'
	    			];
	    			return json($res);
	    		}
    		}else{
    			$res=[
	    		   'code'=>0,
	    		   'msg'=>'您不是管理员...'
	    		];
	    		return json($res);
    		}
    		
    	}
    	return $this->fetch();
    }
    //删除角色信息 
    public function roleDel(){
    	$roleid=input('param.roleid');
    	$result=$this->seladmin();
    	if($result){
    		$delres1=Role::where('id',$roleid)->delete();
	    	$delres2=Rolepower::where('roleid',$roleid)->delete();
	    	//删除该角色下的权限信息
	    	if($delres1!=''&&$delres2!=''){
	    		$res=[
	    		   'code'=>1,
	    		   'msg'=>'删除成功'
	    		];
	    		return json($res);
	    	}else{
	    		$res=[
	    		   'code'=>0,
	    		   'msg'=>'删除失败'
	    		];
	    		return json($res);
	    	}
    	}else{
    		$res=[
	    		   'code'=>0,
	    		   'msg'=>'您不是管理员...'
	    		];
	    		return json($res);
    	}
    	
    }
    //权限管理
    public function power(){
    	//查询权限信息
    	$powerData=Power::paginate(10);
    	$this->assign('powerData',$powerData);
    	//数据分页
    	$page=$powerData->render();
    	$this->assign('page',$page);
    	return $this->fetch();
    }
    public function powerAdd(){
    	if(request()->isPost()){
    		$data['power']=input('param.power');
    		$data['sort']=input('param.sort');
    		$result=$this->seladmin();
    		if($result){
    			$powerM=new Power();
	    		$addres=$powerM->save($data);
	    		if($addres){
	    			$res=[
	    			   'code'=>1,
	    			   'msg'=>'添加成功'
	    			];
	    			return json($res);
	    		}else{
	    			$res=[
	    			   'code'=>0,
	    			   'msg'=>'添加失败'
	    			];
	    			return json($res);
	    		}
    		}else{
    			$res=[
	    		   'code'=>0,
	    		   'msg'=>'您不是管理员...'
	    		];
	    		return json($res);
    		}
    	}
    	return $this->fetch();
    }
    //修改权信信息
    public function powerUpdate(){
    	if(request()->isPost()){
    		$powerid=input('param.powerid');
    		$data['power']=input('param.power');
    		$data['sort']=input('param.sort');
    		$result=$this->seladmin();
    		if($result){
    			$saveres=Power::where('id',$powerid)->update($data);
	    		if($saveres){
	    			$res=[
	    			   'code'=>1,
	    			   'msg'=>'修改成功'
	    			];
	    			return json($res);
	    		}else{
	    			$res=[
	    			    'code'=>0,
	    			    'msg'=>'修改失败'
	    			];
	    			return json($res);
	    		}
    		}else{
    			$res=[
	    		   'code'=>0,
	    		   'msg'=>'您不是管理员...'
	    		];
	    		return json($res);
    		}
    	}
	  	$id=input('param.id');
	  	$powerInfo=Power::where('id',$id)->find();
	  	$this->assign('powerInfo',$powerInfo);
    	return $this->fetch();
    }
    //删除权限信息
    public function powerDel(){
    	$powerid=input('param.powerid');
    	$result=$this->seladmin();
    	if($result){
    		$delres=Power::where('id',$powerid)->delete();
	    	if($delres){
	    		$res=[
	    			   'code'=>1,
	    			   'msg'=>'修改成功'
	    			];
	    			return json($res);
	    	}else{
	    		$res=[
	    			    'code'=>0,
	    			    'msg'=>'修改失败'
	    			];
	    			return json($res);
	    	}
    	}else{
    		$res=[
	    		   'code'=>0,
	    		   'msg'=>'您不是管理员...'
	    		];
	    		return json($res);
    	}
    	
    }  
}