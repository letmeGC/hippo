<?php
class Model_Orders_cart extends ActiveRecord
{
	public static $tablepre = "";
	protected static $class = __CLASS__;
	protected static $table;
	protected static $cinit = false;
	protected static $primaryKey = 'id';
	protected static $_desc = null;
	protected static $_numCol = null;


	//判断购物车商品的状态 是否处于购物车中
	public static function in_cart($cart_id_arr)
	{
		require_once("Models/Model_Cart.php");
		
		foreach ($cart_id_arr as $key => $value) {
			$sel = Model_Cart::selectOne("id=? and `status`=0 ",array($value));
			if($sel)
			{
				return true;
			}
		}
		return false;
	}

	public  static function create_order_cart($user,$out_trade_no,$cart_id_arr)
	{
		require_once("Models/Model_Cart.php");
		require_once("Models/Model_Product.php");
		$Qty_Order = 0;//件数
		$total_amount = 0;//总金额
		$type = $user->type;//1 master 2 sub 3 new 

		$ret =  array();
		foreach ($cart_id_arr as $key => $value) {
			$sel = Model_Cart::selectOne("id=? and `status`=0 ",array($value));
			if($sel)
			{
				$Qty_Order += $sel->num;

				$Model_Product = Model_Product::selectOne("id=?",array($sel->product_id));
				switch ($type) {//1 master 2 sub 3 new 
					case '1'://master
						$total_amount += $Model_Product->price_low * $sel->num;
						break;
					case '2':
						$total_amount += $Model_Product->price_mid * $sel->num;
						break;
					case '3':
						$total_amount += $Model_Product->price_mid * $sel->num;
						break;
				}

				$sel->status = 1;//购物车商品状态 0  未下单 ,  1已下单 2 移除购物车
				$sel->save();



				$self = self::create();
				$self->cart_id = $value;
				$self->out_trade_no = $out_trade_no;
				$self->save();
			}

			
		}
		if($Qty_Order>0)
		{
			require_once("Models/Model_Orders.php");
			$Model_Orders = Model_Orders::selectOne("out_trade_no=?",array($out_trade_no));
			$Model_Orders->Qty_Order =  $Qty_Order;//件数
			$Model_Orders->total_amount =  $total_amount;//订单总金额
			$Model_Orders->subtotal =  $Model_Orders->shippingfee + $Model_Orders->total_amount;//运费+ 订单总金额
			$Model_Orders->save();

			$ret['Qty_Order'] = $Qty_Order;//件数
			$ret['total_amount'] = $total_amount;//总金额
			$ret['shippingfee'] =  $Model_Orders->shippingfee;//运费
			$ret['subtotal'] = $Model_Orders->subtotal;//总金额
		}
		
		return $ret;

	}


	/*
	*功能：根据订单号，查询商品信息
	*@out_trade_no：订单号
	*/
	public static function Viewgoods($out_trade_no)
	{
		$ret = array();
		$sql = "select  tb2.name,tb2.small_img,tb2.price_low,tb2.price_mid,tb2.price_high,tb1.product_id,tb1.num,tb1.choicejson,tb1.`status`  from (SELECT  cart.product_id,cart.num,cart.choicejson,cart.`status`  FROM  orders_cart  LEFT JOIN cart   ON   cart.id = orders_cart.cart_id  WHERE   out_trade_no =? )as tb1
			LEFT JOIN   product  as tb2  on tb1.product_id =  tb2.id";
		$self = self::selectBySql($sql,array($out_trade_no));
		if($self)
		{
			foreach ($self as $key => $value) {
				$arr= array();
				$arr['name'] = $value->name; //产品名称
				$arr['small_img'] = $value->small_img; //产品图片
				$arr['price_low'] = $value->price_low; //产品价格(低)
				$arr['price_mid'] = $value->price_mid; //产品价格//中
				$arr['price_high'] = $value->price_high; //产品价格//高
				$arr['product_id'] = $value->product_id; //产品id
				$arr['num'] = $value->num; //购买产品数量
				$arr['choicejson'] = $value->choicejson; //用户选择的商品属性构成的json格式数据
				$arr['choice'] = json_decode($value->choicejson);//用户选择的商品属性
				$arr['status'] = $value->status; //
				$ret[] = $arr;
			}
		}
		return $ret;
	}


	/*
	*功能：根据订单号，查询商品信息
	*@out_trade_no：订单号
	*/
	public static function Viewgoods1($out_trade_no)
	{
		$ret = array();
		$sql = "select  tb2.name,tb2.small_img,tb2.price_low,tb2.price_mid,tb2.price_high,tb1.product_id,tb1.num_order,tb1.num,tb1.choicejson,tb1.`status`  from (SELECT  cart.product_id,cart.num_order,cart.num,cart.choicejson,cart.`status`  FROM  orders_cart  LEFT JOIN cart   ON   cart.id = orders_cart.cart_id  WHERE   out_trade_no =? )as tb1
			LEFT JOIN   product  as tb2  on tb1.product_id =  tb2.id";
		$self = self::selectBySql($sql,array($out_trade_no));
		if($self)
		{
			foreach ($self as $key => $value) {
				$arr= array();
				$arr['name'] = $value->name; //产品名称
				$arr['small_img'] = $value->small_img; //产品图片
				$arr['price_low'] = $value->price_low; //产品价格(低)
				$arr['price_mid'] = $value->price_mid; //产品价格//中
				$arr['price_high'] = $value->price_high; //产品价格//高
				$arr['product_id'] = $value->product_id; //产品id
				$arr['DeliveryQty'] = $value->num; //该商品---MD审核的数量
				$arr['OrderQty'] = $value->num_order; //该商品 ---用户下单的数量
				$arr['choicejson'] = $value->choicejson; //用户选择的商品属性构成的json格式数据
				$arr['choice'] = json_decode($value->choicejson);//用户选择的商品属性
				$arr['status'] = $value->status; //
				$ret[] = $arr;
			}
		}
		return $ret;
	}

	/*
	*
	*功能：删除订单时，将商品放会购物车
	*@out_trade_no：订单号
	*/
	public static function On_Progress_cancel($out_trade_no)
	{
		require_once("Models/Model_Cart.php");
		$self = self::select("out_trade_no=?",array($out_trade_no));
		if($self)
		{
			foreach ($self as $key => $value) {
				$cart_id = $value->cart_id;

				$model = Model_Cart::selectOne(" id=?",array($cart_id));
				if($model)
				{
					$model->status=0;
					$model->save();
				}

			}
			self::delete("out_trade_no=?",array($out_trade_no));
		}
		lgj("end-------------------------------On_Progress_cancelOn_Progress_cancelOn_Progress_cancel");

	}



	//根据订单号 ，返回该订单的所有cart id 以数组的形式返回
	public static function reCartid($out_trade_no)
	{
		$self = self::select("out_trade_no=?",array($out_trade_no));
		$ret= array();

		if($self)
		{
			foreach ($self as $key => $value) {
				$ret[] = $value->cart_id;
			}
		}
		return $ret;
	}
}
?>
