<?php
use \GatewayWorker\Lib\Gateway;
use \Workerman\Worker;
use \Workerman\Lib\Timer;
class UserLogic extends LogicBase
{
	protected static $_before=array(
        "checkLogin" => array("include" => array("all"), 'except'=>array('login'))
    );


    /**登录
     * @param $clientID
     * @param $d
     */
  public static  function login($clientID,$d)
  {
       if(static::doLogin($clientID,$d)) {
              send(13010, array('errcode'=>0,'msg'=>'login suc'));
       }
  }

    /**f发消息
     * @param $clientID
     * @param $d
     */
  public static function sendToUid($clientID,$d){
        require_once "Models/Model_Talk_log.php";
        require_once "Models/Model_Customer_service.php";
          $user = static::user();
          $toUser = $d['toUser'] ;   //  给客服发消息传0
          $msg = $d['msg'];
          $msgType = $d['msgType']; //' 0 文本消息 1 链接 2 图片',

          if(!$toUser){
                  $cs_id  = Model_Customer_service::select();
                  foreach ($cs_id as $val){
                      if(Gateway::isUidOnline(Consts::UID_PREFIX.$val->id)){
                             $toUser = Consts::UID_PREFIX.$val->id;
                      }
                  }
          }
          $talk_model = Model_Talk_log::create();
          $talk_model->touser = $toUser;
          $talk_model->fromuser = $user->id;
          $talk_model->content = $msg;
          $talk_model->type = $msgType;
          $talk_model->save();
       //   Gateway::sendToUid($toUser,json_encode(array("type"=>$msgType,'msg'=>$msg,'fromUser'=>$user->id,'fromName'=>$user->nickname)));
          send2User(13011, array("msgType"=>$msgType,'msg'=>$msg,'fromUser'=>$user->id,'fromName'=>$user->nickname), array($toUser));
  }

 public static function  talkHistory()
 {
     require_once "Models/Model_Talk_log.php"; 
     require_once "Models/Model_Customer_service.php";
     $user = static::user();
     $historyData = Model_Talk_log::selectBySql("select * from `talk_log` ");    
 } 

}