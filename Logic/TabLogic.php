<?php
class TabLogic extends LogicBase
{
	protected static $_before=array(
    	"aes"=>array("include"=>array("all")),
    	// "checkLogin"=>array("include"=>array("all")),
    	);

	public static function lt()
	{
		require_once "Models/Model_Tab.php";
		$tabs = Model_Tab::select();
		$ret = array();
		foreach ($tabs as $key => $value) {
			$ret['tabs'][] = $value->toArray();
		}

		require_once "Models/Model_Banner.php";
		$banners = Model_Banner::select('position_type=1');
		foreach ($banners as $key => $value) {
			$ret['banners'][] = $value->toArray();
		}

		addmsg(1003,$ret);
	}

	public static function tab_data(){
		require_once "Models/Model_Banner.php";
		require_once "Models/Model_Tag.php";
		require_once "Models/Model_Product.php";
		require_once "Models/Model_Brand.php";
		require_once "Models/Model_Selltime.php";

		require_once "Models/Model_Tag_tab.php";

		$tab_id = static::$_P['tab_id'];
		//$tags = Model_Tag::select('tab_id=?',array($tab_id));
		//$tags = Model_Tag_tab::select('tab_id=?',array($tab_id));

        //$tag_id = Model_Tag_tab::select('tab_id=?',array($tab_id));
//        $tidArray = array();
//        foreach ($tag_id as $kk=>$vv){
//            $tidArray[]= $vv->tag_id;
//        }
//        $tid_str =implode(", ",$tidArray);
        $tags = Model_Tag::selectBysql("select tg.* from  (select * from `tag_tab` where `tab_id` ={$tab_id} ) as tgb LEFT join `tag` tg on tgb.tag_id = tg.id order by tgb.sort desc ");
        //$tags = Model_Tag::selectBysql("select * from `tag` where `id` in({$tid_str}) order by `sort` desc");

		$ret = array();
		foreach ($tags as $key => $value) {
			$ret[$key]['tag_id'] = $value->id;
			$ret[$key]['tag_type'] = $value->type;
			$ret[$key]['tag_name'] = $value->name;
			$banners = Model_Banner::select('position_type=2 and position_target=?',array($value->id));
			$ret[$key]['banners'] = array();
			foreach ($banners as $key1 => $value1) {
				$bt = $value1->toArray();
				$btag = Model_Tag::findOne($bt['redirect_target']);
				$bt['tag_type'] = $btag->type;
				$ret[$key]['banners'][] = $bt;
			}

			if($value->type==1||$value->type==6){//产品列表
				$products = Model_Product::selectBysql("select p.* from product p,product_tag pt where p.id = pt.product_id and pt.tag_id=? limit 10",array($value->id));
				$ret[$key]['products'] = array();
				foreach ($products as $key1 => $value1) {
					$temp = array(
						'product_id'=>$value1->id,
						'img'=>$value1->small_img,
						'name'=>$value1->name,
						'price_low'=>$value1->price_low,
						'price_mid'=>$value1->price_mid,
						'price_high'=>$value1->price_high,
						'view_type'=>$value1->view_type
					);
					$ret[$key]['products'][] = $temp;
				}
			}elseif($value->type==2){//品牌列表
				$brands = Model_Brand::selectBysql("select b.* from brand b,tag_brand tb where b.id = tb.brand_id and tb.tag_id=? limit 10",array($value->id));
				$ret[$key]['brands'] = array();
				foreach ($brands as $key1 => $value1) {
					$temp = array(
						'brand_id'=>$value1->id,
						'img'=>$value1->img,
					);
					$ret[$key]['brands'][] = $temp;
				}
			}elseif($value->type==3){//限时销售
				$ret[$key]['selltime'] = array();
				$selltimes = Model_Selltime::select('type=1');
				foreach ($selltimes as $key1 => $value1) {
					$st = json_decode($value1->param,true);
					$begin = date('H:i',$st['begin']);
					$end = date('H:i',$st['end']);
					$products = Model_Product::selectBysql("select p.* from product p,product_tag pt where p.id = pt.product_id and pt.tag_id=? and selltime=? limit 10",array($value->id,$value1->id));
					if($products){
						foreach ($products as $key2 => $value2) {
							$temp = array(
								'product_id'=>$value2->id,
								'img'=>$value2->small_img,
								'name'=>$value2->name,
								'price_low'=>$value2->price_low,
								'price_mid'=>$value2->price_mid,
								'price_high'=>$value2->price_high,
								'view_type'=>$value2->view_type
							);
							$ret[$key]['selltime'][$begin.'-'.$end]['limit'] = $begin.'-'.$end;
							$ret[$key]['selltime'][$begin.'-'.$end]['products'][] = $temp;
							// $ret[$key]['selltime'][$begin.'-'.$end][] = $temp;
						}
					}
				}
			}elseif($value->type==4){//限星期几销售
				$ret[$key]['selltime'] = array();
				$selltimes = Model_Selltime::select('type=2');
				foreach ($selltimes as $key1 => $value1) {
					$weekarray=array("Monday","Tuesday","Wednesday","Thursday","Friday","Saturday","Sunday");
					$day = $weekarray[$value1->param-1];
					$products = Model_Product::selectBysql("select p.* from product p,product_tag pt where p.id = pt.product_id and pt.tag_id=? and selltime=? limit 10",array($value->id,$value1->id));
					if($products){
						foreach ($products as $key2 => $value2) {
							$temp = array(
								'product_id'=>$value2->id,
								'img'=>$value2->small_img,
								'name'=>$value2->name,
								'price_low'=>$value2->price_low,
								'price_mid'=>$value2->price_mid,
								'price_high'=>$value2->price_high,
								'view_type'=>$value2->view_type
							);
							$ret[$key]['selltime'][$day]['limit'] = $day;
							$ret[$key]['selltime'][$day]['products'][] = $temp;
							// $ret[$key]['selltime'][$day][] = $temp;
						}
					}
				}
			}elseif($value->type==5){//限月销售
				$ret[$key]['selltime'] = array();
				$selltimes = Model_Selltime::select('type=3');
				foreach ($selltimes as $key1 => $value1) {
					$montharray=array("January","February","March","April","May","June","July","Aguest","September","October","November","December");
					$month = $montharray[$value1->param-1];
					$products = Model_Product::selectBysql("select p.* from product p,product_tag pt where p.id = pt.product_id and pt.tag_id=? and selltime=? limit 10",array($value->id,$value1->id));
					if($products){
						foreach ($products as $key2 => $value2) {
							$temp = array(
								'product_id'=>$value2->id,
								'img'=>$value2->small_img,
								'name'=>$value2->name,
								'price_low'=>$value2->price_low,
								'price_mid'=>$value2->price_mid,
								'price_high'=>$value2->price_high,
								'view_type'=>$value2->view_type
							);
							$ret[$key]['selltime'][$month]['limit'] = $month;
							$ret[$key]['selltime'][$month]['products'][] = $temp;
							// $ret[$key]['selltime'][$month][] = $temp;
						}
					}
				}
			}
			if($ret[$key]['selltime']){
				$ret[$key]['selltime'] = array_values($ret[$key]['selltime']);
			}
		}
		addmsg(1006,$ret);
	}
}
?>