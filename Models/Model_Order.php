<?php
class Model_Order extends ActiveRecord
{

	/*
	*功能：选择购物车的商品 下单
	*
	* @uid:用户id
	* @cart_id_arr：购物车主键组成的数组
	*/
	public static function  preOrder($user,$cart_id_arr)
	{
		$ret = array();
		
		$uid = $user->id;

		
		require_once("Models/Model_Char.php");
		//返回用户的Master Dealer
		$ret['master_dealer'] = Model_Char::MasterDealer($user);
		//返回用户默认地址
		require_once("Models/Model_Address.php");
		$ret['Address'] = Model_Address::defaultAddress($uid);
		//显示订单中 物品的信息，若超过两条，则只显示两条
		require_once("Models/Model_Cart.php");
		$in_arr = array();
		//若超过两条，则只显示两条
		if(count($cart_id_arr)>2)
		{
			$in_arr[] = $cart_id_arr[0];
			$in_arr[] = $cart_id_arr[1];
		}else
		{
			$in_arr = $cart_id_arr;
		}
		$ret['goods'] = Model_Cart::choicedetailCart($user,$in_arr,1);
		$ret['cost'] = Model_Cart::choicedetailCart($user,$cart_id_arr,2);
		

		$ret['errcode'] =  0;//
	    $ret['msg'] =  "success";//

	    return $ret;
	}


	/*
	*功能：用户下单
	*
	* @uid:用户id
	* @cart_id_arr：购物车主键组成的数组
	* @payment_menthod：支付渠道
	*/
	public static function Order($user,$master_dealer,$address_id,$cart_id_arr,$payment_menthod)
	{
		require_once("Models/Model_Orders_cart.php");

		$uid = $user->id;
		$type = $user->type;
		$ret= array();
		$create_order_arr = array();
		$create_order_arr['uid'] = $uid;//用户id
		$create_order_arr['payment_menthod'] = $payment_menthod;//支付渠道
		$create_order_arr['master_dealer'] = $master_dealer;//master_dealer
		$create_order_arr['status'] = 0;//状态  0、On Progress  1、Approved  2、delivering 3、Complete  4、cancel
		$create_order_arr['type'] = $type;//1 master 2 sub 3 new
		$create_order_arr['address_id'] = $address_id;//快递地址主键id

	
		require_once("Models/Model_Orders.php");
		//判断商品是否在购物车中
		if(!Model_Orders_cart::in_cart($cart_id_arr))
		{
			$ret['errcode'] =-1;
			$ret['msg'] ='fail';
			return $ret;
		}

		$out_trade_no = Model_Orders::create_order($create_order_arr);
		if($out_trade_no)
		{
			$ret = Model_Orders_cart::create_order_cart($user,$out_trade_no,$cart_id_arr);
			$ret['payment_menthod'] = $payment_menthod;//支付渠道1、CBD    2、COD   3、OTP
			$ret['out_trade_no'] = $out_trade_no;//订单号
			$ret['Date'] = date("d M Y",time());;//31 Jan 2018

			//master_dealer
			require_once("Models/Model_Char.php");
			$ret['MasterDealer'] = Model_Char::get_nickname($master_dealer);

			//通知有未读记录
			require_once("Models/Model_Orders_read.php");
			if($re_uid = Model_Char::get_uid($master_dealer))
			{
				Model_Orders_read::add_orders_read($re_uid,1,5);
			}


			$ret['errcode'] =0;
			$ret['msg'] ='success';
		}else
		{
			$ret['errcode'] =-1;
			$ret['msg'] ='fail';
		}
		return $ret;


	}



	/*
	*功能：我的订单--On Progress
	*@user :用户信息的对象
	*@page :分页： 第一页：0
	*/
	public static function MyOrder_On_Progress($user,$page)
	{
		require_once("Models/Model_Orders.php");
		$uid = $user->id;//用户id
		$ret = Model_Orders::MyOrder_On_Progress($uid,$page);
		if($ret)
		{

			$ret['errcode'] =0;
			$ret['msg'] ='success';
		}else
		{
			$ret['errcode'] =-1;
			$ret['msg'] ='没记录了';
		}
		return $ret;
	}


	/*
	*功能：我的订单--On Progress   的某条具体记录
	*@user :用户信息的对象
	*@out_trade_no :订单号
	*/
	public static function MyOrder_On_Progress_detail($user,$out_trade_no)
	{
		require_once("Models/Model_Orders.php");
		$ret = Model_Orders::MyOrder_On_Progress_detail($user,$out_trade_no);
		if($ret)
		{

			$ret['errcode'] =0;
			$ret['msg'] ='success';
		}else
		{
			$ret['errcode'] =-1;
			$ret['msg'] ='fail';
		}
		return $ret;
	}


