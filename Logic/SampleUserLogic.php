<?php
class SampleUserLogic extends LogicBase
{
	protected static $_before=array(
    	"aes"=>array("include"=>array("all")),
    	"checkLogin"=>array("include"=>array("all"),"except"=>array("login")),
    	);

	protected static function initChar(&$user)
	{
		// 在此处填充char 初始数据
	}

	protected static function loginFaild()
	{
		$ret = array("id"=>-1);
		addmsg(1,$ret);
	}

	public static function wxLogin()
	{
		require_once "Models/Model_Wx.php";
		require_once "Models/Model_Char.php";
		$code = static::$_P["code"];
		$unionid = static::$_P["unionid"];
		$user = null;

		$info = null;
		$wx = null;
		$access_token = null;
		$refresh_token = null;
		$openid = null;
		// $code 非空表示未授权，否则用refresh_token刷新
		if($code)
		{//授权
			$access_info = file_get_contents("https://api.weixin.qq.com/sns/oauth2/access_token?appid=".APPID."&secret=".APPSECRET."&code=$code&grant_type=authorization_code");
			$access_info = json_decode($access_info,true);
			$access_token = $access_info["access_token"];
			$refresh_token = $access_info["refresh_token"];
			$openid = $access_info['openid'];
		}
		else
		{
			$wx = Model_Wx::findOne($unionid,"unionid");
			if（$wx == null) return static::loginFaild();

			$openid = $wx->openid;
			$refresh_token = $wx->refresh_token;
			$access_info = file_get_contents("https://api.weixin.qq.com/sns/oauth2/refresh_token?appid=".APPID."&grant_type=refresh_token&refresh_token=".$refresh_token);
			$access_info = json_decode($access_info,true);
			if(!isset($access_info["access_token"])) return static::loginFaild();
			$access_token = $access_info["access_token"];
			$refresh_token= $access_info["refresh_token"];
		}
			
		$info = file_get_contents("https://api.weixin.qq.com/sns/userinfo?access_token=$access_token&openid=$openid");
		$info = json_decode($info,true);
		
		if ($wx == null) $wx = Model_Wx::findOne($info["unionid"],"unionid");
		if ($wx)
		{
			if ($wx->user_id == null)
			{
				$user = Model_Char::create()
				$user->nickname = $wx->nickname;
				$user->icon = $wx->icon;
				static::initChar($user);
				$user->save();
				$wx->user_id = $user->id;
				$wx->save();
			}
			else
			{
				$user = Model_Char::findOne($wx->user_id);
			}

		}
		else
		{
			$user = Model_Char::create()
			$user->nickname = $info["nickname"];
			$user->icon = $info["headimgurl"];
			static::initChar($user);
			$user->save();

			$wx = Model_Wx::create();
			$wx->user_id = $user->id;
			$wx->unionid = $info["unionid"];
			$wx->openid = $info["openid"];
			$wx->nickname = $info["nickname"];
			$wx->icon = $info["headimgurl"];
			$wx->sex = $info["sex"];
			$wx->city = $info["city"];
			$wx->province = $info["province"];
			$wx->access_token = $access_token;
			$wx->refresh_token = $refresh_token;
			$wx->save();
		}

		$tk = static::doLogin($user);
		$ret = $user->toArray();
		$ret["tk"] = $tk;
		addmsg(1,$ret);
	}

	public static function deviceLogin()
	{
		require_once "Models/Model_Device.php";
		require_once "Models/Model_Char.php";

		$dv = static::$_P["dv"];
		if ($dv == null) static::loginFaild();

		$dev = Model_Device::findOne($dv,"device");
		$user = null;
		if ($dev == null)
		{
			$user = Model_Char::create();
			$user->nickname = randStr(6);
			$user->icon = "http://lorempixel.com/96/96/?".md5("".time().",".$nickname);
			static::initChar($user);
			$user->save();

			$dev = Model_Device::create();
			$dev->device = $dv;
			$dev->user_id = $user->id;
		}
		else
		{
			$user = Model_Char::findOne($dev->user_id);
		}

		$tk = static::doLogin($user);
		$ret = $user->toArray();
		$ret["tk"] = $tk;
		addmsg(1,$ret);
	}
}
?>