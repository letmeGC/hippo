<?php
class Model_Orders extends ActiveRecord
{
	public static $tablepre = "";
	protected static $class = __CLASS__;
	protected static $table;
	protected static $cinit = false;
	protected static $primaryKey = 'id';
	protected static $_desc = null;
	protected static $_numCol = null;


	/*
	*创建记录
	*/
	public  static function create_order($arr)
	{
		$self =  self::create();
		$self->uid = $arr['uid'];//用户id
		$self->payment_menthod = $arr['payment_menthod'];//支付渠道
		$self->status = $arr['status'];//状态  0、On Progress  1、Approved  2、delivering 3、Complete  4、cancel
		$self->type = $arr['type'];//
		$self->master_dealer = $arr['master_dealer'];
		$self->address_id = $arr['address_id'];//
		$self->out_trade_no = self::create_out_trade_no();//平台订单号
		$self->save();
		return $self->out_trade_no;
	}


	//商户订单号，商户网站订单系统中唯一订单号，必填
	public  static function  create_out_trade_no()
	{
        $randstr="0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
        for($i=0;$i<10;$i++){$key .= $randstr{mt_rand(0,32)}; }
        $out_trade_no =  time().rand(1000,9999).$key;
    	return $out_trade_no;
	}



	/*查询用户的MyOrder_On_Progress
	*uid：用户id
	*page：分页   第一页 ：0
	*/ 
	public static function MyOrder_On_Progress($uid,$page)
	{

		return self::MyOrder_Common($uid,$page,0);
		// $limit = 10;//每页10条记录
		// $limit_start = $page *  $limit;

		// require_once("Models/Model_Char.php");
		// $ret = array();
		// $sql = "select *  from orders where `status`=0 and uid=? limit ".$limit_start.",".$limit."";//状态status  0、On Progress  1、Approved  2、delivering 3、Complete  4、cancel
		// $self =  self::selectBySql($sql,array($uid));
		// if($self)
		// {
		// 	foreach ($self as $key => $value) {
		// 		$arr =  array();
		// 		$arr['out_trade_no'] = $value->out_trade_no;//订单号
		// 		$arr['Date'] = date("d M Y",strtotime($value->created_at));//日期
		// 		$arr['payment_menthod'] = $value->payment_menthod ;//支付渠道1、CBD    2、COD   3、OTP
		// 		$arr['MasterDealer'] = Model_Char::get_nickname($value->master_dealer);
		// 		$arr['status'] = $value->status;//状态  0、On Progress  1、Approved  2、delivering 3、Complete  4、cancel
		// 		$ret[] = $arr;
		// 	}
		// }
		// return $ret;
	}


	/*
	*查询，我的订单  On Progress, Approved 等
	*@uid：用户id
	*@page：分页   第一页 ：0
	*@status :  0、On Progress  1、Approved  2、delivering 3、Complete  
	*/
	public static function MyOrder_Common($uid,$page,$status)
	{
		$limit = 10;//每页10条记录
		$limit_start = $page *  $limit;

		require_once("Models/Model_Char.php");
		$ret = array();
		$sql = "select *  from orders where `status`=".$status." and uid=? limit ".$limit_start.",".$limit."";//状态status  0、On Progress  1、Approved  2、delivering 3、Complete  4、cancel
		$self =  self::selectBySql($sql,array($uid));
		if($self)
		{
			foreach ($self as $key => $value) {
				$arr =  array();
				$arr['out_trade_no'] = $value->out_trade_no;//订单号
				$arr['Date'] = date("d M Y",strtotime($value->created_at));//日期
				$arr['payment_menthod'] = $value->payment_menthod ;//支付渠道1、CBD    2、COD   3、OTP
				$arr['MasterDealer'] = Model_Char::get_nickname($value->master_dealer);
				$arr['No_Resi'] = $value->No_Resi ;//订单号
				$arr['status'] = $value->status;//状态  0、On Progress  1、Approved  2、delivering 3、Complete  4、cancel
				$ret[] = $arr;
			}
		}
		return $ret;
	}

