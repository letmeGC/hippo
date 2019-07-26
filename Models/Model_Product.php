<?php
class Model_Product extends ActiveRecord
{
	public static $tablepre = "";
	protected static $class = __CLASS__;
	protected static $table;
	protected static $cinit = false;
	protected static $primaryKey = 'id';
	protected static $_desc = null;
	protected static $_numCol = null;
	
	/**价格由低到高排序 */
	public function get_user_price($user_type){
		if(!$user_type){
		    $price_arr = array($this->price_high);
		}elseif($user_type == 1){
		    $price_arr = array($this->price_low,$this->price_mid,$this->price_high);
		}else{
		    $price_arr = array($this->price_mid,$this->price_high); 	
		}
		return $price_arr;
	}

	public function toArray()
	{
		require_once("cnf/const.php");

		$ret = parent::toArray();
		
		$ret['small_img'] = Consts::imgurlhttp.$ret['small_img'];
		return $ret;
	}

		//更新product_tag
	//@id : product 表id
	//@arr：tag id
	public static function update_product_tag($id,$arr)
	{
		require_once("Models/Model_Product_tag.php");
		if(empty($arr))
		{
			return '';
		}
		Model_Product_tag::delete("product_id=?",array($id));
		foreach ($arr as $key => $value) {
			$product_tag =  Model_Product_tag::create();
			$product_tag->product_id =$id;
			$product_tag->tag_id =$value;
			$product_tag->save();
		}
	}

	
	//展现product_tag
	public static function  show_product_tag($id=0)
	{
		require_once("Models/Model_Product_tag.php");
		require_once("Models/Model_Tag.php");
		$arr = array();
		if($id > 0)
		{
			$product_tag  = Model_Product_tag::select("product_id=?",array($id));
			if($product_tag)
			{
				foreach ($product_tag as $key => $value) {
					$arr[] = $value->tag_id;
				}
			}
		}

		$sql = "SELECT *  FROM  tag";
		$Model_Tag = Model_Tag::selectBySql($sql,array());

		if($Model_Tag)
		{ 
			$str = "";
			foreach ($Model_Tag as $key => $value) {
				if(in_array($value->id,$arr))
				{
					$str .=  "<input name=\"product_tag[]\" type=\"checkbox\"  checked=\"checked\"  value=\"".$value->id."\">&nbsp;&nbsp;".$value->name."<br>";
				}else
				{
					$str .=  "<input   name=\"product_tag[]\"  type=\"checkbox\" value=\"".$value->id."\">&nbsp;&nbsp;".$value->name."<br>";
				}
			}
			return $str;
		}else
		{
			return '';
		}
	}



	//更新product_ptype
	//@id : product 表id
	//@arr：ptype id
	public static function update_product_ptype($id,$arr)
	{
		require_once("Models/Model_Product_ptype.php");
		if(empty($arr))
		{
			return '';
		}
		Model_Product_ptype::delete("product_id=?",array($id));
		foreach ($arr as $key => $value) {
			$Product_ptype =  Model_Product_ptype::create();
			$Product_ptype->product_id =$id;
			$Product_ptype->ptype_id =$value;
			$Product_ptype->save();
		}
	}

	
	//展现product_ptype
	public static function  show_product_ptype($id=0)
	{
		require_once("Models/Model_Product_ptype.php");
		require_once("Models/Model_Ptype.php");
		$arr = array();
		if($id > 0)
		{
			$product_ptype  = Model_Product_ptype::select("product_id=?",array($id));
			if($product_ptype)
			{
				foreach ($product_ptype as $key => $value) {
					$arr[] = $value->ptype_id;
				}
			}
		}

		$sql = "SELECT *  FROM  ptype";
		$Model_Ptype = Model_Ptype::selectBySql($sql,array());

		if($Model_Ptype)
		{ 
			$str = "";
			foreach ($Model_Ptype as $key => $value) {
				if(in_array($value->id,$arr))
				{
					$str .=  "<input name=\"product_ptype[]\" type=\"checkbox\"  checked=\"checked\"  value=\"".$value->id."\">&nbsp;&nbsp;".$value->name."<br>";
				}else
				{
					$str .=  "<input   name=\"product_ptype[]\"  type=\"checkbox\" value=\"".$value->id."\">&nbsp;&nbsp;".$value->name."<br>";
				}
			}
			return $str;
		}else
		{
			return '';
		}
	}



	//更新product_choice
	//@id : product 表id
	//@arr：choice id
	public static function update_product_choice($id,$arr)
	{
		require_once("Models/Model_Product_choice.php");
		if(empty($arr))
		{
			return '';
		}
		
		Model_Product_choice::delete("product_id=?",array($id));

		foreach ($arr as $key => $value) {
			$Product_choice =  Model_Product_choice::create();
			$Product_choice->product_id =$id;
			$Product_choice->choice_id =$value;
			$Product_choice->save();
		}
	
	}



	//展现product_choice
	public static function  show_product_choice($id=0)
	{
		require_once("Models/Model_Product_choice.php");
		$arr = array();
		if($id > 0)
		{
			$product_choice  = Model_Product_choice::select("product_id=?",array($id));
			if($product_choice)
			{
				foreach ($product_choice as $key => $value) {
					$arr[] = $value->choice_id;
				}
			}
		}

		$sql = "SELECT *  FROM  choice";
		$self = self::selectBySql($sql,array());

		if($self)
		{ 
			$str = "";
			foreach ($self as $key => $value) {
				if(in_array($value->id,$arr))
				{
					$str .=  "<input name=\"product_choice[]\" type=\"checkbox\"  checked=\"checked\"  value=\"".$value->id."\">&nbsp;&nbsp;".$value->name.":".$value->param."<br>";
				}else
				{
					$str .=  "<input   name=\"product_choice[]\"  type=\"checkbox\" value=\"".$value->id."\">&nbsp;&nbsp;".$value->name.":".$value->param."<br>";
				}
			}
			return $str;
		}else
		{
			return '';
		}
	}



}
?>