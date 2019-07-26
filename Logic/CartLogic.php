<?php
class CartLogic extends LogicBase
{
	protected static $_before=array(
    	"aes"=>array("include"=>array("all")),
    	"checkLogin"=>array("include"=>array("all")),
    	);


	/*
	*功能：加入购物车
	*
	* url：http://ateam.ticp.io:9112/1016?tk=c0f177d03256767288e4e978b7dee2f1&d={"product_id":"1","num":"1","choicejson":{"Color":"Red","Size":"42"}}
	*
	* 参数：如，购买一双鞋(商品id为1，数量num为1)，选择的鞋是红色的，大小为42号，则传参形式如下：
	*      {"product_id":"1","num":"1","choicejson":{"Color":"Red","Size":"42"}
	*      的json：{"product_id":"1","num":"1","choicejson":{"Color":"Red","Size":"42"}}
	* 
	* @product_id:商品id
	* @num:  商品数量
	* @choicejson:数组，   若没有数据时，此数组元素为 空数组
	*/

	public static function addCart()
	{
		require_once("Models/Model_Cart.php");
	
		$product_id = static::$_P['product_id'];
		$num = static::$_P['num'];
		$choicejson = static::$_P['choicejson'];
		$user = static::$user;
		$uid = $user->id;//用户id

		$ret = Model_Cart::addCart($product_id,$num,$uid,$choicejson);
		if($ret['errcode'] == -1)
		{
			sendError($ret['msg']);
			return;

		}
		
		unset($ret['errcode']);
		unset($ret['msg']);
		addmsg(1016,$ret);
	}


	/*
	*修改购物车商品数量/属性
	*
	* @product_id：商品id
	* @num,修改后购物车商品的数量
	*
	* url：http://ateam.ticp.io:9112/1017?tk=c0f177d03256767288e4e978b7dee2f1&d={"product_id":"1","num":"1","choicejson":{"Color":"Red","Size":"42"}}
	*/
	public static function midifyCart()
	{
		require_once("Models/Model_Cart.php");

		$product_id = static::$_P['product_id'];
		$choicejson = static::$_P['choicejson'];

		$num = static::$_P['num'];
		$user = static::$user;
		$uid = $user->id;//用户id

		$ret = Model_Cart::midifyCart($product_id,$num,$uid,$choicejson);
		if($ret['errcode'] == -1)
		{
			sendError($ret['msg']);
			return;

		}
		
		unset($ret['errcode']);
		unset($ret['msg']);
		
		addmsg(1017,$ret);
	}



	/*
	*用户购物车列表
	*
	*
	* url：http://ateam.ticp.io:9112/1018?tk=c0f177d03256767288e4e978b7dee2f1
	*/
	public static function detailCart()
	{
		require_once("Models/Model_Cart.php");

		$user = static::$user;
		$uid = $user->id;//用户id

		$ret = Model_Cart::detailCart($user);
		if($ret['errcode'] == -1)
		{
			sendError($ret['msg']);
			return;

		}
		
		unset($ret['errcode']);
		unset($ret['msg']);
		
		addmsg(1018,$ret);
	}


	/*移除购物车

	*url：http://ateam.ticp.io:9112/1019?tk=c0f177d03256767288e4e978b7dee2f1&d={"cart_id":"1"}
	*/
	public static function deleteCart()
	{
		require_once("Models/Model_Cart.php");
		$cart_id = static::$_P['cart_id'];

		$user = static::$user;
		$uid = $user->id;//用户id

		$ret = Model_Cart::deleteCart($cart_id,$uid);
		if($ret['errcode'] == -1)
		{
			sendError($ret['msg']);
			return;
		}
		unset($ret['errcode']);
		unset($ret['msg']);
		addmsg(1019,$ret);
	}






	

	
}

?>