	/*
	**功能：我的订单--On Progress   的某条具体记录
	*@user :用户信息的对象
	*@out_trade_no :订单号
	*/
	public  static function MyOrder_On_Progress_detail($user,$out_trade_no)
	{
		require_once("Models/Model_Char.php");
		require_once("Models/Model_Orders_cart.php");
		$ret =  array();
		$uid = $user->id;
		$self = self::selectOne("out_trade_no=? and uid=? and status=0",array($out_trade_no,$uid));
		if($self)
		{
			$ret['master_dealer'] = Model_Char::get_nickname($self->master_dealer);//master  dealer、
			$ret['total_amount'] = $self->total_amount;//商品总金额
			$ret['shippingfee'] = $self->shippingfee;//运费
			$ret['subtotal'] = $self->subtotal;//商品总金额 + 运费
			$ret['payment_menthod'] = $self->payment_menthod;//支付渠道1、CBD    2、COD   3、OTP
			$ret['out_trade_no'] = $self->out_trade_no;//订单号
			$ret['Date'] = date("d M Y",strtotime($self->created_at));//日期
			
			$ret['goods']= array();//存放具体商品信息

			//根据订单号，获得具体商品信息
			$ret['goods'] = Model_Orders_cart::Viewgoods($out_trade_no);
		}
		return $ret;
	}



	/*功能：我的订单， 显示approved列表
	*uid：用户id
	*page：分页   第一页 ：0
	*/ 
	public static function MyOrder_Approved($uid,$page)
	{

		return self::MyOrder_Common($uid,$page,1);
	}



	/*
	**功能：我的订单--Approved的某条具体记录
	*@user :用户信息的对象
	*@out_trade_no :订单号
	*/
	public  static function MyOrder_Approved_detail($user,$out_trade_no)
	{
		require_once("Models/Model_Char.php");
		require_once("Models/Model_Orders_cart.php");
		$ret =  array();
		$uid = $user->id;
		$self = self::selectOne("out_trade_no=? and uid=? and  status=1",array($out_trade_no,$uid));
		if($self)
		{
			$ret['master_dealer'] = Model_Char::get_nickname($self->master_dealer);//master  dealer、
			$ret['total_amount'] = $self->total_amount;//商品总金额
			$ret['shippingfee'] = $self->shippingfee;//运费
			$ret['subtotal'] = $self->subtotal;//商品总金额 + 运费
			$ret['payment_menthod'] = $self->payment_menthod;//支付渠道1、CBD    2、COD   3、OTP
			$ret['out_trade_no'] = $self->out_trade_no;//订单号

			$ret['Date'] = date("d M Y",strtotime($self->created_at));//日期
			
			$ret['goods']= array();//存放具体商品信息

			//根据订单号，获得具体商品信息
			$ret['goods'] = Model_Orders_cart::Viewgoods($out_trade_no);
		}
		return $ret;
	}

	/*
	*功能：Agent Order--on progress列表
	*/
	public  static function agentorder_OnProgress($user)
	{
		$dealer_code = $user->dealer_code;
		return self::agent_list($dealer_code,0);
	}

	//根据master_dealer  ，查询对应需要审核的记录列表
	//master_dealer:
	//status:状态  0、On Progress  1、Approved  2、delivering 3、Complete
	public  static function agent_list($master_dealer,$status)
	{
		$ret = array();
		$self = self::select("master_dealer=? and status=?",array($master_dealer,$status));
		if($self)
		{
			foreach ($self as $key => $value) {
				$arr =  array();
				$arr['out_trade_no'] = $value->out_trade_no;//订单号
				$arr['Date'] = date("d M Y",strtotime($value->created_at));//日期
				$arr['payment_menthod'] = $value->payment_menthod ;//支付渠道1、CBD    2、COD   3、OTP
				$arr['SubDealer'] = Model_Char::get_nickname1($value->uid);
				$arr['status'] = $value->status;//状态  0、On Progress  1、Approved  2、delivering 3、Complete  4、cancel
				$ret[] = $arr;
			}
		}
		return $ret;
	}



	/*
	*功能：Agent Order--on progress列表--某条具体记录
	**@user的对象
	*@out_trade_no:订单号
	*/
	public static function agentorder_OnProgress_detail($user,$out_trade_no)
	{
		$dealer_code = $user->dealer_code;
		$self = self::selectOne("out_trade_no=?  and master_dealer=? and  status=0",array($out_trade_no,$dealer_code));
		$ret =  array();
		if($self)
		{
			$ret['out_trade_no'] = $out_trade_no;
			$ret['Date'] = date("d M Y",strtotime($self->created_at));
			$ret['payment_menthod'] = $self->payment_menthod ;//支付渠道1、CBD    2、COD   3、OTP
			$ret['SubDealer'] = Model_Char::get_nickname1($self->uid);
			$ret['status'] = $self->status;//状态  0、On Progress  1、Approved  2、delivering 3、Complete  4、cancel
			$ret['total_amount'] = $self->total_amount;//商品总金额
			$ret['shippingfee'] = $self->shippingfee;//运费
			$ret['subtotal'] = $self->subtotal;//商品总金额 + 运费
			$ret['data']= self::AgentGoods($out_trade_no);
		}
		
		return $ret;
	}


