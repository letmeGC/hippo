<?php
class Model_Cart extends ActiveRecord
{
	public static $tablepre = "";
	protected static $class = __CLASS__;
	protected static $table;
	protected static $cinit = false;
	protected static $primaryKey = 'id';
	protected static $_desc = null;
	protected static $_numCol = null;

	//商品状态 ，判断商品是否还有库存,是否已经下架等
	//@product_id:商品id
	//
	//return   
	//errcode 为200，则可以下单或加入购物车
	public static function product_status($product_id)
	{
		require_once("Models/Model_Product.php");
		$product = Model_Product::selectOne("id=?",array($product_id));
		if($product)
		{
			if($product->status==1)
			{
				$ret['errcode'] = -1;
				$ret['msg'] = '此商品已下架';
				return $ret;
			}

			if($product->inventory<1)
			{
				$ret['errcode'] = -1;
				$ret['msg'] = '此商品已卖完';
				return $ret;
			}

			$ret['errcode'] = 200;
			$ret['msg'] = 'success';
			return $ret;

		}else
		{
			$ret['errcode'] = -1;
			$ret['msg'] = '此商品不存在';
			return $ret;
		}
	}

	/*加入购物车
	*
	*@product_id:商品id
	*@num：商品数量
	*@uid：用户id
	*@choicejson:如存储鞋的颜色，大小等组成数组的json格式
	*/
	public static function addCart($product_id,$num,$uid,$choicejson)
	{	
		$ret = array();
		require_once("Models/Model_Product.php");
		$product_status = self::product_status($product_id);
		if($product_status['errcode'] == -1)//商品不允许加入购物车
		{
			$ret = $product_status;
			return $ret;
		}

		
		ksort($choicejson);
		$choicejson = json_encode($choicejson);
		
		
		$self1 = self::selectOne("product_id=? and uid=? and status=0  and choicejson=? ",array($product_id,$uid,$choicejson));
		if($self1)//若购物车有此商品，则将增加‘购物车商品’数量
		{
			$self1->num += $num;//用户购买的数量
			$self1->num_order += $num;//用户下单的数量
			$self1->save();
		}else//若购物车没有此商品，则，将商品加入购物车
		{
			$self = self::create();
			$self->product_id = $product_id;//商品id
			$self->num = $num;//购物车数量
			$self->num_order = $num;//用户下单的数量
			$self->uid = $uid;//用户id
			$self->choicejson = $choicejson;
			$self->status = 0;//购物车商品状态 0  未下单 ,  1已下单 2 移除购物车
			$self->save();
		}

		
		$ret['errcode'] = 0;
		$ret['msg'] = '添加购物车成功';
		return $ret;
	}

	/*
	*修改购物车商品数量
	*
	* @product_id：商品id
	* @num,修改后购物车商品的数量
	* @uid 用户id
	*/
	public static function midifyCart($product_id,$num,$uid,$choicejson)
	{
		$ret = array();
		require_once("Models/Model_Product.php");
		$product_status = self::product_status($product_id);
		if($product_status['errcode'] == -1)//商品不允许加入购物车
		{
			$ret = $product_status;
			return $ret;
		}

		ksort($choicejson);
		$choicejson = json_encode($choicejson);

		
		$self1 = self::selectOne("product_id=? and uid=? and status=0  and choicejson=? ",array($product_id,$uid,$choicejson));
		
		if($self1)
		{
			$self1->num = $num;
			$self1->choicejson = $choicejson;

			$self1->save();
			$ret['errcode'] = 0;
			$ret['msg'] = '成功';
		}else
		{
			$ret['errcode'] = -1;
			$ret['msg'] = '失败';
		}
		return $ret;
	} 

	/*
	*功能：移除购物车
	* @cart_id:购物车id
	* @uid 用户id
	*/
	public static function deleteCart($cart_id,$uid)
	{
		$self = self::selectOne("id=? and uid=? and status=0 ",array($cart_id,$uid));
		if($self)
		{
			$self->status = 2;//购物车商品状态 0  未下单 ,  1已下单 2 移除购物车
			$self->save();
			$ret['errcode'] = 0;
			$ret['msg'] = '成功';
		}else
		{
			$ret['errcode'] = -1;
			$ret['msg'] = '成功';
		}
		return $ret;
	}



