<?php
namespace app\index\controller;
use think\Controller;
use app\admin\model\User;
class Hong extends Controller{
	public function lala(){
		$appid = 'wx2ede70c97a1b1cc3';
		$redirect_uri='http://cunjiugongzhonghao.taiyannet.com/public/index.php/index/Hong/user';
		$res=header('location:https://open.weixin.qq.com/connect/oauth2/authorize?appid='.$appid.'&redirect_uri='.$redirect_uri.'/oauth.php&response_type=code&scope=snsapi_userinfo&state=123&connect_redirect=1#wechat_redirect');
		//return $res;
	}
	public function user(){
		$code=$_GET['code'];
		//换成自己的接口信息
		$appid = 'wx2ede70c97a1b1cc3';
		$appsecret = '6c14c6e462f3abe31d24022d7b9afe75';
		$token_url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$appid.'&secret='.$appsecret.'&code='.$code.'&grant_type=authorization_code';
		$token = json_decode(file_get_contents($token_url));
		if (isset($token->errcode)) {
			echo '<h1>错误：</h1>'.$token->errcode;
			echo '<br/><h2>错误信息：</h2>'.$token->errmsg;
			exit;
		}
		$access_token_url = 'https://api.weixin.qq.com/sns/oauth2/refresh_token?appid='.$appid.'&grant_type=refresh_token&refresh_token='.$token->refresh_token;
		//转成对象
		$access_token_string=file_get_contents($access_token_url);
		$access_token = json_decode(file_get_contents($access_token_url));
		$user_info_url = 'https://api.weixin.qq.com/sns/userinfo?access_token='.$access_token->access_token.'&openid='.$access_token->openid.'&lang=zh_CN';
		$user_info = json_decode(file_get_contents($user_info_url));
		if (isset($user_info->errcode)) {
			echo '<h1>错误：</h1>'.$user_info->errcode;
			echo '<br/><h2>错误信息：</h2>'.$user_info->errmsg;
			exit;
		}
		
		//打印用户信息
		$data['openid']=$user_info->openid;
		$data['nickname']=$user_info->nickname;
		$data['headimgurl']=$user_info->headimgurl;
		$data['sex']=$user_info->sex;
		$userM=new User();
		$addres=$userM->save($data);
		if($addres){
			return 1;
		}else{
			return 0;
		}
	}
	public function getAccesstoken(){
		 $appid="wx2ede70c97a1b1cc3";
		 $appsecret="6c14c6e462f3abe31d24022d7b9afe75";
		 $posturl="https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$appid&secret=$appsecret";
		 $resstring1=file_get_contents($posturl);
		
		 $cont = json_decode($resstring1);
		 return $cont->access_token;
	}
	//登录页面
	public function login(){
		return $this->fetch();
	}
}