	public static function AgentGoods($out_trade_no)
	{
		$ret = array();
		require_once("Models/Model_Orders_cart.php");
		require_once("Models/Model_Orders_cancel_reasons.php");
		$reasons = Model_Orders_cancel_reasons::reasons_list();

		$sql = "SELECT tb2.name,tb2.small_img,tb2.price_low,tb2.price_mid,tb2.price_high,tb1.cart_id,tb1.uid,tb1.num,tb1.num_order,tb1.choicejson   FROM (SELECT product_id,id as cart_id,uid,num,num_order,choicejson   FROM  cart WHERE  id in(SELECT cart_id  FROM  orders_cart  WHERE   out_trade_no =?))
			as tb1
			LEFT JOIN 
			product as tb2
			on 
			tb1.product_id = tb2.id";
		$model = Model_Orders_cart::selectBySql($sql,array($out_trade_no));
		if($model)
		{
			foreach ($model as $key => $value) {
				$arr = array();
				$arr['name'] = $value->name;//商品名称
				$arr['small_img'] = $value->small_img;//商品的图片
				$arr['price_low'] = $value->price_low;//商品的价格(低)
				$arr['price_mid'] = $value->price_mid;//商品的价格（中）
				$arr['price_high'] = $value->price_high;//商品的价格(高)
				$arr['cart_id'] = $value->cart_id;//购物车id
				$arr['uid'] = $value->uid;//用户id
				$arr['DeliveryQty'] = $value->num;//审核的数量
				$arr['OrderQty'] = $value->num_order;//用户下单的数量
				$arr['choicejson'] = $value->choicejson;//json格式的商品属性
				$arr['choice'] = explode(",",$value->choicejson);//商品属性
				$arr['reasons'] = $reasons;
				$ret[] = $arr;
			}
		}
		return $ret;
	}

	/*
	*@out_trade_no:订单号
	*@cart：数组
	*      元素：{"cartid":"cart主键id","DeliveryQty":"审批数量","reason":"原因"}
	*/
	public static function agentorder_OnProgress_detail_submit($out_trade_no,$cart,$user)
	{
		require_once("Models/Model_Cart.php");
		$self = self::selectOne("out_trade_no=?  and  status=0",array($out_trade_no));
		if($self)
		{
			$self->status=1;//状态  0、On Progress  1、Approved  2、delivering 3、Complete  4、cancel 
			$type = $self->type;//用户类型：1 master 2 sub 3 new 
			
			//根据订单号 ，返回该订单的所有cart id 以数组的形式返回
			require_once("Models/Model_Orders_cart.php");
			$cartidArr = Model_Orders_cart::reCartid($out_trade_no);


			foreach ($cart as $key => $v) {
				//判断cartid 是否在此订单中
				if(in_array($v['cartid'],$cartidArr))
				{
					//更新购物车的审核数量，以及记录原因
					$model = Model_Cart::selectOne("id=? and  status=1",array($v['cartid']));
					if($model)
					{
						$model->num = $v['DeliveryQty'];
						$model->reasons = $v['reason'];
						$model->save();
					}
				}
			}
			$self->total_amount =Model_Cart::re_total_amount($out_trade_no,$type);
			$self->Qty_Order =Model_Cart::re_Qty_Order($out_trade_no);
			$self->subtotal = $self->total_amount + $self->shippingfee;//商品总金额 + 运费
			$self->save();

			
			require_once("Models/Model_Orders_read.php");
			//减少MD 用户未读记录数 
			Model_Orders_read::minus_orders_read($user->id,1,5);
			//通知SD用户  有未读记录
			Model_Orders_read::add_orders_read($self->uid,1,2);
			return true;
		}
		return false;
	}



	/*
	*功能：Agent Order--on progress列表
	*/
	public  static function agentorder_Approved($user)
	{
		$dealer_code = $user->dealer_code;
		return self::agent_list($dealer_code,1);
	}