	/*
	*用户购物车列表
	*
	*@用户id
	*/
	public static function detailCart($user)
	{
		$ret = array();
		$ret['data'] = array();
		$ret['sumprice'] = 0;//需要支付的价格

		$uid = $user->id;//用户id
		$type = $user->type;//用户类型  1 master 2 sub 3 new 

		$sql = "SELECT  p.name,p.small_img,p.price_low,p.price_mid,p.price_high , c.id as cart_id ,c.product_id,c.num,c.choicejson  FROM    cart as c LEFT JOIN  product  as p on p.id=c.product_id   WHERE c.uid=? and c.status=0";
		$high = 0;//high价格加总
		$mid = 0;//mid价格加总
		$low = 0;//low价格加总

		$self = self::selectBySql($sql, array($uid));
		if($self)
		{
			$arr = array();
			foreach ($self as $key => $value) {
				$arr[$key]['name'] = $value->name;//商品名称
				$arr[$key]['small_img'] = $value->small_img;//商品图片
				$arr[$key]['price_low'] = $value->price_low;//商品价格（低）
				$arr[$key]['price_mid'] = $value->price_mid;//商品价格（中）
				$arr[$key]['price_high'] = $value->price_high;//商品价格（高）
				$arr[$key]['cart_id'] = $value->cart_id;//购物车id
				$arr[$key]['product_id'] = $value->product_id;//产品id
				$arr[$key]['num'] = $value->num;//用户选择商品数量
				$arr[$key]['choicejson']  = $value->choicejson;//用户选择的商品属性构成的json格式数据
				$arr[$key]['choice'] = json_decode($value->choicejson);//用户选择的商品属性

				$high += $value->price_high  * $value->num;
				$mid += $value->price_mid * $value->num;
				$low += $value->price_low * $value->num;

			}
			switch ($type) {
				case '1'://最低价格
					$ret['sumprice'] = $low; 
					break;
				default:
					$ret['sumprice'] = $mid; 
					break;
			}
			$ret['data'] = $arr;//购物车数据
		}
		$ret['errcode'] =0;//成功
		$ret['msg'] ='success';//


		return $ret;
	}

	
	/*显示  购物车  将要下单的商品
	*user:用户对象
	*in_arr:要购物车id组成的数组
	*view_type:值为1 ，只需要显示商品列表，   值为2，只需要显示总价格，值为3，商品列表和价格都需要显示
	*/
	public  static function choicedetailCart($user,$in_arr,$view_type)
	{
		$uid = $user->id;//用户id
		$type = $user->type;//用户类型  1 master 2 sub 3 new 
		$ret =  array();
		if(empty($in_arr))
		{
			return $ret;
		}
		$in =  "(";
		foreach ($in_arr as $key => $value) {
			$in .= "'".$value."',";
		}
		$in = rtrim($in, ',').")";

		$sql = "SELECT   cart.id as cart_id,cart.product_id,cart.num,cart.choicejson,product.name,product.small_img,product.price_low,product.price_mid,product.price_high   FROM cart LEFT JOIN  product on product.id =  cart.product_id  WHERE  cart.id in".$in." and   cart.`status`=0   and  uid=? ";
		$self = self::selectBySql($sql,array($uid));
		if($self)
		{
			foreach ($self as $key => $value) {
				if(in_array($view_type,array(1,3)))//需要显示商品列表
				{
			
					$ret[$key]['name'] = $value->name;//商品名称
					$ret[$key]['small_img'] = $value->small_img;//商品图片
					$ret[$key]['price_low'] = $value->price_low;//商品价格（低）
					$ret[$key]['price_mid'] = $value->price_mid;//商品价格（中）
					$ret[$key]['price_high'] = $value->price_high;//商品价格（高）
					$ret[$key]['cart_id'] = $value->cart_id;//购物车id
					$ret[$key]['product_id'] = $value->product_id;//产品id
					$ret[$key]['num'] = $value->num;//用户选择商品数量
					$ret[$key]['choicejson']  = $value->choicejson;//用户选择的商品属性构成的json格式数据
					$ret[$key]['choice'] = json_decode($value->choicejson);//用户选择的商品属性
				}
				
				$high += $value->price_high  * $value->num;
				$mid += $value->price_mid * $value->num;
				$low += $value->price_low * $value->num;

			}

			if(in_array($view_type,array(2,3)))//需要统计商品价格
			{
				switch ($type) {
					case '1'://最低价格
						$ret['sumprice'] = $low; 
						break;
					default:
						$ret['sumprice'] = $mid; 
						break;
				}
			}
			
		}

		return $ret;
	}	

	//根据订单号  重新统计用户订单的商品数量
	public static function re_Qty_Order($out_trade_no)
	{
		$Qty_Order = 0;
		$sql = "SELECT num  FROM  cart   WHERE id in(SELECT cart_id  FROM   orders_cart WHERE  out_trade_no=?)";
		$self = self::selectBySql($sql,array($out_trade_no));
		if($self)
		{
			foreach ($self as $key => $value) {
				$Qty_Order += $value->num;
			}
		}
		return $Qty_Order;
	}

	//根据订单号 重新统计用户的总价格
	//@out_trade_no：订单号
	//@type:用户类型：1 master 2 sub 3 new
	public  static function  re_total_amount($out_trade_no,$type)
	{
		$total_amount = 0;//总价格

		//重新结算  总价格
			$sql = "SELECT tb2.name,tb2.small_img,tb2.price_low,tb2.price_mid,tb2.price_high,tb1.cart_id,tb1.uid,tb1.num,tb1.num_order,tb1.choicejson   FROM 
				(SELECT product_id,id as cart_id,uid,num,num_order,choicejson   FROM  cart WHERE  id in(SELECT cart_id  FROM  orders_cart  WHERE   out_trade_no =?))
							as tb1
							LEFT JOIN 
							product as tb2
							on 
							tb1.product_id = tb2.id
				";
			$mo = self::selectBySql($sql,array($out_trade_no));
			if($mo)
			{
				foreach ($mo as $key => $value) {
					$low = $value->price_low * $value->num;
					$mid = $value->price_mid * $value->num;
					switch ($type) {
						case '1'://最低价格  master
							$total_amount += $low;
							break;
						
						default:
							$total_amount += $mid;
							break;
					}

				}
			}

			return $total_amount;
	}
}
?>