<?php

class NewsLogic extends LogicBase
{
	protected static $_before=array(
    	"aes"=>array("include"=>array("all")),
		//"checkLogin"=>array("include"=>array("all")),
        );
    
   public static function categoryList()
   {
       require_once "Models/Model_Ncategory.php";
       $data = Model_Ncategory::select();
       foreach($data as $key => $value){
           $res[] = $value->toArray();
       }
       addmsg(1013,$res); 
   }

   public  static function newsList()
   {
       require_once "Models/Model_News.php";  
       $category_id = static::$_P['category_id'];
       $page = static::$_P['page'] ?static::$_P['page'] :1;
       $show = 10;
       $start = ($page-1)*$show;
       $data = Model_News::selectBySql("select * from `news` where `id` in (select distinct `news_id` from 
       `news_category` where `category_id`= {$category_id}) order by `hit` desc limit {$start},{$show}");
       $res= array();
       if($data){
          foreach($data as $value){
             $res[] = $value->toArray();
          }
       }
    
       addmsg(1014,$res); 

   }

   public static function newsDetail()
   {
       require_once "Models/Model_News.php";  
       $news_id = static::$_P['news_id'];
       $news = Model_News::selectOne("id = ?",array($news_id));
       $news->hit += 1;
       $news->save();
       $news->body = html_entity_decode($news->body);
       addmsg(1015,$news->toArray()); 
   } 
}