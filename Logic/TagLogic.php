<?php
class TagLogic extends LogicBase
{
	protected static $_before=array(
    	"aes"=>array("include"=>array("all")),
    	// "checkLogin"=>array("include"=>array("all")),
    	);

	public static function lt()
	{
		require_once "Models/Model_Tag.php";
		require_once "Models/Model_Tag_tab.php";
		$tab_id = static::$_P['tab_id'];


	//	$tabs = Model_Tag::select('tab_id=?',array($tab_id));
		$tag_id = Model_Tag_tab::select('tab_id=?',array($tab_id));
        $tidArray = array();
        foreach ($tag_id as $kk=>$vv){
             $tidArray[]= $vv->tag_id;
        }
        $tid_str =implode(", ",$tidArray);

		$tags = Model_Tag::selectBysql("select * from `tag` where `id` in({$tid_str})");
		$ret = array();
		foreach ($tags as $key => $value) {
			 $ret[] = $value->toArray();
		}
		addmsg(1004,$ret);
	}

	public static function tag_detail(){

		require_once "Models/Model_Tag.php";
		require_once "Models/Model_Product.php";
		require_once "Models/Model_Selltime.php";
		$tag_id = static::$_P['tag_id'];
		$page = static::$_P['page'];
		$page = $page?$page:1;

		$limit = 10;
		$count = $limit*$page-$limit;
		$products = Model_Product::selectBysql("select p.* from product p,product_tag pt where p.id = pt.product_id and pt.tag_id=? limit $count,$limit",array($tag_id));
		$tag = Model_Tag::findOne($tag_id);
		$ret['tag_name'] = $tag->name;
		$ret['tag_type'] = $tag->type;

		if($tag->type != 1){
			//TODO SQL优化
			$ptypes = Model_Product::selectBysql("select DISTINCT(pt.id) as ptype,pt.name as name from product p,product_ptype pp,ptype pt,product_tag ptg where ptg.product_id = p.id and p.id = pp.product_id and pp.ptype_id = pt.id and ptg.tag_id=$tag_id ");
			$ret['ptypes'] = array();
			foreach ($ptypes as $key => $value) {
				$ret['ptypes'][] = array(
					'name'=>$value->name,
					'ptype_id'=>$value->ptype,
				);
			}
		}

		
			//tag的角标
			require_once("cnf/const.php");
			$Tag_image	= array();
			if($arr =Str::$tag_Img[$tag_id])
			{
				foreach ($arr as $key => $value) {
					$Tag_image[$key] = Consts::imgurlhttp.$value;
				}
			}



		if($tag->type == 1||$tag->type == 6){
			$ret['products'] = array();
			foreach ($products as $key1 => $value1) {
				$temp = array(
					'product_id'=>$value1->id,
					'img'=>$value1->small_img,

					'Tag_image'=>$Tag_image,//tag的角标
					
					'name'=>$value1->name,
					'price_low' => $value1->price_low,
					'price_mid' => $value1->price_mid,
					'price_high' => $value1->price_high,
					// 'price'=>$value1->get_user_price(),//TODO根据不同用户给不同价格
				);
				$ret['products'][] = $temp;
			}
		}elseif($tag->type == 3){
			$ret['selltime'] = array();
			$selltimes = Model_Selltime::select('type=1');
			foreach ($selltimes as $key1 => $value1) {
				$st = json_decode($value1->param,true);
				$begin = date('H:i',$st['begin']);
				$end = date('H:i',$st['end']);
				$products = Model_Product::selectBysql("select p.* from product p,product_tag pt where p.id = pt.product_id and pt.tag_id=? and selltime=? limit 10",array($tag_id,$value1->id));
				if($products){
					foreach ($products as $key2 => $value2) {
						$temp = array(
							'product_id'=>$value2->id,
							'img'=>$value2->small_img,
							'name'=>$value2->name,
							'price_low' => $value2->price_low,
							'price_mid' => $value2->price_mid,
							'price_high' => $value2->price_high,
							// 'price'=>$value2->get_user_price(),//TODO根据不同用户给不同价格
						);
						$ret['selltime'][$begin.'-'.$end]['limit'] = $begin.'-'.$end;
						$ret['selltime'][$begin.'-'.$end]['products'][] = $temp;
					}
				}
			}
		}elseif($tag->type == 4){
			$ret['selltime'] = array();
			$selltimes = Model_Selltime::select('type=2');
			foreach ($selltimes as $key1 => $value1) {
				$weekarray=array("Monday","Tuesday","Wednesday","Thursday","Friday","Saturday","Sunday");
				$day = $weekarray[$value1->param-1];
				$products = Model_Product::selectBysql("select p.* from product p,product_tag pt where p.id = pt.product_id and pt.tag_id=? and selltime=? limit 10",array($tag_id,$value1->id));
				if($products){
					foreach ($products as $key2 => $value2) {
						$temp = array(
							'product_id'=>$value2->id,
							'img'=>$value2->small_img,
							'name'=>$value2->name,
							'price_low' => $value2->price_low,
							'price_mid' => $value2->price_mid,
							'price_high' => $value2->price_high,
							// 'price'=>$value2->get_user_price(),//TODO根据不同用户给不同价格
						);
						$ret['selltime'][$day]['limit'] = $day;
						$ret['selltime'][$day]['products'][] = $temp;
					}
				}
			}
		}elseif($tag->type == 5){
			$ret['selltime'] = array();
			$selltimes = Model_Selltime::select('type=3');
			foreach ($selltimes as $key1 => $value1) {
				$montharray=array("January","February","March","April","May","June","July","Aguest","September","October","November","December");
				$month = $montharray[$value1->param-1];
				$products = Model_Product::selectBysql("select p.* from product p,product_tag pt where p.id = pt.product_id and pt.tag_id=? and selltime=? limit 10",array($tag_id,$value1->id));
				if($products){
					foreach ($products as $key2 => $value2) {
						$temp = array(
							'product_id'=>$value2->id,
							'img'=>$value2->small_img,
							'name'=>$value2->name,
							'price_low' => $value2->price_low,
							'price_mid' => $value2->price_mid,
							'price_high' => $value2->price_high,
							// 'price'=>$value2->get_user_price(),//TODO根据不同用户给不同价格
						);
						$ret['selltime'][$month]['limit'] = $month;
						$ret['selltime'][$month]['products'][] = $temp;
					}
				}
			}
		}
		if($ret['selltime']){
			$ret['selltime'] = array_values($ret['selltime']);
		}
		
		addmsg(1009,$ret);
	}
}
?>