	//取消订单
	public static function On_Progress_cancel($out_trade_no)
	{
		require_once("Models/Model_Orders.php");
		require_once("Models/Model_Orders_cart.php");
		$ret = array();
		$Model_Orders = Model_Orders::selectOne("out_trade_no=? and status=0",array($out_trade_no));
		if($Model_Orders)
		{
			//将订单状态改为取消
			$Model_Orders->status = 4;//状态  0、On Progress  1、Approved  2、delivering 3、Complete  4、cancel
			$Model_Orders->save();

			//将下单的商品，放回购物车
			Model_Orders_cart::On_Progress_cancel($out_trade_no);
			

			lgj("1111111111111111111111111111111111111111111111111");
			//减少用户未读记录数 
			require_once("Models/Model_Orders_read.php");
			require_once("Models/Model_Char.php");
			$master_dealer = $Model_Orders->master_dealer;
			$uid = Model_Char::get_uid($master_dealer);
			Model_Orders_read::minus_orders_read($uid,1,5);
			lgj("22222222222222222222222222222222222222222222");

			$ret['errcode'] =0;
			$ret['msg'] ='success';
		}else
		{
			$ret['errcode'] =-1;
			$ret['msg'] ='fail';
		}
		return $ret;
	}


	/*我的订单--On Progress  取消订单
	*@user :用户信息的对象
	*@out_trade_no :订单号
	*/
	public static function MyOrder_On_Progress_cancel($user,$out_trade_no)
	{
		return self::On_Progress_cancel($out_trade_no);
	}



	/*
	*功能：我的订单--Approved
	*@user :用户信息的对象
	*@page :分页： 第一页：0
	*/
	public static function MyOrder_Approved($user,$page)
	{
		require_once("Models/Model_Orders.php");
		$uid = $user->id;//用户id
		$ret = Model_Orders::MyOrder_Approved($uid,$page);
		if($ret)
		{

			$ret['errcode'] =0;
			$ret['msg'] ='success';
		}else
		{
			$ret['errcode'] =-1;
			$ret['msg'] ='没记录了';
		}
		return $ret;
	}

	
	/*
	*功能：我的订单--Approved的某条具体记录
	*@user :用户信息的对象
	*@out_trade_no :订单号
	*/
	public static function MyOrder_Approved_detail($user,$out_trade_no)
	{
		require_once("Models/Model_Orders.php");
		$ret = Model_Orders::MyOrder_Approved_detail($user,$out_trade_no);
		if($ret)
		{

			$ret['errcode'] =0;
			$ret['msg'] ='success';
		}else
		{
			$ret['errcode'] =-1;
			$ret['msg'] ='fail';
		}
		return $ret;
	}

	/*
	*功能：的订单--Approved的某条具体记录 ---Comfirm to Delivery
	*/
	public static function confirm2Delivery($user,$out_trade_no)
	{
		require_once("Models/Model_Orders.php");

		$ret = array();
		$uid = $user->id;
		$Model = Model_Orders::selectOne("uid=? and  out_trade_no=? and status=1",array($uid,$out_trade_no));
		if($Model)
		{
			$Model->status=2;//状态  0、On Progress  1、Approved  2、delivering 3、Complete  4、cancel
			$Model->save();
			
			
			require_once("Models/Model_Orders_read.php");
			require_once("Models/Model_Char.php");
			if($re_uid = Model_Char::get_uid($Model->master_dealer))
			{
				//通知MD有未读记录
				Model_Orders_read::add_orders_read($re_uid,1,7);
			}

			//减少SD 未读记录
			Model_Orders_read::minus_orders_read($uid,1,2);



			$ret['errcode'] =0;
			$ret['msg'] ='success';

		}else
		{
			$ret['errcode'] =-1;
			$ret['msg'] ='fail';
		}
		return $ret;
	}

	/*
	*功能：Agent Order--on progress列表
	*/
	public static function agentorder_OnProgress($user)
	{
		$ret = array();
		require_once("Models/Model_Orders.php");
		$ret = Model_Orders::agentorder_OnProgress($user);
		if($ret)
		{

			$ret['errcode'] =0;
			$ret['msg'] ='success';
		}else
		{
			$ret['errcode'] =-1;
			$ret['msg'] ='fail';
		}
		
		return $ret;
	}

	/*
	*功能：Agent Order--on progress列表--某条具体记录
	*@user的对象
	*@out_trade_no:订单号
	*/
	public  static function agentorder_OnProgress_detail($user,$out_trade_no)
	{
		$ret = array();
		require_once("Models/Model_Orders.php");
		$ret = Model_Orders::agentorder_OnProgress_detail($user,$out_trade_no);
		if($ret)
		{

			$ret['errcode'] =0;
			$ret['msg'] ='success';
		}else
		{
			$ret['errcode'] =-1;
			$ret['msg'] ='fail';
		}
		
		return $ret;
	}