	/*
	*功能：Agent Order--Approved列表--某条具体记录
	*/
	public static function agentorder_Approved_detail($user,$out_trade_no)
	{
		require_once("Models/Model_Char.php");
		require_once("Models/Model_Orders_cart.php");
		$ret =  array();
		$uid = $user->id;
		$self = self::selectOne("out_trade_no=?  and status=1",array($out_trade_no));
		if($self)
		{
			$ret['SubDealer'] = Model_Char::get_nickname1($self->uid);//master  dealer、
			$ret['total_amount'] = $self->total_amount;//商品总金额
			$ret['shippingfee'] = $self->shippingfee;//运费
			$ret['subtotal'] = $self->subtotal;//商品总金额 + 运费
			$ret['payment_menthod'] = $self->payment_menthod;//支付渠道1、CBD    2、COD   3、OTP
			$ret['out_trade_no'] = $self->out_trade_no;//订单号

			$ret['Date'] = date("d M Y",strtotime($self->created_at));//日期
			
			$ret['goods']= array();//存放具体商品信息

			//根据订单号，获得具体商品信息
			$ret['goods'] = Model_Orders_cart::Viewgoods1($out_trade_no);
		}
		return $ret;

	}
	
	/*
	*
	*我的订单 --Delivering列表
	*
	*/
	public static function MyOrder_Delivering($uid,$page)
	{
		return self::MyOrder_Common($uid,$page,2);

	}



	/*
	*功能：我的订单--Delivering列表--具体记录
	*/
	public static function MyOrder_Delivering_detail($user,$out_trade_no)
	{
		require_once("Models/Model_Char.php");
		require_once("Models/Model_Orders_cart.php");
		$ret =  array();
		$uid = $user->id;
		$self = self::selectOne("out_trade_no=?  and status=2",array($out_trade_no));
		if($self)
		{
			$ret['master_dealer'] = Model_Char::get_nickname($self->master_dealer);//master  dealer、
			$ret['total_amount'] = $self->total_amount;//商品总金额
			$ret['shippingfee'] = $self->shippingfee;//运费
			$ret['subtotal'] = $self->subtotal;//商品总金额 + 运费
			$ret['payment_menthod'] = $self->payment_menthod;//支付渠道1、CBD    2、COD   3、OTP
			$ret['out_trade_no'] = $self->out_trade_no;//订单号
			$ret['Date'] = date("d M Y",strtotime($self->created_at));//日期
			$ret['type_of_delivery'] = $self->type_of_delivery;//Type of delivery
			$ret['delivery_service'] = $self->delivery_service;//Delivery Service
			$ret['No_Resi'] = $self->No_Resi;//NO. Resi
			
			$ret['goods']= array();//存放具体商品信息

			//根据订单号，获得具体商品信息
			$ret['goods'] = Model_Orders_cart::Viewgoods1($out_trade_no);
		}
		return $ret;
	}


	/*
	*
	*我的订单 --Complete列表
	*
	*/
	public static function MyOrder_Complete($uid,$page)
	{
		return self::MyOrder_Common($uid,$page,3);
	}



	/*
	*功能：我的订单--Complete列表--具体记录
	*/
	public  static function MyOrder_Complete_detail($user,$out_trade_no)
	{
		require_once("Models/Model_Char.php");
		require_once("Models/Model_Orders_cart.php");
		$ret =  array();
		$uid = $user->id;
		$self = self::selectOne("out_trade_no=? and uid=? and  status=3",array($out_trade_no,$uid));
		if($self)
		{
			$ret['master_dealer'] = Model_Char::get_nickname($self->master_dealer);//master  dealer、
			$ret['total_amount'] = $self->total_amount;//商品总金额
			$ret['shippingfee'] = $self->shippingfee;//运费
			$ret['subtotal'] = $self->subtotal;//商品总金额 + 运费
			$ret['payment_menthod'] = $self->payment_menthod;//支付渠道1、CBD    2、COD   3、OTP
			$ret['out_trade_no'] = $self->out_trade_no;//订单号
			$ret['Date'] = date("d M Y",strtotime($self->created_at));//日期
			
			$ret['No_Resi'] = $self->No_Resi;//发票
			$ret['Qty_Order'] = $self->Qty_Order;//多少件商品
			$ret['type_of_delivery'] = $self->type_of_delivery;//Type of delivery
			$ret['delivery_service'] = $self->delivery_service;//Delivery Service

			$ret['goods']= array();//存放具体商品信息

			//根据订单号，获得具体商品信息
			$ret['goods'] = Model_Orders_cart::Viewgoods($out_trade_no);
		}
		return $ret;
	}



