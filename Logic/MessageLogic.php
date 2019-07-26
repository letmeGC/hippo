<?php

class MessageLogic extends LogicBase
{
	protected static $_before=array(
    	"aes"=>array("include"=>array("all")),
		"checkLogin"=>array("include"=>array("all")),
        );
    
   public static function msgList(){

        require_once "Models/Model_User_message.php";
        $user = static::$user ;
         Model_User_message::addCommonToUser($user->id,$user->type);
         $res = Model_User_message::userMsgList($user->id);
         addmsg(1059,$res);
   }

   public static function  msgDetail(){
       require_once "Models/Model_User_message.php";
       $id  = static::$_P['id'];
       $data = Model_User_message::selectOne("id = ?",array($id));
       if(empty($data)){addmsg(1060,['errcode'=>-1,'msg'=>'不存在的记录']);return;}
       $detail = array(
           'errcode'=>0,
            'id'=>$data->id,
            'created_at' => $data->created_at,
            'title' => $data->title,
            'img' => Consts::imgurlhttp.$data->img,
            'body' => html_entity_decode($data->body),
            'introduction' =>$data->introduction,
       );
       addmsg(1060,$detail);
   }

}