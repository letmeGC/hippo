<?php
class OrderLogic extends LogicBase
{
	protected static $_before=array(
    	"aes"=>array("include"=>array("all")),
    	"checkLogin"=>array("include"=>array("all")),
    	);

	/*
	*功能：选择购物车的商品 下单
	*ateam.ticp.io:9112/1029?tk=56dc26c78d194953af37a839c9811834&d={"cart_id":["1","2","3"]}
	*/
	public static function  preOrder()
	{
		require_once("Models/Model_Order.php");
		$arr = array();
    	$user =  static::$user;
		
	    $cart_id = static::$_P['cart_id'];//购物车的主键组成的数组
	   

	    $ret = Model_Order::preOrder($user,$cart_id);
	    if($ret['errcode'] == -1)
		{
			sendError($ret['msg']);
			return;
		}
		unset($ret['errcode']);
		unset($ret['msg']);
		addmsg(1029,$ret);
	}


	/*
	*功能：Item List 选择购物车的商品 下单产品的具体列表
	*ateam.ticp.io:9112/1030?tk=56dc26c78d194953af37a839c9811834&d={"cart_id":["1","2","3"]}
	*/
	public static function  preOrderProductDetail()
	{
		require_once("Models/Model_Order.php");
		$arr = array();
    	$user =  static::$user;
		
	    $cart_id_arr = static::$_P['cart_id'];//购物车的主键组成的数组
	   

	    require_once("Models/Model_Cart.php");
		$ret = Model_Cart::choicedetailCart($user,$cart_id_arr,1);
	    if($ret['errcode'] == -1)
		{
			sendError($ret['msg']);
			return;
		}
		unset($ret['errcode']);
		unset($ret['msg']);
		addmsg(1030,$ret);
	}


	/*
	*功能：用户确认下单
	*ateam.ticp.io:9112/1031?tk=56dc26c78d194953af37a839c9811834&d={"payment_menthod":"1","address_id":"4","master_dealer":"1471558","cart_id":["1","2","3"]}
	*/
	public static function Order()
	{
		require_once("Models/Model_Order.php");
		$arr = array();
    	$user =  static::$user;
	    $master_dealer = static::$_P['master_dealer'];//选择的master dealer
		
	    $cart_id_arr = static::$_P['cart_id'];//购物车的主键组成的数组
	    $address_id = static::$_P['address_id'];//快递地址表 主键id
	    $payment_menthod = static::$_P['payment_menthod'];//支付渠道1、CBD    2、COD   3、OTP
	   	


		$ret = Model_Order::Order($user,$master_dealer,$address_id,$cart_id_arr,$payment_menthod);
	    if($ret['errcode'] == -1)
		{
			sendError($ret['msg']);
			return;
		}
		unset($ret['errcode']);
		unset($ret['msg']);
		addmsg(1031,$ret);
	}

	/*
	*功能：我的订单--On Progress
	*url：ateam.ticp.io:9112/1032?tk=56dc26c78d194953af37a839c9811834&d={"page":"0"}
	*/
	public static function MyOrder_On_Progress()
	{
		require_once("Models/Model_Order.php");

	    if(static::$_P['page'])//分页： 第一页：0  
	    {
	    	$page = static::$_P['page'];
	    }else
	    {
	    	$page =0;//分页 第一页
	    }
		$arr = array();
    	$user =  static::$user;


    	$ret = Model_Order::MyOrder_On_Progress($user,$page);
	    if($ret['errcode'] == -1)
		{
			sendError($ret['msg']);
			return;
		}
		unset($ret['errcode']);
		unset($ret['msg']);
		addmsg(1032,$ret);
	}


	/*
	*功能：我的订单--On Progress的某条具体记录
	*url：ateam.ticp.io:9112/1033?tk=56dc26c78d194953af37a839c9811834&d={"out_trade_no":"15173675259195pp0nm1tv5e"}
	*/
	public static function MyOrder_On_Progress_detail()
	{
		require_once("Models/Model_Order.php");

	  
		$arr = array();
    	$user =  static::$user;
	    $out_trade_no = static::$_P['out_trade_no'];//订单号

    	$ret = Model_Order::MyOrder_On_Progress_detail($user,$out_trade_no);
	    if($ret['errcode'] == -1)
		{
			sendError($ret['msg']);
			return;
		}
		unset($ret['errcode']);
		unset($ret['msg']);
		addmsg(1033,$ret);
	}

