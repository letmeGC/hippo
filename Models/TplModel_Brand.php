<?php

class TplModel_Brand extends TplModel_Base
{
	protected  $FieldArr = array(
	 	'id'=>'ID',
	 	'name'=>'name',
	 	'desc'=>'description',
		 'img'=>'logo - rectangle',
		 'brand_img'=>'logo - circle'
	);

	//设置为select框
	protected $FieldTypeSelect = array(
		// 'status' => array(
		// 	'0'=>'正在加油中',
		// 	'1'=>'完成加油',
		// 	),
		
	);

	//加入元素到下拉框
	public  function addselect()
	{

	}

	//图片
	protected $FieldTypeImg = array(
		'img' => "BrandImg",
		'brand_img'=>'BrandImg'
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
		require_once "Models/Model_Brand.php";
		$this->addselect();
		
		Model_Brand::init();
		$desc = Model_Brand::desc();
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
		require_once "Models/Model_Brand.php";
		Model_Brand::delete("id=?",array($id));
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