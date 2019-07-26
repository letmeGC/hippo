<?php
class UserLogic extends LogicBase
{
	protected static $_before=array(
    	"aes"=>array("include"=>array("all"),"except"=>array("upload")),
    	"checkLogin"=>array("include"=>array("all"),"except"=>array("login","sendCode","phone_regist","upload","forgetPwd","newDealerMd")),
    	);

	public static function login(){
		require_once "Models/Model_Char.php";

		$phone = static::$_P['phone'];
		$password = static::$_P['password'];

		if(!$phone || !$password){
			sendError('参数错误');
			return;
		}
		$user = Model_Char::selectOne('phone=?',array($phone));
		if(!$user){
			 sendError('用户不存在');
		  	  return;
		}else{
		 	if(md5($password)!=$user->password){
                    sendError('密码错误');
				    return;
			}else{
				$tk = static::doLogin($user);
				$ret = $user->toArray();
				$ret["tk"] = $tk;
				addmsg(1001,$ret);
			}
		}
	}







	public static function  sendCode(){
		$sign = static::$_P['sign'];
		$time = static::$_P['time'];
		$phone = static::$_P['phone'];
		$phone = str_replace('+', '', $phone);
		if(!$phone || !$time || !$sign){
			sendError('参数错误');
			return;			
		}
		if(!(md5($time.Consts::LG_VERIFY_KEY) == $sign)){
			sendError('sign错误');
			return;
		}
		$code = rand(10000,99999);
		mem()->set("hippo_code" . $phone, $code, 300);
		$content = "Your activation code:$code";
		$param_time = time();
		$sms_sign = md5($param_time.Consts::LG_VERIFY_KEY);
		$param = 'merchant_id='.Consts::LG_VERIFY_MERCHANT_ID.'&time='.$param_time.'&sign='.$sms_sign.'&to='.$phone.'&from='.Consts::LG_VERIFY_FROM.'&text='.$content;
		require_once("Utils/httpRequest.php");
		lg($param);
		$result = fetch_page(Consts::LG_VERIFY_URL, $param);
		lg($result);
		addmsg(1,'success');
	}
   
	public static function  phone_regist(){
		require_once "Models/Model_Char.php";
		require_once "Models/Model_Snd_md.php";
		$code = static::$_P['code'];
		$phone = static::$_P['phone'];
		$password = static::$_P['password'];
		$email = static::$_P['email'];
		$city = static::$_P['city'];
		$province = static::$_P['province'];
		$store_address = static::$_P['store_address'];
		$post_code = static::$_P['post_code'];
		$store_number = static::$_P['store_number'];
		$store_name = static::$_P['store_name'];
		$person_incharge = static::$_P['person_incharge'];
        $type = static::$_P['type'];

		$store_status = static::$_P['store_status'];

		$master_dealer_code = static::$_P['master_dealer_code'];
		$npwp_img = static::$_P['npwp_img'];
		$ktp_img = static::$_P['ktp_img'];
		$store_img = static::$_P['store_img'];

		if(!$code || !$phone || !$password ){
			sendError('参数错误');
			return;
			// addmsg(-1,'参数错误'); 
			// return;
		}
		if(Model_Char::selectOne('phone=?',[$phone])){
			sendError('用户已存在');
			return;
		}
		// if(mem()->set("hippo_code" . $phone)!=$code){
		// 	sendError('验证码错误');
		// 	return;
		// 	// addmsg(-1,'验证码错误');
		// 	// return;
		// }

		$user = Model_Char::create();
		$user->nickname  = $phone;
		$user->phone = $phone;
		$user->password = md5($password);
		$user->email  = $email;
		$user->province = $province;
		$user->city = $city;
		$user->store_address = $store_address;
		$user->store_name = $store_name;
		$user->post_code  = $post_code;
		$user->store_number = $store_number;
		$user->person_incharge = $person_incharge;
		$user->store_status  = $store_status;
		//$user->master_dealer_code = $master_dealer_code;
		$user->npwp_img = $npwp_img;
		$user->ktp_img  = $ktp_img;
		$user->store_img = $store_img;
		$user->type = $type;
		$user->save();
		if(!$user->id){
			sendError('创建用户失败');
			return;
		}
        if($type == 2){
              $md_id =   Model_Char::get_uid($master_dealer_code);
              if(!$md_id){
                     sendError('master_dealer_code 错误');
                     return;
              }
             $snd_md =  Model_Snd_md::create();
             $snd_md->md_id = $md_id;
             $snd_md->snd_id = $user->id;
             $snd_md->save();
        }

		addmsg(1,'success');
  }
  

  private static  function checkCode($code,$phone)
  {
	  $memCode = mem()->get("hippo_code".$phone);
	  if( $memCode && $code ==  $memCode ){ return true; }
	  return false;
  }



/**
 * 上传
 */
  public static function upload(){

      require_once "Models/Model_File_Upload.php";
      $key = $_REQUEST['key'];
      $up = new Model_File_Upload;
      $path = "images/user";
      $custom = $key.time();
      $up -> set("path", $path);
      $up -> set("maxsize", 20000000);
      $up -> set("custom" ,$custom);
      $up -> set("allowtype", array("gif","png","jpg","jpeg"));
      $up -> set("israndname", false);
      if($up->upload($key)) {
            $res = array('errcode'=>0,'newFile'=>Consts::imgurlhttp.$path.'/'.$up->getFileName());
      }else{
		    sendError($up->getErrorMsg());return;
      }
      addmsg(1012,$res);
  }