	/*我的订单--On Progress  取消订单
	*
	* url：ateam.ticp.io:9112/1034?tk=56dc26c78d194953af37a839c9811834&d={"out_trade_no":"15173675259195pp0nm1tv5e"}
	**/
	public static function MyOrder_On_Progress_cancel()
	{
		require_once("Models/Model_Order.php");
		$arr = array();
    	$user =  static::$user;
	    $out_trade_no = static::$_P['out_trade_no'];//订单号

    	$ret = Model_Order::MyOrder_On_Progress_cancel($user,$out_trade_no);
	    if($ret['errcode'] == -1)
		{
			sendError($ret['msg']);
			return;
		}
		unset($ret['errcode']);
		unset($ret['msg']);
		addmsg(1034,$ret);
	}




	/*
	*功能：我的订单--Approved
	*url：ateam.ticp.io:9112/1035?tk=56dc26c78d194953af37a839c9811834&d={"page":"0"}
	*/
	public static function MyOrder_Approved()
	{
		require_once("Models/Model_Order.php");

	    if(static::$_P['page'])//分页： 第一页：0  
	    {
	    	$page = static::$_P['page'];
	    }else
	    {
	    	$page =0;//分页 第一页
	    }
		$arr = array();
    	$user =  static::$user;


    	$ret = Model_Order::MyOrder_Approved($user,$page);
	 //    if($ret['errcode'] == -1)
		// {
		// 	sendError($ret['msg']);
		// 	return;
		// }
		unset($ret['errcode']);
		unset($ret['msg']);
		addmsg(1035,$ret);
	}




	/*
	*功能：我的订单--Approved的某条具体记录
	*url：ateam.ticp.io:9112/1036?tk=56dc26c78d194953af37a839c9811834&d={"out_trade_no":"15173675259195pp0nm1tv5e"}
	*/
	public static function MyOrder_Approved_detail()
	{
		require_once("Models/Model_Order.php");
			  
		$arr = array();
    	$user =  static::$user;
	    $out_trade_no = static::$_P['out_trade_no'];//订单号

    	$ret = Model_Order::MyOrder_Approved_detail($user,$out_trade_no);
	    if($ret['errcode'] == -1)
		{
			sendError($ret['msg']);
			return;
		}
		unset($ret['errcode']);
		unset($ret['msg']);
		addmsg(1036,$ret);
	}

	/*
	*功能：我的订单--Approved的某条具体记录 ---Comfirm to Delivery
	*ateam.ticp.io:9112/1037?tk=56dc26c78d194953af37a839c9811834&d={"out_trade_no":"15173675259195pp0nm1tv5e"}
	*/
	public static function MyOrder_Approved_detail_confirm2Delivery()
	{
		require_once("Models/Model_Order.php");
			  
		$ret = array();
    	$user =  static::$user;
	    $out_trade_no = static::$_P['out_trade_no'];//订单号

    	$ret = Model_Order::confirm2Delivery($user,$out_trade_no);
	    if($ret['errcode'] == -1)
		{
			sendError($ret['msg']);
			return;
		}
		unset($ret['errcode']);
		unset($ret['msg']);
		addmsg(1037,$ret);
	}

	/*
	*功能：Agent Order--on progress列表
	*ateam.ticp.io:9112/1038?tk=56dc26c78d194953af37a839c9811834
	*/
	public static function agentorder_OnProgress()
	{
		require_once("Models/Model_Order.php");

		$ret = array();
    	$user =  static::$user;
    	$ret = Model_Order::agentorder_OnProgress($user);

 	// 	if($ret['errcode'] == -1)
		// {
		// 	sendError($ret['msg']);
		// 	return;
		// }
		unset($ret['errcode']);
		unset($ret['msg']);
		addmsg(1038,$ret);
	}

	/*
	*功能：Agent Order--on progress列表--某条具体记录
	*url:ateam.ticp.io:9112/1039?tk=56dc26c78d194953af37a839c9811834&d={"out_trade_no":"15173675259195pp0nm1tv5e"}
	*/
	public static function agentorder_OnProgress_detail()
	{
		require_once("Models/Model_Order.php");

		$ret = array();
    	$user =  static::$user;

	    $out_trade_no = static::$_P['out_trade_no'];//订单号


    	$ret = Model_Order::agentorder_OnProgress_detail($user,$out_trade_no);

 	// 	if($ret['errcode'] == -1)
		// {
		// 	sendError($ret['msg']);
		// 	return;
		// }
		unset($ret['errcode']);
		unset($ret['msg']);
		addmsg(1039,$ret);
	}

