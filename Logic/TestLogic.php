<?php
class TestLogic extends  AdminLogicBase
{
	protected static $_before=array(
    	"aes"=>array("include"=>array("all"))
    	);
	public static function upload()
	{
		mem()->set("drawdata",static::$_P["data"]);
	}

	public static function download()
	{
		$data = mem()->get("drawdata");
		addmsg(1,$data);
	}

}
?>
