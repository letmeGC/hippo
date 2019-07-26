<?php

class TalkLogic extends LogicBase
{
    protected static $_before = array(
         "checkLogin" => array("include" => array("all"), "except" => array("login")),
    );

    public static function login(){
         require_once "Models/Model_Customer_service.php";
         require_once "Models/Model_Talk_log.php";
         $name = $_REQUEST['username'];
         $password = $_REQUEST['password'];
        if($name){
            $user = Model_Customer_service::selectOne('username=?',array($name));
            $user->id = Consts::UID_PREFIX.$user->id;
            $tk = static::doLogin($user);
            $newMsg = Model_Talk_log::selectBySql("select * from `talk_log` where status = '0' ");
            $data = Model_Talk_log::selectBySql("select u.id , u.nickname,u.head_img   from  (select DISTINCT  `fromuser` as uid from `talk_log` where `touser` = '".$user->id."' and  status = 0 ) as t  left join `user_char` u on t.uid = u.id");
            require "View/Tpls/csindexView.tpl";die;
        }else{
            require "View/Tpls/csloginView.tpl";die;
        }

    }

    /**
     * @param $data
     * @return mixed
     */
    protected static function doLogin($data)
    {
        require_once "Models/Model_Customer_service.php";
        $primary = Model_Customer_service::primary(); // id
        $tk = Session::register($data->$primary); //$user->id
        Session::setData("userid",$data->$primary,$tk);
        return $tk;
    }

}