	/*
	*功能：Agent Order--on progress列表--某条具体记录----取消订单
	*url:http://ateam.ticp.io:9112/1040?tk=65d891c8f374b24c784f2e26786f884a&d={"out_trade_no":"订单号"}
	*/
	public static function agentorder_OnProgress_detail_cancel()
	{
		require_once("Models/Model_Order.php");
	    $out_trade_no = static::$_P['out_trade_no'];//

	    $ret = array();
    	$user =  static::$user;

		$ret = Model_Order::agentorder_OnProgress_detail_cancel($user,$out_trade_no);

 		if($ret['errcode'] == -1)
		{
			sendError($ret['msg']);
			return;
		}
		unset($ret['errcode']);
		unset($ret['msg']);
		addmsg(1040,$ret);

	}

	/*
	*功能：Agent Order--on progress列表--某条具体记录----确定订单
	*
	* url：http://ateam.ticp.io:9112/1041?tk=65d891c8f374b24c784f2e26786f884a&d={"out_trade_no":"订单号","cart":[{"cartid":"cart主键id","DeliveryQty":"审批数量","reason":"原因"}]}
	*/
	public  static function agentorder_OnProgress_detail_submit()
	{
		require_once("Models/Model_Order.php");

	    $out_trade_no = static::$_P['out_trade_no'];//
	    $cart = static::$_P['cart'];//

		$ret = array();
    	$user =  static::$user;

		$ret = Model_Order::agentorder_OnProgress_detail_submit($out_trade_no,$cart,$user);

 		if($ret['errcode'] == -1)
		{
			sendError($ret['msg']);
			return;
		}
		unset($ret['errcode']);
		unset($ret['msg']);
		addmsg(1041,$ret);
	}

	/*
	*功能：Agent Order--Approved列表
	*http://ateam.ticp.io:9112/1042?tk=65d891c8f374b24c784f2e26786f884a
	*/
	public static function agentorder_Approved()
	{
		require_once("Models/Model_Order.php");

		$ret = array();
    	$user =  static::$user;
    	$ret = Model_Order::agentorder_Approved($user);

 		if($ret['errcode'] == -1)
		{
			sendError($ret['msg']);
			return;
		}
		unset($ret['errcode']);
		unset($ret['msg']);
		addmsg(1042,$ret);
	}

	/*
	*功能：Agent Order--Approved列表--某条具体记录
	*http://ateam.ticp.io:9112/1043?tk=65d891c8f374b24c784f2e26786f884a&d={"out_trade_no":"订单号"}
	*/
	public  static function agentorder_Approved_detail()
	{
		require_once("Models/Model_Order.php");
	    $out_trade_no = static::$_P['out_trade_no'];//
		$ret = array();
    	$user =  static::$user;
    	$ret = Model_Order::agentorder_Approved_detail($user,$out_trade_no);

 		if($ret['errcode'] == -1)
		{
			sendError($ret['msg']);
			return;
		}
		unset($ret['errcode']);
		unset($ret['msg']);
		addmsg(1043,$ret);
	}

	/*
	*功能：我的订单--Delivering列表
	*ateam.ticp.io:9112/1044?tk=56dc26c78d194953af37a839c9811834&d={"page":"0"}
	*/
	public static function MyOrder_Delivering()
	{
		require_once("Models/Model_Order.php");
		if(static::$_P['page'])//分页： 第一页：0  
	    {
	    	$page = static::$_P['page'];
	    }else
	    {
	    	$page =0;//分页 第一页
	    }
		$ret = array();
    	$user =  static::$user;
    	$ret = Model_Order::MyOrder_Delivering($user,$page);

 	// 	if($ret['errcode'] == -1)
		// {
		// 	sendError($ret['msg']);
		// 	return;
		// }
		unset($ret['errcode']);
		unset($ret['msg']);
		addmsg(1044,$ret);
	}


