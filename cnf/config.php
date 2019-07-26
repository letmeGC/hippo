<?php
// for debug
// ini_set( 'display_errors', 'On' );
// error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE & ~E_WARNING);
// ini_set('date.timezone','Asia/Shanghai');
set_include_path(get_include_path() . PATH_SEPARATOR . "/home/hippo");

$GLOBALS["config"]=array();
$GLOBALS["config"]["mysql"] = array(
	"host"=>"127.0.0.1",
	"port"=>3306,
	"dbname"=>"hippo",
	"user"=>"root",
	"password"=>""
	);

$GLOBALS["config"]["memcache"] = array(
	"host"=>"localhost",
	"port"=>11211,
	"shost"=>"localhost",
	"sport"=>11211
	);

$GLOBALS["config"]["session"] = array(
	"host"=>"localhost",
	"port"=>11211
	);


define("AESKEY","#@SAD@#%*(*Dsd3246dsajfhdkjf435DFm,rerew4532978dskfkdasrGYH%YUGFDERWrtw");
define("LOGPATH","/var/log/php-fpm");
define("USECRYPT",false);
define("CLIENTVER",1.0);
define("APPSECRET",'839d0863081e986c56bcdfec90fad9b6');
define("APPID",'wx76ff7a819b1b5b3d');
?>