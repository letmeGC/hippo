<?php
class BannerLogic extends LogicBase
{
	protected static $_before=array(
    	"aes"=>array("include"=>array("all")),
    	// "checkLogin"=>array("include"=>array("all")),
    	);

	public static function top_banner(){
		require_once "Models/Model_Banner.php";
		$banners = Model_Banner::select('position_type=1');
		$ret = array();
		foreach ($banners as $key => $value) {
			$ret[] = $value->toArray();
		}
		addmsg(1005,$ret);
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