	/*
	*功能：我的订单--Delivering列表--具体记录
	*http://ateam.ticp.io:9112/1045?tk=65d891c8f374b24c784f2e26786f884a&d={"out_trade_no":"订单号"}
	*/
	public  static function MyOrder_Delivering_detail()
	{
		require_once("Models/Model_Order.php");
	    $out_trade_no = static::$_P['out_trade_no'];//
		$ret = array();
    	$user =  static::$user;
    	$ret = Model_Order::MyOrder_Delivering_detail($user,$out_trade_no);

 		if($ret['errcode'] == -1)
		{
			sendError($ret['msg']);
			return;
		}
		unset($ret['errcode']);
		unset($ret['msg']);
		addmsg(1045,$ret);
	}

 
	/*
	*功能：我的订单--Complete列表
	*ateam.ticp.io:9112/1046?tk=56dc26c78d194953af37a839c9811834&d={"page":"0"}
	*/
	public static function MyOrder_Complete()
	{
		require_once("Models/Model_Order.php");
		if(static::$_P['page'])//分页： 第一页：0  
	    {
	    	$page = static::$_P['page'];
	    }else
	    {
	    	$page =0;//分页 第一页
	    }
		$ret = array();
    	$user =  static::$user;
    	$ret = Model_Order::MyOrder_Complete($user,$page);

 	// 	if($ret['errcode'] == -1)
		// {
		// 	sendError($ret['msg']);
		// 	return;
		// }
		unset($ret['errcode']);
		unset($ret['msg']);
		addmsg(1046,$ret);
	}


	/*
	*功能我的订单--Complete列表--具体记录
	*http://ateam.ticp.io:9112/1047?tk=65d891c8f374b24c784f2e26786f884a&d={"out_trade_no":"订单号"}
	*/
	public  static function MyOrder_Complete_detail()
	{
		require_once("Models/Model_Order.php");
	    $out_trade_no = static::$_P['out_trade_no'];//
		$ret = array();
    	$user =  static::$user;
    	$ret = Model_Order::MyOrder_Complete_detail($user,$out_trade_no);

 		if($ret['errcode'] == -1)
		{
			sendError($ret['msg']);
			return;
		}
		unset($ret['errcode']);
		unset($ret['msg']);
		addmsg(1047,$ret);
	}

	/*
	*功能：我的订单--Comfirm receipt
	*http://ateam.ticp.io:9112/1048?tk=65d891c8f374b24c784f2e26786f884a&d={"out_trade_no":"订单号"}
	*/
	public static function MyOrder_Comfirm_receipt()
	{
		require_once("Models/Model_Order.php");
	    $out_trade_no = static::$_P['out_trade_no'];//
		$ret = array();
    	$user =  static::$user;
    	$ret = Model_Order::MyOrder_Comfirm_receipt($user,$out_trade_no);

 		if($ret['errcode'] == -1)
		{
			sendError($ret['msg']);
			return;
		}
		unset($ret['errcode']);
		unset($ret['msg']);
		addmsg(1048,$ret);
	}


	/*
	*功能：我的订单--Comfirm receipt--提交评论
	*http://ateam.ticp.io:9112/1049?tk=65d891c8f374b24c784f2e26786f884a&d={"out_trade_no":"订单号","date_of_receipt":"收件日期","condition_of_goods":"评论Goods、Complain","comment":"具体评论"}
	*/
	public static function MyOrder_Comfirm_receipt_submit()
	{
		require_once("Models/Model_Order.php");
	    $out_trade_no = static::$_P['out_trade_no'];//

	    $arr['date_of_receipt'] = static::$_P['date_of_receipt'];//收件日期
	    $arr['condition_of_goods'] = static::$_P['condition_of_goods'];//评论Goods/Complain
	    $arr['comment'] = static::$_P['comment'];//具体评论

		$ret = array();
    	$user =  static::$user;
    	$ret = Model_Order::MyOrder_Comfirm_receipt_submit($user,$out_trade_no,$arr);

 		if($ret['errcode'] == -1)
		{
			sendError($ret['msg']);
			return;
		}
		unset($ret['errcode']);
		unset($ret['msg']);
		addmsg(1049,$ret);
	}

	/*
	*功能：Agent Order--Delivering列表
	*
	* http://ateam.ticp.io:9112/1050?tk=65d891c8f374b24c784f2e26786f884a
	*/
	public static function agentorder_Delivering()
	{
		require_once("Models/Model_Order.php");

		$ret = array();
    	$user =  static::$user;
    	$ret = Model_Order::agentorder_Delivering($user);

 		if($ret['errcode'] == -1)
		{
			sendError($ret['msg']);
			return;
		}
		unset($ret['errcode']);
		unset($ret['msg']);
		addmsg(1050,$ret);
	}

