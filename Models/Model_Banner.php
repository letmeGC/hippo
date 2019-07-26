<?php
class Model_Banner extends ActiveRecord
{
	public static $tablepre = "";
	protected static $class = __CLASS__;
	protected static $table;
	protected static $cinit = false;
	protected static $primaryKey = 'id';
	protected static $_desc = null;
	protected static $_numCol = null;

	public function toArray()
	{
		require_once("cnf/const.php");

		$ret = parent::toArray();
		
		$ret['img'] = Consts::imgurlhttp.$ret['img'];
		return $ret;
	}
}
?>