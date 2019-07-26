<?php
class Model_User_message extends ActiveRecord
{
	public static $tablepre = "";
	protected static $class = __CLASS__;
	protected static $table;
	protected static $cinit = false;
	protected static $primaryKey = 'id';
	protected static $_desc = null;
	protected static $_numCol = null;

	public static function  userMsgList($uid){
            $res = self::select("uid= ? and isnew = ?",array($uid,1));
           $data = array();
            if($res){
               foreach ($res as $key=>$val){
                   $val->img = Consts::imgurlhttp.$val->img;
                   $val->body = html_entity_decode($val->body);
                   $data[] = $val->toArray();
                   $id_array[]= $val->id;
               }
               //修改状态为已读
               //self::updateStatus($id_array);
           }
           return   $data;
	}

	public  static function  updateStatus($id_array){
        if(empty($id_array)){return;}
	    $id_str = implode(',',$id_array);
        $id_str = '('.$id_str.')';
        idb()->runSql("update `user_message` set  `isnew` = '0' where  `id` in {$id_str}");
    }


  public static  function  addCommonToUser($uid,$type){
         $type_array = array('1'=>3,'2'=>1,'3'=>1);//1 master 2 sub 3 new
         $send_to = $type_array[$type];
        $common  =    self::selectBySql("select `id`  from `common_message` where date_sub(curdate(), INTERVAL 7 DAY) <= `created_at` and `send_to` in(0,{$send_to})");
        $arr1 = array();
        $arr2 = array();
       foreach ($common as $val){
           $arr1[] = $val->id;
      }
      $user_common= self::selectBySql("select `common_id` from `user_message` where `uid` = ? and `common_id` > 0 ",array($uid));
      foreach ($user_common as $val){
          $arr2[] = $val->common_id;
      }

      $arr_diff = array_diff($arr1,$arr2);
      $str = implode(",",$arr_diff);
      if($str){
          //查询缺少的系统邮件内容
          $res = self::selectBySql("select * from `common_message` where `id` in ({$str})");
          $count =  count($res) -1;
          $values = '';
          $created_at = date("Y-m-d H:i:s",time());
          foreach ($res as $key=> $val){
              if($key == $count ){
                   $values .= "($user->id,'{$val->title}','{$val->body}','{$val->introduction}','{$val->id}','{$created_at}','{$created_at}')"; continue;
              }
              $values .= "($user->id,'{$val->title}','{$val->body}','{$val->introduction}','{$val->id}','{$created_at}','{$created_at}'),";
          }
          //将缺少的系统消息插入user_message
          $inert_sql = "insert into `user_message` (`uid`,`title`,`body`,`introduction`,`common_id`,`created_at`,`updated_at`) values {$values}";
          idb()->runSql($inert_sql);
      }
	}

    /**给用户发消息
     * @param $param
     */
  public static function  sendMsgToUser($param){
          $newObj = self::create();
          $newObj->uid = $param['uid'];
          $newObj->title = $param['title'];
          $newObj->img = $param['img'];
          $newObj->introduction = $param['introduction'];
          $newObj->created_at = $param['send_time'];
          $newObj->updated_at = $param['send_time'];
          $newObj->save();
  }

    /**判断最近 7 天是否有未读邮件
     * @param $uid
     * @return int
     */
    public  static function  checkMsg(&$user)
    {
        $user_count = self::selectBySql ("select  count(1)  as ct from `user_message` where date_sub(curdate(), INTERVAL 7 DAY) <= `created_at`  and  `uid` = ?   and  `isnew` =  ?  order by `created_at` desc",array($user->id,1));
        $user_count = $user_count[0]->ct;
        $common_count = self::checkCommonMsg($user->id,$user->type);
        $common_count = count($common_count);
        return $user_count + $common_count;
    }

    /**
     * 查询最近七天缺少的系统消息返回缺少的ID
     */
    public static function checkCommonMsg($uid,$type){
        $type_array = array('1'=>3,'2'=>1,'3'=>1);//1 master 2 sub 3 new
        $send_to = $type_array[$type];
        //有效的common_message
        $common = self::selectBySql("select `id` from `common_message` where  date_sub(curdate(), INTERVAL 7 DAY) <= `created_at`  and send_to in(0,{$send_to})");
        $arr_diff = array();
        if($common){
                $arr1 = array();
                $arr2 = array();
                foreach ($common as $val){
                     $arr1[] = $val->id;
                }
              //查询用的common_message
               $user_common= self::selectBySql("select `common_id` from `user_message`  where  `uid` = ? and `common_id` > 0 ",array($uid));
              if($user_common){
                  foreach ($user_common as $val){
                      $arr2[] = $val->common_id;
                  }
              }
               $arr_diff = array_diff($arr1,$arr2);
       }
        return $arr_diff;
    }
}