	/*
	*功能：Agent Order--on progress列表--某条具体记录----取消订单
	*@user：用户对象
	*@out_trade_no：订单号
	*/
	public static function agentorder_OnProgress_detail_cancel($user,$out_trade_no)
	{
		$uid = $user->id;

		//取消订单
		$ret = self::On_Progress_cancel($out_trade_no);

		// if($ret['errcode'] ==0)
		// {
		// 	//减少用户未读记录数 
		// 	require_once("Models/Model_Orders_read.php");
		// 	Model_Orders_read::minus_orders_read($uid,1,5);
		// }
		
		return $ret;
	}

	/*
	*@out_trade_no:订单号
	*@cart：数组
	*      元素：{"cartid":"cart主键id","DeliveryQty":"审批数量","reason":"原因"}
	*/
	public static function agentorder_OnProgress_detail_submit($out_trade_no,$cart,$user)
	{
		// $uid = $user->id;
		$ret = array();
		require_once("Models/Model_Orders.php");
		$re = Model_Orders::agentorder_OnProgress_detail_submit($out_trade_no,$cart,$user);
		if($re)
		{
			// //减少用户未读记录数 
			// require_once("Models/Model_Orders_read.php");
			// Model_Orders_read::minus_orders_read($uid,1,5);

			$ret['errcode'] =0;
			$ret['msg'] ='success';
		}else
		{
			$ret['errcode'] =-1;
			$ret['msg'] ='fail';
		}
		
		return $ret;
	}


	/*
	*功能：Agent Order--on progress列表
	*/
	public static function agentorder_Approved($user)
	{
		$ret = array();
		require_once("Models/Model_Orders.php");
		$ret = Model_Orders::agentorder_Approved($user);
		if($ret)
		{

			$ret['errcode'] =0;
			$ret['msg'] ='success';
		}else
		{
			$ret['errcode'] =-1;
			$ret['msg'] ='fail';
		}
		
		return $ret;
	}

	/*
	*Agent Order--Approved列表--某条具体记录
	*/
	public  static function agentorder_Approved_detail($user,$out_trade_no)
	{
		require_once("Models/Model_Orders.php");
		$ret = Model_Orders::agentorder_Approved_detail($user,$out_trade_no);
		if($ret)
		{

			$ret['errcode'] =0;
			$ret['msg'] ='success';
		}else
		{
			$ret['errcode'] =-1;
			$ret['msg'] ='fail';
		}
		return $ret;
	}

	/*
	*我的订单 --Delivering列表
	*/
	public  static function MyOrder_Delivering($user,$page)
	{
		$uid = $user->id;
		require_once("Models/Model_Orders.php");
		$ret = Model_Orders::MyOrder_Delivering($uid,$page);
		if($ret)
		{

			$ret['errcode'] =0;
			$ret['msg'] ='success';
		}else
		{
			$ret['errcode'] =-1;
			$ret['msg'] ='fail';
		}
		return $ret;
	}


	/*
	*功能：我的订单--Delivering列表--具体记录
	*/
	public  static function MyOrder_Delivering_detail($user,$out_trade_no)
	{
		require_once("Models/Model_Orders.php");
		$ret = Model_Orders::MyOrder_Delivering_detail($user,$out_trade_no);
		if($ret)
		{

			$ret['errcode'] =0;
			$ret['msg'] ='success';
		}else
		{
			$ret['errcode'] =-1;
			$ret['msg'] ='fail';
		}
		return $ret;
	}

	/*
	*我的订单--Complete列表
	*/
	public  static function MyOrder_Complete($user,$page)
	{
		$uid = $user->id;
		require_once("Models/Model_Orders.php");
		$ret = Model_Orders::MyOrder_Complete($uid,$page);
		if($ret)
		{

			$ret['errcode'] =0;
			$ret['msg'] ='success';
		}else
		{
			$ret['errcode'] =-1;
			$ret['msg'] ='fail';
		}
		return $ret;
	}
	

	/*
	*功能：我的订单--Complete列表--具体记录
	*/
	public  static function MyOrder_Complete_detail($user,$out_trade_no)
	{
		require_once("Models/Model_Orders.php");
		$ret = Model_Orders::MyOrder_Complete_detail($user,$out_trade_no);
		if($ret)
		{

			$ret['errcode'] =0;
			$ret['msg'] ='success';
		}else
		{
			$ret['errcode'] =-1;
			$ret['msg'] ='fail';
		}
		return $ret;
	}


	/*
	*功能：我的订单--Comfirm receipt
	*/
	public  static function MyOrder_Comfirm_receipt($user,$out_trade_no)
	{
		require_once("Models/Model_Orders.php");
		$ret = Model_Orders::MyOrder_Comfirm_receipt($user,$out_trade_no);
		if($ret)
		{
			$ret['errcode'] =0;
			$ret['msg'] ='success';
		}else
		{
			$ret['errcode'] =-1;
			$ret['msg'] ='fail';
		}
		return $ret;
	}