	/*
	*功能：我的订单--Comfirm receipt
	*/
	public static function MyOrder_Comfirm_receipt($user,$out_trade_no)
	{
		require_once("Models/Model_Char.php");
		require_once("Models/Model_Orders_cart.php");
		$ret =  array();
		$uid = $user->id;
		$self = self::selectOne("out_trade_no=? and uid=? and  status=3",array($out_trade_no,$uid));
		if($self)
		{
			$self->comfirm_receipt = 1;//更改状态为已签收
			$self->save();

			$ret['from'] = Model_Char::get_nickname($self->master_dealer);//master  dealer、
			$ret['total_amount'] = $self->total_amount;//商品总金额
			$ret['shippingfee'] = $self->shippingfee;//运费
			$ret['subtotal'] = $self->subtotal;//商品总金额 + 运费
			$ret['payment_menthod'] = $self->payment_menthod;//支付渠道1、CBD    2、COD   3、OTP
			$ret['out_trade_no'] = $self->out_trade_no;//订单号
			$ret['Date'] = date("d M Y",strtotime($self->created_at));//日期
			$ret['No_Resi'] = $self->No_Resi;//发票
			$ret['Qty_Order'] = $self->Qty_Order;//多少件商品
			$ret['type_of_delivery'] = $self->type_of_delivery;//Type of delivery
			$ret['delivery_service'] = $self->delivery_service;//Delivery Service
			
		}
		return $ret;
	}

	/*
	*我的订单--Comfirm receipt--提交评论
	*/
	public static function MyOrder_Comfirm_receipt_submit($user,$out_trade_no,$arr)
	{
		require_once("Models/Model_Char.php");
		require_once("Models/Model_Orders_cart.php");
		$ret =  array();
		$uid = $user->id;
		$self = self::selectOne("out_trade_no=? and uid=? and  status=3",array($out_trade_no,$uid));
		if($self)
		{
			$self->date_of_receipt = $arr['date_of_receipt'] ;//收件日期
	    	$self->condition_of_goods = $arr['condition_of_goods']  ;//评论Goods/Complain
	        $self->comment =  $arr['comment'] ;//具体评论
	        $self->save();

			$ret = self::view_receipt($out_trade_no);
		}
		return $ret;
	}


    public static function view_receipt($out_trade_no)
    {
    	$ret= array();
		$self = self::selectOne("out_trade_no=?  and  status=3",array($out_trade_no));
		if($self)
		{
			$ret['from'] = Model_Char::get_nickname($self->master_dealer);//master  dealer、
			$ret['total_amount'] = $self->total_amount;//商品总金额
			$ret['shippingfee'] = $self->shippingfee;//运费
			$ret['subtotal'] = $self->subtotal;//商品总金额 + 运费
			$ret['payment_menthod'] = $self->payment_menthod;//支付渠道1、CBD    2、COD   3、OTP
			$ret['out_trade_no'] = $self->out_trade_no;//订单号
			$ret['Date'] = date("d M Y",strtotime($self->created_at));//日期
			$ret['No_Resi'] = $self->No_Resi;//发票
			$ret['Qty_Order'] = $self->Qty_Order;//多少件商品
			$ret['type_of_delivery'] = $self->type_of_delivery;//Type of delivery
			$ret['delivery_service'] = $self->delivery_service;//Delivery Service
			
			$ret['date_of_receipt'] = $self->date_of_receipt ;//收件日期
		    $ret['condition_of_goods'] = $self->condition_of_goods;//评论Goods/Complain
		    $ret['comment'] = $self->comment;//具体评论
		}
		return $ret;
    }


	/*
	*功能：Agent Order--Delivering列表
	*/
	public  static function agentorder_Delivering($user)
	{
		$dealer_code = $user->dealer_code;
		return self::agent_list($dealer_code,2);
	}