	/*
	*功能：Agent Order--Delivering列表--某条具体记录
	*http://ateam.ticp.io:9112/1051?tk=65d891c8f374b24c784f2e26786f884a&d={"out_trade_no":"订单号"}
	*/
	public static function agentorder_Delivering_detail()
	{
		require_once("Models/Model_Order.php");
	    $out_trade_no = static::$_P['out_trade_no'];//
		$ret = array();
    	$user =  static::$user;
    	$ret = Model_Order::agentorder_Delivering_detail($user,$out_trade_no);

 		if($ret['errcode'] == -1)
		{
			sendError($ret['msg']);
			return;
		}
		unset($ret['errcode']);
		unset($ret['msg']);
		addmsg(1051,$ret);
	}


	/*
	*功能：Agent Order--Complete列表
	*
	* http://ateam.ticp.io:9112/1052?tk=65d891c8f374b24c784f2e26786f884a
	*/
	public static function agentorder_Complete()
	{
		require_once("Models/Model_Order.php");

		$ret = array();
    	$user =  static::$user;
    	$ret = Model_Order::agentorder_Complete($user);

 		if($ret['errcode'] == -1)
		{
			sendError($ret['msg']);
			return;
		}
		unset($ret['errcode']);
		unset($ret['msg']);
		addmsg(1052,$ret);
	}

	/*
	*功能：Agent Order--Complete列表--某条记录
	*http://ateam.ticp.io:9112/1053?tk=65d891c8f374b24c784f2e26786f884a&d={"out_trade_no":"订单号"}
	*/
	public static function agentorder_Complete_detail()
	{
		require_once("Models/Model_Order.php");
	    $out_trade_no = static::$_P['out_trade_no'];//
		$ret = array();
    	$user =  static::$user;
    	$ret = Model_Order::agentorder_Complete_detail($user,$out_trade_no);

 		if($ret['errcode'] == -1)
		{
			sendError($ret['msg']);
			return;
		}
		unset($ret['errcode']);
		unset($ret['msg']);
		addmsg(1053,$ret);
	}



	/*
	*功能：Agent Order--Delivering列表--具体记录---Comfirm to Delivery
	*ateam.ticp.io:9112/1055?tk=56dc26c78d194953af37a839c9811834&d={"out_trade_no":"15173675259195pp0nm1tv5e"}
	*/
	public static function agentorder_Confirm2Delivery()
	{
		require_once("Models/Model_Order.php");
			  
		$ret = array();
    	$user =  static::$user;
	    $out_trade_no = static::$_P['out_trade_no'];//订单号

    	$ret = Model_Order::agentorder_Confirm2Delivery($user,$out_trade_no);
	    if($ret['errcode'] == -1)
		{
			sendError($ret['msg']);
			return;
		}
		unset($ret['errcode']);
		unset($ret['msg']);
		addmsg(1055,$ret);
	}

	/*
	*功能：Agent Order--Complete列表--某条记录---View Receipt
	*ateam.ticp.io:9112/1056?tk=56dc26c78d194953af37a839c9811834&d={"out_trade_no":"15173675259195pp0nm1tv5e"}
	*/
	public static function agentorder_Complete_detail_viewReceipt()
	{
		require_once("Models/Model_Order.php");
			  
		$ret = array();
    	$user =  static::$user;
	    $out_trade_no = static::$_P['out_trade_no'];//订单号

    	$ret = Model_Order::agentorder_Complete_detail_viewReceipt($user,$out_trade_no);
	   
		unset($ret['errcode']);
		unset($ret['msg']);
		addmsg(1056,$ret);
	}

	
	/*
	*功能：我的订单--查看评论
	*ateam.ticp.io:9112/1057?tk=56dc26c78d194953af37a839c9811834&d={"out_trade_no":"15173675259195pp0nm1tv5e"}
	*/
	public static function MyOrder_view_receipt()
	{
		require_once("Models/Model_Order.php");
			  
		$ret = array();
    	$user =  static::$user;
	    $out_trade_no = static::$_P['out_trade_no'];//订单号

    	$ret = Model_Order::MyOrder_view_receipt($out_trade_no);
	   
		unset($ret['errcode']);
		unset($ret['msg']);
		addmsg(1057,$ret);
	}

}
?>
	