	/*
	*功能：我的订单--Comfirm receipt--提交评论
	*/
	public  static function MyOrder_Comfirm_receipt_submit($user,$out_trade_no,$arr)
	{
		require_once("Models/Model_Orders.php");
		$ret = Model_Orders::MyOrder_Comfirm_receipt_submit($user,$out_trade_no,$arr);
		if($ret)
		{
			$ret['errcode'] =0;
			$ret['msg'] ='success';
		}else
		{
			$ret['errcode'] =-1;
			$ret['msg'] ='fail';
		}
		return $ret;
	}


	/*
	*功能：Agent Order--Delivering列表
	*/
	public static function agentorder_Delivering($user)
	{
		$ret = array();
		require_once("Models/Model_Orders.php");
		$ret = Model_Orders::agentorder_Delivering($user);
		if($ret)
		{

			$ret['errcode'] =0;
			$ret['msg'] ='success';
		}else
		{
			$ret['errcode'] =-1;
			$ret['msg'] ='fail';
		}
		
		return $ret;
	}

	/*
	*功能：Agent Order--Delivering列表--某条具体记录
	*/
	public static function agentorder_Delivering_detail($user,$out_trade_no)
	{
		$ret = array();
		require_once("Models/Model_Orders.php");
		$ret = Model_Orders::agentorder_Delivering_detail($user,$out_trade_no);
		if($ret)
		{

			$ret['errcode'] =0;
			$ret['msg'] ='success';
		}else
		{
			$ret['errcode'] =-1;
			$ret['msg'] ='fail';
		}
		
		return $ret;
	}


	/*
	*功能：Agent Order--Complete列表
	*/
	public static function agentorder_Complete($user)
	{
		$ret = array();
		require_once("Models/Model_Orders.php");
		$ret = Model_Orders::agentorder_Complete($user);
		if($ret)
		{

			$ret['errcode'] =0;
			$ret['msg'] ='success';
		}else
		{
			$ret['errcode'] =-1;
			$ret['msg'] ='fail';
		}
		
		return $ret;
	}


	/*
	*功能：Agent Order--Delivering列表--某条具体记录
	*/
	public static function agentorder_Complete_detail($user,$out_trade_no)
	{
		$ret = array();
		require_once("Models/Model_Orders.php");
		$ret = Model_Orders::agentorder_Complete_detail($user,$out_trade_no);
		if($ret)
		{

			$ret['errcode'] =0;
			$ret['msg'] ='success';
		}else
		{
			$ret['errcode'] =-1;
			$ret['msg'] ='fail';
		}
		
		return $ret;
	}


	/*
	*功能：Agent Order--Delivering列表--具体记录---Comfirm to Delivery
	*/
	public static function agentorder_Confirm2Delivery($user,$out_trade_no)
	{
		require_once("Models/Model_Orders.php");

		$ret = array();
		$uid = $user->id;
		$Model = Model_Orders::selectOne(" out_trade_no=? and status=2",array($out_trade_no));
		if($Model)
		{
			$Model->status=3;//状态  0、On Progress  1、Approved  2、delivering 3、Complete  4、cancel
			$Model->save();
			
			
			require_once("Models/Model_Orders_read.php");
			require_once("Models/Model_Char.php");
			//减少MD 用户未读记录数 
			Model_Orders_read::minus_orders_read($user->id,1,7);
			//通知SD用户  有未读记录
			Model_Orders_read::add_orders_read($Model->uid,1,4);



			$ret['errcode'] =0;
			$ret['msg'] ='success';

		}else
		{
			$ret['errcode'] =-1;
			$ret['msg'] ='fail';
		}
		return $ret;
	}

	/*
	**功能：Agent Order--Complete列表--某条记录---View Receipt
	*/
	public static function agentorder_Complete_detail_viewReceipt($user,$out_trade_no)
	{
		require_once("Models/Model_Orders.php");
		$ret = Model_Orders::agentorder_Complete_detail_viewReceipt($user,$out_trade_no);
		if($ret)
		{
			$ret['errcode'] =0;
			$ret['msg'] ='success';
		}else
		{
			$ret['errcode'] =-1;
			$ret['msg'] ='fail';
		}
		return $ret;
	}


	//功能：我的订单--查看评论
	public static function MyOrder_view_receipt($out_trade_no)
	{
		require_once("Models/Model_Orders.php");
		$ret = Model_Orders::view_receipt($out_trade_no);
		if($ret)
		{
			$ret['errcode'] =0;
			$ret['msg'] ='success';
		}else
		{
			$ret['errcode'] =-1;
			$ret['msg'] ='fail';
		}
		return $ret;
	}

}