	/*
	*功能：Agent Order--Delivering列表--某条具体记录
	*/
	public static function agentorder_Delivering_detail($user,$out_trade_no)
	{
		require_once("Models/Model_Char.php");
		require_once("Models/Model_Orders_cart.php");
		$ret =  array();
		$uid = $user->id;
		$self = self::selectOne("out_trade_no=?  and status=2",array($out_trade_no));
		if($self)
		{
			$ret['SubDealer'] = Model_Char::get_nickname1($self->uid);//master  dealer、
			$ret['total_amount'] = $self->total_amount;//商品总金额
			$ret['shippingfee'] = $self->shippingfee;//运费
			$ret['subtotal'] = $self->subtotal;//商品总金额 + 运费
			$ret['payment_menthod'] = $self->payment_menthod;//支付渠道1、CBD    2、COD   3、OTP
			$ret['out_trade_no'] = $self->out_trade_no;//订单号
			$ret['Date'] = date("d M Y",strtotime($self->created_at));//日期
			
			$ret['goods']= array();//存放具体商品信息

			//根据订单号，获得具体商品信息
			$ret['goods'] = Model_Orders_cart::Viewgoods1($out_trade_no);
		}
		return $ret;
	}


	/*
	*功能：Agent Order--Complete列表
	*/
	public  static function agentorder_Complete($user)
	{
		$dealer_code = $user->dealer_code;
		return self::agent_list($dealer_code,3);
	}

	/*
	*功能：Agent Order--Delivering列表--某条具体记录
	*/
	public static function agentorder_Complete_detail($user,$out_trade_no)
	{
		require_once("Models/Model_Char.php");
		require_once("Models/Model_Orders_cart.php");
		$ret =  array();
		$uid = $user->id;
		$self = self::selectOne("out_trade_no=?  and status=3",array($out_trade_no));
		if($self)
		{
			$ret['SubDealer'] = Model_Char::get_nickname1($self->uid);//master  dealer、
			$ret['total_amount'] = $self->total_amount;//总金额
			$ret['shippingfee'] = $self->shippingfee;//运费
			$ret['subtotal'] = $self->subtotal;//商品总金额 + 运费
			$ret['payment_menthod'] = $self->payment_menthod;//支付渠道1、CBD    2、COD   3、OTP
			$ret['out_trade_no'] = $self->out_trade_no;//订单号
			$ret['Date'] = date("d M Y",strtotime($self->created_at));//日期
			$ret['type_of_delivery'] = $self->type_of_delivery;//Type of Delivery
			$ret['delivery_service'] = $self->delivery_service;//Delivery Service
			$ret['No_Resi'] = $self->No_Resi;//发票
			$ret['Qty_Order'] = $self->Qty_Order;//订单商品数量
			$ret['goods']= array();//存放具体商品信息

			//根据订单号，获得具体商品信息
			$ret['goods'] = Model_Orders_cart::Viewgoods1($out_trade_no);
		}
		return $ret;
	}




	/*
	**功能：Agent Order--Complete列表--某条记录---View Receipt
	*
	*/
	public static function agentorder_Complete_detail_viewReceipt($user,$out_trade_no)
	{
		require_once("Models/Model_Char.php");
		require_once("Models/Model_Orders_cart.php");
		$ret =  array();
		$uid = $user->id;
		$self = self::selectOne("out_trade_no=?  and  status=3",array($out_trade_no));
		if($self)
		{
			$ret['from'] = Model_Char::get_nickname($self->master_dealer);//master  dealer、
			$ret['total_amount'] = $self->total_amount;//商品总金额
			$ret['shippingfee'] = $self->shippingfee;//运费
			$ret['subtotal'] = $self->subtotal;//商品总金额 + 运费
			$ret['payment_menthod'] = $self->payment_menthod;//支付渠道1、CBD    2、COD   3、OTP
			$ret['out_trade_no'] = $self->out_trade_no;//订单号
			$ret['Date'] = date("d M Y",strtotime($self->created_at));//日期
			$ret['No_Resi'] = $self->No_Resi;//发票
			$ret['Qty_Order'] = $self->Qty_Order;//多少件商品
			$ret['type_of_delivery'] = $self->type_of_delivery;//Type of delivery
			$ret['delivery_service'] = $self->delivery_service;//Delivery Service
			$ret['date_of_receipt'] = $self->date_of_receipt ;//收件日期
		    $ret['condition_of_goods'] = $self->condition_of_goods;//评论Goods/Complain
		    $ret['comment'] = $self->comment;//具体评论
		}
		return $ret;
	}

	
}