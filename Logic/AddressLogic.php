<?php
class AddressLogic extends LogicBase
{
	protected static $_before=array(
    	"aes"=>array("include"=>array("all")),
    	"checkLogin"=>array("include"=>array("all")),
    	);
	

	/*
	*功能：增加快递地址
	*/
	//http://ateam.ticp.io:9112/1024?tk=7690f69335408670e8481b9af630fadc&d={"city":"上海市","address":"宋园路","recipients":"张三","tel_pre":"86","tel":"13670581985","post_code":"123456"}
	public static function addAddress()
	{
		require_once("Models/Model_Address.php");

	    $city = static::$_P['city'];//城市
	    $address = static::$_P['address'];//快递地址
	    $recipients = static::$_P['recipients'];//收件人
	    $tel = static::$_P['tel'];//联系电话
	    $tel_pre = static::$_P['tel_pre'];//电话前缀 如+86
	    $post_code = static::$_P['post_code'];//Post Code
	    $user =  static::$user;
	    $uid = $user->id;
	    $ret = Model_Address::addAddress($uid,$city,$address,$recipients,$tel,$tel_pre,$post_code);
	    
	    if($ret['errcode'] == -1)
		{
			sendError($ret['msg']);
			return;

		}
		unset($ret['errcode']);
		unset($ret['msg']);
		addmsg(1024,$ret);
	}

	//返回该用户的快递地址列表
	//http://ateam.ticp.io:9112/1025?tk=f2a5b7aa9c6ed0c70da26453640a3db0
	public  static  function  getAddress()
	{
		require_once("Models/Model_Address.php");
		$user =  static::$user;
	    $uid = $user->id;
	    $ret = Model_Address::getAddress($uid);
	    if($ret['errcode'] == -1)
		{
			sendError($ret['msg']);
			return;
		}
		unset($ret['errcode']);
		unset($ret['msg']);
	    addmsg(1025,$ret);
	}

	//设置默认地址
	//http://ateam.ticp.io:9112/1026?tk=5b59c49fa05a1c985546a18857195c5f&d={"id":"12"}
	public  static  function setdefaultAddress()
	{
	    $id = static::$_P['id'];//快递地址表主键id
		require_once("Models/Model_Address.php");
		$user =  static::$user;
	    $uid = $user->id;
	    $ret = Model_Address::setdefaultAddress($uid,$id);
	    if($ret['errcode'] == -1)
		{
			sendError($ret['msg']);
			return;
		}
		unset($ret['errcode']);
		unset($ret['msg']);
	    addmsg(1026,$ret);

	}


	 //删除地址
    //http://ateam.ticp.io:9112/1027?tk=4cdce6266679b733e42ab3f427722cb1&d={"id":1}
    //
    public  static function removeAddress()
    {
    	require_once("Models/Model_Address.php");
        $user =  static::$user;
        $uid = $user->id;
		$id = static::$_P['id'];//地址表主键id
		$ret  = Model_Address::removeAddress($uid,$id);
      	if($ret['errcode'] == -1)
		{
			sendError($ret['msg']);
			return;
		}
		unset($ret['errcode']);
		unset($ret['msg']);
      	addmsg(1027,$ret);
    }


    //返回用户的默认地址
    //http://ateam.ticp.io:9112/1028?tk=4cdce6266679b733e42ab3f427722cb1
    //
    public static function getdefaultAddress()
    {
    	require_once("Models/Model_Address.php");
    	$user =  static::$user;
        $uid = $user->id;
        $ret  = Model_Address::getdefaultAddress($uid);
      	if($ret['errcode'] == -1)
		{
			sendError($ret['msg']);
			return;
		}
		unset($ret['errcode']);
		unset($ret['msg']);
      	addmsg(1028,$ret);
    }

    //修改用户地址 并设定此地址为默认地址
    //http://ateam.ticp.io:9112/1058?tk=7690f69335408670e8481b9af630fadc&d={"id":"7","city":"上海市1","address":"宋园路1","recipients":"李四","tel_pre":"86","tel":"13670581985","post_code":"123456"}
    public static function alterAddress()
    {
    	require_once("Models/Model_Address.php");
		$id = static::$_P['id'];//地址表主键id
	    $city = static::$_P['city'];//城市
	    $address = static::$_P['address'];//快递地址
	    $recipients = static::$_P['recipients'];//收件人
	    $tel = static::$_P['tel'];//联系电话
	    $tel_pre = static::$_P['tel_pre'];//电话前缀 如+86
	    $post_code = static::$_P['post_code'];//Post Code

	    $user =  static::$user;
	    $uid = $user->id;
	    $ret = Model_Address::alterAddress($uid,$id,$city,$address,$recipients,$tel,$tel_pre,$post_code);
	    
	    if($ret['errcode'] == -1)
		{
			sendError($ret['msg']);
			return;

		}
		unset($ret['errcode']);
		unset($ret['msg']);
		addmsg(1058,$ret);
    }

	
}

?>