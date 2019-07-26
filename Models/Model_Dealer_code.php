<?php
class Model_Dealer_code extends ActiveRecord
{
	public static $tablepre = "";
	protected static $class = __CLASS__;
	protected static $table;
	protected static $cinit = false;
	protected static $primaryKey = 'id';
	protected static $_desc = null;
	protected static $_numCol = null;

	/*
   *产生dealer code

   */
    public static function create_dealer_code()
    {
    	lgj("begin-dealer code---------------------");
        require_once("Models/Model_Dealer_code.php");
      
        $sql = "SELECT *  FROM  dealer_code  where  `status` = 0   LIMIT 1";
        $Model_Dealer_code = Model_Dealer_code::selectBysql($sql,array());
        
        $Model_Dealer_code[0]->status= 1;
        $Model_Dealer_code[0]->save();
        $dealer_code = $Model_Dealer_code[0]->code;//dealer code
    	lgj("end-dealer code---------------------");
        
        return $dealer_code;

    }
}
?>