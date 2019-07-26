<?php
class Model_Address extends ActiveRecord
{
	public static $tablepre = "user_";
	protected static $class = __CLASS__;
	protected static $table;
	protected static $cinit = false;
	protected static $primaryKey = 'id';
	protected static $_desc = null;
	protected static $_numCol = null;
	

	//修改用户地址
	public static function alterAddress($uid,$id,$city,$address,$recipients,$tel,$tel_pre,$post_code)
	{
		$self = self::selectOne("id=?",array($id));
		if($self)
		{
			$Model_Address = Model_Address::selectOne("uid=? and status=1 ",array($uid));
	    	if($Model_Address)
	    	{
	    		//取消之前的默认地址
	    		$Model_Address->status = 0;
	    		$Model_Address->save();
	    	}

	    	$self->city = $city ;//城市
	    	$self->address = $address;//用户快递地址
	    	$self->recipients = $recipients;//收件人
	    	$self->tel = $tel;//联系电话
	    	$self->tel_pre = $tel_pre;//电话前缀 如+86
	    	$self->post_code = $post_code;//Post code
	    	$self->status = 1;//地址状态  0 未使用  1，当前使用 
	    	$self->save();

			$ret['errcode'] =  0;//
	    	$ret['msg'] =  "成功";//
		}else
		{
			$ret['errcode'] =  -1;//
	    	$ret['msg'] =  "失败";
		}
	}

	//设置快递地址
	public  static function addAddress($uid,$city,$address,$recipients,$tel,$tel_pre,$post_code)
	{
		$ret = array();
	    $ret['data'] = array();
	    if($address)
	    {
	    	$Model_Address = Model_Address::selectOne("uid=? and status=1 ",array($uid));
	    	if($Model_Address)
	    	{
	    		//取消之前的默认地址
	    		$Model_Address->status = 0;
	    		$Model_Address->save();
	    	}

	    	//将新增地址设定为默认地址
	    	$Model_create = Model_Address::create();
	    	$Model_create->uid = $uid ;//用户id
	    	$Model_create->city = $city ;//城市
	    	$Model_create->address = $address;//用户快递地址
	    	$Model_create->recipients = $recipients;//收件人
	    	$Model_create->tel = $tel;//联系电话
	    	$Model_create->tel_pre = $tel_pre;//电话前缀 如+86
	    	$Model_create->post_code = $post_code;//Post code

	    	$Model_create->status = 1;//地址状态  0 未使用  1，当前使用 
	    	$Model_create->save();
	    	$ret['errcode'] =  0;//
	    	$ret['msg'] =  "成功";//
	    	$ret['data']['address'] = $Model_create->toArray();
	    }else
	    {
	    	$ret['errcode'] =  -1;//
	    	$ret['msg'] =  "没有填写快递地址";//
	    }

	   return $ret;
	}


	//返回用户的快递地址列表
	public static function getAddress($uid)
	{
		$ret = array();
		$ret['data'] = array();

		$self =  self::select("uid=? order by status desc",array($uid));
		if($self)
		{
			foreach ($self as $key => $value) {
				$ret['data'][$key] = $value->toArray();
			}
		}
		$ret['errcode'] =  0;//
	    $ret['msg'] =  "成功";//
		return $ret;
	}

	//设置快递地址为默认地址
	public  static function  setdefaultAddress($uid,$id)
	{
		$Model_Address = self::selectOne("uid=? and status=1 ",array($uid));
		if($Model_Address->id == $id)
		{	
			// $ret['errcode'] = -1;
	  		//   	$ret['msg'] = "当前为默认地址";
	  		$ret['errcode'] = 0;
	    	$ret['msg'] = "成功";
	    	$ret['data']= $Model_Address->toArray();
		}else
		{
			if($Model_Address)
	    	{
	    		$Model_Address->status = 0;
	    		$Model_Address->save();
	    	}

	    	$Model_Address = self::selectOne("id=?  ",array($id));
	    	$Model_Address->status = 1;
	    	$Model_Address->save();
	    	$ret =  array();
	    	$ret['errcode'] = 0;
	    	$ret['msg'] = "成功";
	    	$ret['data']= $Model_Address->toArray();
		}
    	
    	return $ret;
	}
	



		/**
	* 删除送货地址
	*/
    public  function  removeAddress($uid,$id)
    {
        $Model_Address = self::selectOne("id=? and uid=?",array($id,$uid));
		
		if($Model_Address)
    	{
    		self::delete('id=?',array($id));
    		$ret['errcode'] =  0;//
	    	$ret['msg'] =  "成功";//
    	}else
    	{
    		$ret['errcode'] =  -1;//
	    	$ret['msg'] =  "地址不存在";//
    	}
    	return $ret;
    }


     //返回用户的默认地址
    public static function getdefaultAddress($uid)
    {
    	$ret = array();
    	$ret['data']=  array();
    	$arr = self::defaultAddress($uid);
    	if(empty($arr))
    	{
    		$ret['errcode'] =  -1;//
	    	$ret['msg'] =  "地址不存在";//
    	}else
    	{
    		$ret['data']= $arr;
	    	$ret['errcode'] =  0;//
	    	$ret['msg'] =  "success";//
    	}
		
    	return $ret;
    }

    public  static function defaultAddress($uid)
    {
    	$ret=  array();
		$Model_Address = self::selectOne("uid=? and status=1 ",array($uid));
		if($Model_Address)
		{
			$ret = $Model_Address->toArray();
		}
		return $ret;
    }
}
?>