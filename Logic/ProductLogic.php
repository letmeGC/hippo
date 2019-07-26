<?php
class ProductLogic extends LogicBase
{
	protected static $_before=array(
    	"aes"=>array("include"=>array("all")),
	   // "checkLogin"=>array("include"=>array("all")),
    	);

	public static function product_detail(){
		require_once "Models/Model_Product.php";
		require_once "Models/Model_Choice.php";
		require_once "Models/Model_Product_ptype.php";
		$product_id = static::$_P['product_id'];

		$product = Model_Product::findOne($product_id);
		$product_type = Model_Product_ptype::selectOne('product_id=?',array($product->id));
		if($product_type){
			$ptype = $product_type->ptype_id;
		}
		$choice = Model_Choice::selectBysql('select c.* from choice c,product_choice pc where pc.choice_id = c.id and pc.product_id=?',array($product_id));

		

		$choice_ar = array();
		$choice_ar[] = array(
			'name'=>'Qty',
			'type'=>1,
			'param'=>[""],
		);
		foreach ($choice as $key => $value) {
			$c_temp = $value->toArray();
			$c_temp['param'] = explode(',', $c_temp['param']);
			$choice_ar[] = $c_temp;

		}
		$product_ar = $product->toArray();
		$product_ar['img'] = $product_ar['small_img'];
		$product_ar['product_id'] = $product_ar['id'];
		unset($product_ar['small_img']);
		$product_ar['imgs'] = explode(',', $product_ar['imgs']);
		$related_product = array();
		if($ptype){
			$related = Model_Product::selectBysql("select p.* from product p,product_ptype pp where p.id=pp.product_id and pp.ptype_id=? and p.id<>?  limit 10",array($ptype,$product->id));
			foreach ($related as $key => $value) {
				$temp = array(
					'img' => $value->small_img,
					'product_id' => $value->id,
					'product_name' => $value->product_name,
					'price_low' => $value->price_low,
					'price_mid' => $value->price_mid,
					'price_high' => $value->price_high
				);
				$related_product[] = $temp;
			}
		}
		$ret = array();
		$ret['product'] = $product_ar;
		$ret['choice'] = $choice_ar;
		$ret['related_product'] = $related_product;
		addmsg(1010,$ret);
	}
}
?>