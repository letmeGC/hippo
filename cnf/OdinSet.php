<?php
// tpl表中需要初始化的表明 例如:tpl_t1,tpl_t2
global $__tplTables;
$__tplTables = array("t1","t2");

// Userlist 对应的model名
global $__usermodelname;
$__usermodelname = "Model_Char";

// 长连接 调用的Logic及action
global $__logoutLogicName,$__logoutActionName;
$__logoutLogicName = "User";
$__logoutActionName = "logout";

//后台配置文件
global $__adminConfig;
$__adminConfig= "cnf/admin_config.php";
?>