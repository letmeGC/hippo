<?php

use \GatewayWorker\Lib\Gateway;
use \Workerman\Worker;
use \Workerman\Lib\Timer;

class TalkLogic extends LogicBase
{
    protected static $_before=array(
        "checkLogin" => array("include" => array("all"), 'except'=>array('wsLogin'))
    );

    public  static function wsLogin($clientID, $d){
         $res = static::doLogin($clientID,$d);
    }


    public static function sendToUid($clientID,$d){
        require_once "Models/Model_Talk_log.php";
        $toUser = $d['toUser'];
        $fromUser = $d['fromUser'];
        $fromName = $d['fromName'];
        $msg = $d['msg'];
        $msgType = $d['msgType'];  //' 0 文本消息 1 链接 2 图片',
        $talk_model = Model_Talk_log::create();
        $talk_model->touser = $toUser;
        $talk_model->content = $msg;
        $talk_model->fromuser = $fromUser;
        $talk_model->type = $msgType;
        $talk_model->save();
       // Gateway::sendToUid($toUser,json_encode(array("type"=>$msgType,'msg'=>$msg,'fromUser'=>$fromUser,'fromName'=>$fromName)));
        send2User(13011, array("msgType"=>$msgType,'msg'=>$msg,'fromUser'=>$fromUser,'fromName'=>$fromName), array($toUser));

    }

    public static function doLogin($clientID, $d)
    {

        $clients = Gateway::getClientIdByUid($d['fromUser']);
        foreach ($clients as $key => $value) {
            Gateway::closeClient($value);
        }
        Gateway::bindUid($clientID, $d['fromUser']);
        return true;
    }

    protected static function checkLogin($client_id,&$d)
    {
        return true;
    }
}