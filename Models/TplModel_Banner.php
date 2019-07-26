<?php

class TplModel_Banner extends TplModel_Base
{
	protected  $FieldArr = array(
	 	'id'=>'ID',
	 	'img'=>'img',
	 	'redirect_target'=>'redirect_target',//redirect_type值为新闻，则redirect_target的值为新闻id    若为：product  则redirect_target的值为商品id
	 	'position_target'=>'position_target',//所属哪个tag
	 	'position_type'=>'position_type',
	 	'redirect_type'=>'redirect_type',
	);

	//设置为select框
	protected $FieldTypeSelect = array(
		'position_type' => array(
			'1'=>'top banner',
			'2'=>'tag banner',
			),
		'redirect_type' => array(
			'1'=>'redirect to product',
			'2'=>'redirect to news',
			'3'=>'redirect to tag',
			),
		
	);

	//加入元素到下拉框
	//加入元素到下拉框
	public  function addselect()
	{
		require_once("Models/Model_Tag.php");
		$sql = "SELECT *  FROM  tag ";
		$model  = Model_Tag::selectBySql($sql);
		if($model)
		{
			$arr = array();
			foreach ($model as $key => $value) {
				$arr[$value->id] = $value->name;
			}
			$this->FieldTypeSelect['position_target'] = $arr ;
		}
	

	}

	//图片
	protected $FieldTypeImg = array(
		'img' => "BannerImg",
		
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
		require_once "Models/Model_Banner.php";
		$this->addselect();
		
		Model_Banner::init();
		$desc = Model_Banner::desc();
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
		require_once "Models/Model_Banner.php";
		Model_Banner::delete("id=?",array($id));
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