  public static function changePwd()
  {
		
		require_once "Models/Model_Char.php";
		$user = static::$user;  
		$old = static::$_P['old_pwd'];
		$new = static::$_P['new_pwd'];
		$retype = static::$_P['retype_pwd'];
		require_once "Models/Model_Char.php";
	
		try{
			if(!$old || !$new || !$retype){  throw new Exception("密码不能为空"); return;}
			if($user->password != md5($old)){  throw new Exception("密码错误"); return;}
			if($new != $retype){ throw new Exception("两次密码输入不一致"); return; }
			$model_char = Model_Char::selectOne("id = ?",array($user->id));
			$model_char->password = md5($new);
			$model_char->save();
			addmsg(1020,array('msg'=>'密码修改成功'));
		}catch(Exception $e){

			sendError($e->getMessage());
			return;  
		}
	  
 }


  public static function editProFile()
  {
	 require_once "Models/Model_Char.php"; 
	 $user = static::$user;  
     $nickname = static::$_P['nickname'];
	 $store_name = static::$_P['store_name'];
	 $email = static::$_P['email'];
	 $contact_way  = static::$_P['contact_way'];
	 $city = static::$_P['city'];
	 $province = static::$_P['province'];

	 $head_img = static::$_P['head_img'];
	 if($head_img){

         lgj($head_img);
		 $head_img = explode(Consts::imgurlhttp,$head_img);
		 $head_img = $head_img[1];

         lgj($head_img);
	 }
	 $edit_arr = array(
		 'nickname'=>$nickname,
		 'store_name'=>$store_name,
		 'email'=>$email,
		 'contact_way'=>$contact_way ,
		 'head_img'=>$head_img,
		 'province'=>$province,
		 'city'=>$city,
	 );
	 $model_char = Model_Char::selectOne("id = ?",array($user->id));
	 try{
		foreach($edit_arr as $key=>$value){
			if(!$value){ continue;}
			$model_char->$key = $value;
			$model_char->save();
		 } 
		 addmsg(1021,array('msg'=>'修改成功'));
	 }catch(Exception $e){
		sendError($e->getMessage());
		return;  
	 }
	
 }

   public static function forgetPwd()
   {
	 	require_once "Models/Model_Char.php";
	    try{
			$phone =static::$_P['phone'];
			$password = static::$_P['pwd'];
			$code = static::$_P['code'];
			if(!$password || !$phone){  throw new Exception("信息不完整"); return;}
			if(!self::checkCode($code,$phone)){  throw new Exception("验证码错误"); return;}
			$model_char = Model_Char::selectOne('phone=?',array($phone));
			if(!$pl){  throw new Exception("用户不存在"); return;}
			$model_char->password =md5($password);
			$model_char->save();
			addmsg(1022,array('msg'=>'修改成功'));
		}catch(Exception $e){
            sendError($e->getMessage());
		    return;  
		}
	 
   }


   public static function feedBack(){
        require_once "Models/Model_Feed_back.php";
        try{
                 $content = static::$_P['content'];
                $user = static::$user;
                if(empty($content)){  throw new Exception("信息不能为空"); return;}
                $model = Model_Feed_back::create();
                $model->uid = $user->id;
                $model->content = htmlspecialchars($content);
                $model->save();
                addmsg(1023,array('msg'=>'反馈成功'));
        }catch(Exception $e){
               sendError($e->getMessage());
               return;
        }
   }



   /*
   *Profile--My Order/Agent Order未读记录数
   *ateam.ticp.io:9112/1054?tk=56dc26c78d194953af37a839c9811834
   */
   public static function Profile_unread()
   {	
   		require_once("Models/Model_Orders_read.php");
   		$user =  static::$user;
   		$ret = Model_Orders_read::orders_unread($user);
		addmsg(1054,$ret);
   }

    /**
     * new dealer 绑定 md
     */
   public  static  function  ndBuildMd()
      {

         try{
              require_once "Models/Model_Snd_md.php";
              Model_Snd_md::tranDo(function()  {

                      $user = static::$user;
                      $md_arr =   static::$_P['md_arr'];
                      if(empty($md_arr) || (count($md_arr) > 3) ){
                              throw new Exception("请选择1 - 3 个MD");
                              return;
                      }
                      foreach ($md_arr as $key=>$value){
                             $model = Model_Snd_md::create();
                             $model->snd_id = $user->id;
                             $model->md_id = $value;
                             $model->save();
                      }

              });

              addmsg(1061,array('msg'=>'MD 绑定成功'));
         }catch (Exception $e){
               sendError($e->getMessage());
               return;
         }

    }


    /**
     * 当前用户的 md 列表
     */
 public  static  function  mdList()
 {
         require_once "Models/Model_Snd_md.php";
         require_once "Models/Model_Char.php";
         $user = static::$user;
         $mdIdArray = Model_Snd_md::getMd($user->id);
         $mdArray = array();
         if($mdIdArray){
               $md_arr = Model_Char::selectBySql("select  `id`,`head_img`,`nickname`  from `user_char` where `id` in(".implode(',',$mdIdArray).")");
               foreach ($md_arr as $key => $value){
                     $mdArray[] = array('id'=>$value->id,'head_img'=>Consts::imgurlhttp.$value->head_img,'nickname'=>$value->nickname);
               }
         }

         addmsg(1062,$mdArray);
}

public static function storeInfo()
{
	require_once "Models/Model_Char.php";
	$id = static::$_P['id'];
	$page = static::$_P['page'] ? static::$_P['page'] : 1;
	$show = 10;
	$start = ($page -1)*$show; 

	if($id){ 
		$where = " where `id` = {$id}";
	}else{
		$where = " limit {$start},{$show}";
	}
	$data = Model_Char::selectBySql("select * from `user_char`".$where);
    $res = array();
	foreach($data as $key => $value){
		$res[] = $value->toArray();
	}
	addmsg(1063,$res);
}



}