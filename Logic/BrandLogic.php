<?php
class BrandLogic extends LogicBase
{
	protected static $_before=array(
    	"aes"=>array("include"=>array("all")),
		//"checkLogin"=>array("include"=>array("all")),
    	);

	public static function product_list(){//TODO 根据不同规则排序
		require_once "Models/Model_Brand.php";
		require_once "Models/Model_Product.php";
		$brand_id = static::$_P['brand_id'];
		$ptype_id = static::$_P['ptype_id'];
		$page = static::$_P['page'];
		$page = $page?$page:1;
		
		//TODO SQL优化
		$ptypes = Model_Product::selectBysql("select DISTINCT(pt.id) as ptype,pt.name as name from product p,product_ptype pp,ptype pt where p.id = pp.product_id and pp.ptype_id = pt.id and p.brand=$brand_id");

		$ret['ptypes'] = array();
		foreach ($ptypes as $key => $value) {
			$ret['ptypes'][] = array(
				'name'=>$value->name,
				'ptype_id'=>$value->ptype,
			);
		}

		$limit = 10;
		$count = $limit*$page-$limit;
		if($ptype_id){
			$ptype_sql = "and pp.ptype_id=$ptype_id";
		}else{
			$ptype_sql = "";
		}
		$products = Model_Product::selectBysql("select p.* from product p,brand b,product_ptype pp where p.brand = b.id and p.id=pp.product_id and b.id=? $ptype_sql limit $count,$limit",array($brand_id));
		$brand = Model_Brand::findOne($brand_id);
		$ret['brand_name'] = $brand->name;
		$ret['products'] = array();
		foreach ($products as $key1 => $value1) {
			$temp = array(
				'product_id'=>$value1->id,
				'img'=>$value1->small_img,
				'name'=>$value1->name,
				'price_low' => $value1->price_low,
				'price_mid' => $value1->price_mid,
				'view_type' => $value1->view_type,
				// 'price'=>$value1->get_user_price(),//TODO根据不同用户给不同价格
			);
			$ret['products'][] = $temp;
		}
		addmsg(1008,$ret);
	}

	public static function brand_index(){
		require_once "Models/Model_Brand.php";
		require_once "Models/Model_Product.php";
		require_once "Models/Model_Tag.php";
		require_once("cnf/const.php");
		$product_type = static::$_P['ptype'];
		$tag_id = static::$_P['tag_id'];

		$ret = array();
		$brands = Model_Brand::select();
		$ret['brands'] = array();
		if($tag_id){
			$tag = Model_Tag::findOne($tag_id);
			$ret['tag_name'] = $tag->name;
			$ret['tag_type'] = $tag->type;
		}
		
		foreach ($brands as $key => $value) {//TODO缓存
			$temp = array();
			$temp['brand_id'] = $value->id;
			$temp['brand_name'] = $value->name;
			$temp['brand_desc'] = $value->desc;
			$temp['brand_img'] = Consts::imgurlhttp.$value->brand_img;
			$temp['products'] = array();
			$products = array();

			if($product_type){//查询某种类型
				$products = Model_Product::selectBysql("select p.* from product p,brand b,product_ptype pp where p.id=pp.product_id and p.brand = b.id and b.id=? and pp.ptype_id=? limit 10",array($value->id,$product_type));
			}else{
				$products = Model_Product::selectBysql("select p.* from product p,brand b where p.brand = b.id and b.id=? limit 10",array($value->id));
			}
			lgj($products);

			//tag的角标
			require_once("cnf/const.php");
			$Tag_image	= array();
			if($arr =Str::$tag_Img[$tag_id])
			{
				foreach ($arr as $key => $value) {
					$Tag_image[$key] = Consts::imgurlhttp.$value;
				}
			}

			foreach ($products as $key1 => $value1) {
				$temp_pro = array();
				$temp_pro['product_id'] = $value1->id;
				$temp_pro['img'] = $value1->small_img;

				$temp_pro['Tag_image'] =$Tag_image;//tag的角标


				$temp_pro['name'] = $value1->name;
				$temp_pro['price_low'] = $value1->price_low;
				$temp_pro['price_mid'] = $value1->price_mid;
				$temp_pro['price_high'] = $value1->price_high;
				// $temp_pro['price'] = $value1->get_user_price();
				$temp['products'][] = $temp_pro;
			}
			if($temp['products']){
				$ret['brands'][] = $temp;
			}
		}
		addmsg(1007,$ret);
		// $products = Model_Product::selectBysql("select p.* from product p,brand b where p.brand = b.id and b.id=? limit 10",array($brand_id));
		// $brand = Model_Brand::findOne($brand_id);
		// $ret['brand_name'] = $brand->name;
		// $ret['brand_desc'] = $brand->desc;
		// $ret['products'] = array();
		// foreach ($products as $key1 => $value1) {
		// 	$temp = array(
		// 		'product_id'=>$value1->id,
		// 		'img'=>$value1->small_img,
		// 		'name'=>$value1->name,
		// 		'price'=>$value1->get_user_price(),//TODO根据不同用户给不同价格
		// 	);
		// 	$ret['products'][] = $temp;
		// }
	}

	// public static function tag_banner(){
	// 	require_once "Models/Model_Banner.php";
	// 	$tag_id = static::$_P['tag_id'];
	// 	$banners = Model_Banner::select('position_type=2 and position_target=?',array($tag_id));
	// 	$ret = array();
	// 	foreach ($banners as $key => $value) {
	// 		$ret[] = $value->toArray();
	// 	}
	// 	addmsg(1006,$ret);
	// }

	
}
?>