<?php
class Model_Orders_read extends ActiveRecord
{
	public static $tablepre = "";
	protected static $class = __CLASS__;
	protected static $table;
	protected static $cinit = false;
	protected static $primaryKey = 'id';
	protected static $_desc = null;
	protected static $_numCol = null;

	/*
	*功能：增加用户未读的记录数
	*@uid：用户id
	*@num：未读记录的数字
	*@type:哪个字段增加，
	*      1：myorder_OnProgress
	*      2：myorder_Approved
	*      3：myorder_Delivering
	*      4：myorder_Complete
	*      5：agentorder_OnProgress
	*      6：agentorder_Approved
	*      7：agentorder_Delivering
	*      8：agentorder_Complete
	*/
	public  static function add_orders_read($uid,$num,$type)
	{
		$self = self::selectOne("uid=?",array($uid));
		if(!$self)
		{
			$self = self::create();
			$self->uid= $uid;
			$self->save();
			$self = self::selectOne("uid=?",array($uid));
		}
		$arr = array(
			"1"=>"myorder_OnProgress",
			"2"=>"myorder_Approved",
			"3"=>"myorder_Delivering",
			"4"=>"myorder_Complete",
			"5"=>"agentorder_OnProgress",
			"6"=>"agentorder_Approved",
			"7"=>"agentorder_Delivering",
			"8"=>"agentorder_Complete",
		);
		$self->$arr[$type] += $num;
		$self->save();		
	}


	/*
	*功能：减少  用户未读记录数  
	*@uid：用户id
	*@type:哪个字段需要清零，
	*      1：myorder_OnProgress
	*      2：myorder_Approved
	*      3：myorder_Delivering
	*      4：myorder_Complete
	*      5：agentorder_OnProgress
	*      6：agentorder_Approved
	*      7：agentorder_Delivering
	*      8：agentorder_Complete
	*/
	public  static function minus_orders_read($uid,$num,$type)
	{
		$self = self::selectOne("uid=?",array($uid));
		if(!$self)
		{
			$self = self::create();
			$self->uid= $uid;
			$self->save();
			$self = self::selectOne("uid=?",array($uid));
		}
		$arr = array(
			"1"=>"myorder_OnProgress",
			"2"=>"myorder_Approved",
			"3"=>"myorder_Delivering",
			"4"=>"myorder_Complete",
			"5"=>"agentorder_OnProgress",
			"6"=>"agentorder_Approved",
			"7"=>"agentorder_Delivering",
			"8"=>"agentorder_Complete",
		);
		$i = $self->$arr[$type] - $num;
		if( $i>0)
		{
			$self->$arr[$type] = $i;
		}else
		{
			$self->$arr[$type] = 0;
		}
		$self->save();		
	}


	/*
	*查询用户未读记录
	*/
	public  static function orders_unread($user)
	{
		$uid = $user->id;
		$self = self::selectOne("uid=?",array($uid));
		if(!$self)
		{
			$self = self::create();
			$self->uid= $uid;
			$self->save();
			$self = self::selectOne("uid=?",array($uid));
		}
		return $self->toArray();
	}
	



}
