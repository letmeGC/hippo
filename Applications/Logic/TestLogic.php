<?php
use \GatewayWorker\Lib\Gateway;
use \Workerman\Worker;
class TestLogic extends LogicBase
{

    public static function send($clientID,$d)
    {
         $message = $_REQUEST["message"];
         Gateway::sendToAll($message);
    }

    public static  function  build(){
         Gateway::sendToAll(json_encode(array(
             'type'      => 'all',
             'msg' => '发给所有人'
         )));
lgj(123);
         echo 123;
    }

}
?>
