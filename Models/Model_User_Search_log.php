<?php
class Model_User_Search_log  extends ActiveRecord
{
	public static $tablepre = "";
	protected static $class = __CLASS__;
	protected static $table;
	protected static $cinit = false;
	protected static $primaryKey = 'id';
	protected static $_desc = null;
	protected static $_numCol = null;


	public static function log($uid,$searchData){
		$model = self::create();
		$model->uid = $uid;
		$model->search_data = $searchData;
		$model->save();
	}

	public static function lastedSearch($uid,$start,$show){
		$sql = "select  distinct `search_data` from `user_search_log` where `uid` = '{$uid}' limit {$start},{$show}";
		$data = self::selectBySql($sql);
		$res =array();
		if($data){
			foreach($data as $key=>$value){
				$res[] = $value->search_data;
			}
		}  
		return $res;
	}

	public static function mostViewed(){
	    if($mostViewed = mem()->get('mostViewed')){
            $mostViewed = json_decode($mostViewed,true);
	    }else{
            $sql = "select `search_data` from (select count(*) ct ,`search_data` from `user_search_log` group by `search_data`) as r order by r.ct desc limit 10"; 
			$data  = self::selectBySql($sql);
			if($data){
				foreach($data as $key=>$value){
                   $mostViewed[] = $value->search_data;
				}
			}
			mem()->set('mostViewed',json_encode($mostViewed),300);
		}
        return $mostViewed;
	}
}
	