<?php
class MasterSettingLogic extends LogicBase
{
    protected static $_before = array(
        "aes" => array("include" => array("all")),
        // "checkLogin"=>array("include"=>array("all")),
    );


    public static function aboutUs(){
        require_once "Models/Model_Master_setting.php";
        $data = Model_Master_Setting::selectOne("name = ?",array("about us"));
        addmsg(1067,array('content'=>$data->content));

    }

    public static function  termsOfService(){
        require_once "Models/Model_Master_setting.php";
        $data = Model_Master_Setting::selectOne("name = ?",array("terms of service"));
        addmsg(1068,array('content'=>$data->content));
    }

}