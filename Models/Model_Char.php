<?php
class Model_Char extends ActiveRecord
{
	public static $tablepre = "user_";
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
		$ret['head_img'] = Consts::imgurlhttp.$ret['head_img'];
		$ret['npwp_img'] = Consts::imgurlhttp.$ret['npwp_img'];
		$ret['ktp_img'] = Consts::imgurlhttp.$ret['ktp_img'];
		$ret['store_img'] = Consts::imgurlhttp.$ret['store_img'];
		return $ret;
	}


	

	//查看用户的master  dealer
//	public  static function MasterDealer($user)
//	{
//		$uid = $user->id;
//		$type = $user->type;//1 master 2 sub 3 new
//
//		$ret = array();
//		switch ($type) {
//			case '1'://1 master
//				break;
//			case '2'://2 sub
//				$dealer_code = $user->master_dealer_code;//master  dealer 的dealer code
//				$nickname = self::get_nickname($dealer_code);
//				if($nickname)
//				{
//					$ret[] = array($dealer_code,$nickname);
//				}
//
//				break;
//
//			case '3'://3 new
//				$dealer_code = $user->master_dealer_code;//master  dealer 的dealer code
//				$nickname = self::get_nickname($dealer_code);
//				if($nickname)
//				{
//					$ret[] = array($dealer_code,$nickname);
//				}
//
//				break;
//		}
//		return $ret;
//	}


    public  static function MasterDealer($user)
	{
	        require_once "Models/Model_Snd_md.php";
		   //1 master 2 sub 3 new
	    	$ret = array();
		    if($user->type ==2 || $user->type == 3){
                   $md_arr = Model_Snd_md::getMd($user->id);
                   $ret = self::getUidNickName($md_arr);
		    }
		    return $ret;
	}


	//根据用户的dealer code 获得对应的nickname
	public  static  function  get_nickname($dealer_code)
	{
		  $self = self::selectOne("dealer_code=?",array($dealer_code));
	      if($self)
		  {
			  return $self->nickname;
		   }else
	  	 {
			  return '';
		  }

	}

	//根据用户的uid获得对应的nickname
	public  static  function  get_nickname1($uid)
	{
		$self = self::selectOne("id=?",array($uid));
		if($self)
		{
			return $self->nickname;
		}else
		{
			return '';
		}

	}

	//根据用户的dealer code 获得对应的uid
	public  static  function  get_uid($dealer_code)
	{
		$self = self::selectOne("dealer_code=?",array($dealer_code));
		if($self)
		{
			return $self->id;
		}else
		{
			return false;
		}

	}


    /**根据uid 返回  dealer_code 与 nickname
     * @param $uid_arr
     * @return array  二维数组
     */
	public static  function  getUidNickName($uid_arr)
    {
            $uid_str =  implode(',',$uid_arr);
            $data = self::selectBySql("select  * from `user_char` where `id` in({$uid_str}) ");
            $ret = array();
            if($data){
                foreach ($data as $key=>$value) {
                     $ret[] = array($value->dealer_code,$value->nickname);
                 }
           }
           return $ret;
    }
}





?>