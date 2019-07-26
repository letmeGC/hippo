<?php
class Model_Snd_md extends ActiveRecord
{
	public static $tablepre = "";
	protected static $class = __CLASS__;
	protected static $table;
	protected static $cinit = false;
	protected static $primaryKey = 'id';
	protected static $_desc = null;
	protected static $_numCol = null;

    /**返回uid 的 md_id
     * @param $uid
     * @return array
     */
    public  static function getMd($uid){
         $ret = array();
         $data = self::select("snd_id = ?",array($uid));
         if($data){
               foreach ($data as $key=>$value){
                   $ret[] = $value->md_id;
               }
         }
         return $ret;
    }
}


?>