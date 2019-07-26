<?php
class Model_Device extends ActiveRecord
{
	public static $tablepre = "user_";
	protected static $class = __CLASS__;
	protected static $table;
	protected static $cinit = false;
	protected static $primaryKey = 'id';
	protected static $_desc = null;
	protected static $_numCol = null;

}
?>