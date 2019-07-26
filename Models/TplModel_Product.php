<?php

class TplModel_Product extends TplModel_Base
{
	protected  $FieldArr = array(
	 	'id'=>'ID',
	 	'name'=>'name',//商品名称	
	 	'small_img' => 'image preview',//外面显示图片
	 	'imgs'=>'product images',//里面显示的多张图片
	 	'price_low'=>'MD price',//价格
	 	'price_mid'=>'SD price',//价格
	 	'price_high'=>'Retail price',//价格
	 	'status'=>'status',//上架/下架
	 	'inventory'=>'storage',//库存
	 	'sales'=>'count of sell',//销售数量
	 	'brand'=>'brand',//品牌
	 	'selltime'=>'promotions',//促销时间
	 	'spesification'=>'spesification',//
	 	'introduction'=>'introduction',//
	 	'warranty'=>'warranty',//
		 'related'=>'related',//
		 'view_type'=>'show price',//

	);

	//设置为checkbox框
	protected $FieldTypeCheckbox = array(
	);



	// //加入元素到下拉框
	// public  function addCheckbox($desc)
	// {
	// 	require_once("Models/Model_Choice.php");
	// 	$sql = "SELECT *  FROM choice ";
	// 	$model  = Model_Choice::selectBySql($sql);
	// 	if($model)
	// 	{
	// 		$arr = array();
	// 		foreach ($model as $key => $value) {
	// 			$arr[] = array("label"=>$value->name,"label_value"=>$value->id);
	// 		}
	// 	}

	// 	$tmp['Field'] = 'product_choice';
	// 	$tmp['Type'] = '';
	// 	$tmp['Null'] = '';
	// 	$tmp['Key'] = '';
	// 	$tmp['Default'] = '';
	// 	$tmp['Extra'] = '';
	// 	$tmp['Name'] = 'product_choice';
	// 	$tmp['FieldSelectType'] = 'checkbox';
	// 	$tmp['FieldSelectValue'] = $arr;
	// 	$desc[] = $tmp;
	// 	return $desc;

	// }


	//设置为select框
	protected $FieldTypeSelect = array(
		'status' => array(
			'0'=>'launching',
			'1'=>'dicontinued',
			),
	   'view_type'=>array(
		   '0'=>'yes',
		   '1'=>'no'
	   ),		
		
	);

	//加入元素到下拉框
	public  function addselect()
	{
		require_once("Models/Model_Brand.php");
		$sql = "select id,name  from brand  ";
		$model  = Model_Brand::selectBySql($sql);
		if($model)
		{
			$arr = array();
			foreach ($model as $key => $value) {
				$arr[$value->id] = $value->name;
			}
			$this->FieldTypeSelect['brand'] = $arr ;
		}


		require_once("Models/Model_Selltime.php");
		$sql = "SELECT id,type,param  FROM  selltime";
		$model  = Model_Selltime::selectBySql($sql);
		if($model)
		{
			$arr = array();
			foreach ($model as $key => $value) {
				switch ($value->type) {//1:小时 2:星期几 3:月份
					case '1'://小时
						$jsonarr = json_decode($value->param,true);
						$str ="".date("Y-m-d H:i",$jsonarr['begin'])."~".date("Y-m-d H:i",$jsonarr['end']);
						break;
					case '2'://星期几
						$jsonarr= array(
							"1"=>"Monday",
							"2"=>"Tuesday",
							"3"=>"Wednesday",
							"4"=>"Thursday",
							"5"=>"Friday",
							"6"=>"Staurday",
							"7"=>"Sunday",
							);
						$str = "".$jsonarr[$value->param];
						break;
					case '3'://月份
						// $str = "".$value->param."月";
						$Month_arr=array("1"=>"January","2"=>"February","3"=>"March","4"=>"April","5"=>"May","6"=>"June","7"=>"July","8"=>"August","9"=>"September","10"=>"October","11"=>"November","12"=>"December");
						$str = $Month_arr[$value->param];
						break;
					
					default:
						break;
				}
				lgj($str );
				$arr[$value->id] = $str ;
			}
			$this->FieldTypeSelect['selltime'] = $arr ;
		}

	}



	//图片
	protected $FieldTypeImg = array(
		'small_img' => "ProductImg",
	);

	//带时间控件的文本框
	protected $FieldTypeTime = array(
	);

	//富文本
	protected $FieldTypeUeditor = array(
		'spesification',
		'introduction',
		'warranty',
		'related',
	);

	//textare框
	protected $FieldTypetextarea = array(
		// 'spesification',
		// 'introduction',
		// 'warranty',
		// 'related',
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
		require_once "Models/Model_Product.php";
		$this->addselect();

		Model_Product::init();
		$desc = Model_Product::desc();
		
		

		foreach ($desc as $key => $value) {
			if(in_array($value['Field'], array('created_at','updated_at'))){
				unset($desc[$key]);
			}else
			{
				$desc[$key]['Name'] = $this->FieldArr[$value['Field']];
				if($value['Field']=='related'){
					$desc[$key]['FieldSelectType'] ='no_display';
					$desc[$key]['FieldSelectValue'] ='';
				}else if(array_key_exists($value['Field'] , $this->FieldTypeSelect))
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
				}else if(in_array($value['Field'],$this->FieldTypeCheckbox))
				{
					$desc[$key]['FieldSelectType'] ='checkbox';
					$desc[$key]['FieldSelectValue'] ='';
				}
				else if(in_array($value['Field'],$this->FieldTypetextarea))
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


		// $desc = $this->addCheckbox($desc);
		$tmp['Field'] = 'product_choice';
		$tmp['Type'] = '';
		$tmp['Null'] = '';
		$tmp['Key'] = '';
		$tmp['Default'] = '';
		$tmp['Extra'] = '';
		$tmp['Name'] = 'product_spec';
		$tmp['FieldSelectType'] = 'checkbox';
		$tmp['FieldSelectValue'] = $arr;
		$desc[] = $tmp;

		$tmp['Field'] = 'product_ptype';
		$tmp['Type'] = '';
		$tmp['Null'] = '';
		$tmp['Key'] = '';
		$tmp['Default'] = '';
		$tmp['Extra'] = '';
		$tmp['Name'] = 'product_type';
		$tmp['FieldSelectType'] = 'checkbox';
		$tmp['FieldSelectValue'] = $arr;
		$desc[] = $tmp;

		$tmp['Field'] = 'product_tag';
		$tmp['Type'] = '';
		$tmp['Null'] = '';
		$tmp['Key'] = '';
		$tmp['Default'] = '';
		$tmp['Extra'] = '';
		$tmp['Name'] = 'product_tag';
		$tmp['FieldSelectType'] = 'checkbox';
		$tmp['FieldSelectValue'] = $arr;
		$desc[] = $tmp;

		
		
		$this->desc = array_values($desc);
	}

	public static function delete($id)
	{
		require_once "Models/Model_Product.php";
		Model_Product::delete("id=?",array($id));
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