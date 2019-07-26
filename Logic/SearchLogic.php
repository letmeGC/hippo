<?php
class SearchLogic extends LogicBase
{
	protected static $_before=array(
    	"aes"=>array("include"=>array("all")),
    	"checkLogin"=>array("include"=>array("all")),
        );
        
/**
 * 商品搜索
 */
    public static function search()
    {
        require_once "Models/Model_User_Search_log.php";
        $user = static::$user;
        $searchData = static::$_P['searchData'];
        $searchData = trim($searchData);
        $page = static::$_P['page'] ? static::$_P['page'] : 1;
        $show  = 10;
        $start = ($page-1)*$show;
    
        if(!$searchData){
            sendError('搜索信息不能为空');
			return;
        }

        $sql = "select un.* from (
                  select * from `product` where `name` like '%{$searchData}%'
                   UNION
                  select *  from `product` where brand in (select `id` from brand where `name` like '%{$searchData}%') 
                ) as un limit {$start},{$show}";
       $data = Model_User_Search_log::selectBySql($sql);
       Model_User_Search_log::log($user->id,$searchData);
       $res = array();
       if($data){
           foreach($data as $key=>$value){
               $temp = array(
                   'product_id'=>$value->id,
                   'img'=>$value->small_img,
                   'name'=>$value->name,
                   'price_low' => $value->price_low,
                   'price_mid' => $value->price_mid,
                   'view_type' => $value->view_type,
               );
               $res[] = $temp;
           }  
       }


       addmsg(1064,$res);              
    }   

/**
 * 个人搜索记录
 */
    public static function lastedSearch()
    {
        require_once"Models/Model_User_Search_log.php";
        $user = static::$user;
        $page = static::$_P['page'] ? static::$_P['page'] : 1;
        $show  = 10;
        $start = ($page-1)*$show;
        $res = Model_User_Search_log::lastedSearch($user->id,$start,$show);
        addmsg(1065,$res);
    }

    public static function mostViewed()
    {
        require_once"Models/Model_User_Search_log.php";
        $res = Model_User_Search_log::mostViewed();
        addmsg(1066,$res);
    }

}	