<?php

class TplModel_Char extends TplModel_Base
{
	protected  $FieldArr = array(
	 	'id'=>'ID',
	 	'nickname'=>'nickname',
	 	'phone'=>'phone',
	 	'password'=>'password',
	 	'email'=>'email',
	 	'head_img'=>'head_img',
	 	'dealer_code'=>'dealer_code',
	 	'region'=>'region',
	 	'store_address'=>'store_address',
	 	'post_code'=>'post_code',
	 	'store_name'=>'store_name',
	 	'store_number'=>'store_number',
	 	'person_incharge'=>'person_incharge',
	 	'store_status'=>'store_status',
	 	'master_dealer_code'=>'master_dealer_code',
	 	'npwp_img'=>'npwp_img',
	 	'ktp_img'=>'ktp_img',
	 	'type'=>'type',
	 	'store_img'=>'store_img',
	);


	//设置为select框
	protected $FieldTypeSelect = array(
		'type' => array(
			'1'=>'master',
			'2'=>'sub',
			'3'=>'new',
			),
		'store_status' => array(
			'0'=>'rent',
			'1'=>'own',
			),
	);

	//加入元素到下拉框
	public  function addselect()
	{
		
	}

	//图片
	protected $FieldTypeImg = array(
		'head_img' => "CharImg",
		'npwp_img' => "CharImg",
		'ktp_img'  => "CharImg",
		'store_img'  => "CharImg",
		
	);

	//带时间控件的文本框
	protected $FieldTypeTime = array(
	);

	//富文本
	protected $FieldTypeUeditor = array(

	);

	//textare框
	protected $FieldTypetextarea = array(
	);

	//返回文件夹
	public function  getDocumentImg()
	{
		return $this->FieldTypeImg;
	}


    public function getEditUrl($id)
    {
        return "/Admin/mineedit?tb=".$this->name."&id=$id&page=".$_REQUEST["page"];
    }


    public function desc(){
		require_once "Models/Model_Char.php";
		
		$this->addselect();

		Model_Char::init();
		$desc = Model_Char::desc();
		foreach ($desc as $key => $value) {
			if(in_array($value['Field'], array('created_at','updated_at'))){
				unset($desc[$key]);
			}else
			{
				$desc[$key]['Name'] = $this->FieldArr[$value['Field']];
				if(array_key_exists($value['Field'] , $this->FieldTypeSelect))
				{
					$desc[$key]['FieldSelectType'] ='select';
					$desc[$key]['FieldSelectValue'] =$this->FieldTypeSelect[$value['Field']];
				}else if(array_key_exists($value['Field'],$this->FieldTypeImg))
				{
					$desc[$key]['FieldSelectType'] ='img';
					$desc[$key]['FieldSelectValue'] ='';
				}else if(in_array($value['Field'],$this->FieldTypeTime))
				{
					$desc[$key]['FieldSelectType'] ='time';
					$desc[$key]['FieldSelectValue'] ='';
				}else if(in_array($value['Field'],$this->FieldTypeUeditor))
				{
					$desc[$key]['FieldSelectType'] ='ueditor';
					$desc[$key]['FieldSelectValue'] ='';
				}else if(in_array($value['Field'],$this->FieldTypetextarea))
				{
					$desc[$key]['FieldSelectType'] ='textarea';
					$desc[$key]['FieldSelectValue'] ='';
				}else
				{
					$desc[$key]['FieldSelectType'] ='input';
					$desc[$key]['FieldSelectValue'] ='';
				}

			}

		}
		
		$this->desc = array_values($desc);
	}

	public static function delete($id)
	{
		require_once "Models/Model_Char.php";
		Model_Char::delete("id=?",array($id));
	}


	public function getDeleteUrl($id)
	{
		return "/Admin/minedelete?tb=".$this->name."&id=$id&page=".$_REQUEST["page"];

	}
	public function getNewUrl()
	{
		return "/Admin/minecreate?tb=".$this->name;
	}

	public function getPageUrl($id)
	{
		return "/Admin/mineShow?tb=".$this->name."&page=$id";
	}






}

?>