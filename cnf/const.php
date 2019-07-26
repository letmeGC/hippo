<?php

class Str
{
	public static $tag_Img = array(
		//数据格式
		// 'tag_id'=>array(
		// 	"位置1"=>"角标图片地址",
		// 	"位置2"=>"角标图片地址",
		// 	"位置3"=>"角标图片地址",
		// 	"位置4"=>"角标图片地址",
		// 	"位置5"=>"角标图片地址",
		// 	"位置6"=>"角标图片地址",
		// 	), 	
		
		'1'=>array(
				"1"=>"/home/hippo/Tag_image/Disc.png",
				'2'=>"/home/hippo/Tag_image/Hot.jpg",
				'3'=>"/home/hippo/Tag_image/limit_stock.png",
				'4'=>"/home/hippo/Tag_image/new.png",
				'5'=>"/home/hippo/Tag_image/pre_order.png",
				'6'=>"/home/hippo/Tag_image/Recommended.png"
			),
		'2'=>array(
				"1"=>"/home/hippo/Tag_image/Disc.png",
				'2'=>"/home/hippo/Tag_image/Hot.jpg",
				'3'=>"/home/hippo/Tag_image/limit_stock.png",
				'4'=>"/home/hippo/Tag_image/new.png",
				'5'=>"/home/hippo/Tag_image/pre_order.png",
				'6'=>"/home/hippo/Tag_image/Recommended.png"
			),
		
		);

}

class Consts
{
	const imgurlhttp = 'http://ateam.ticp.io:9112/';//图片访问
	const imgurl = '/home/hippo/root/';//上传文件存放目录

	const LG_VERIFY_TIMEOUT = 300;	//验证码有效期
	const LG_VERIFY_URL = "http://52.220.29.74/Send/Send";
	const LG_VERIFY_KEY = "dsfk3flek";
	const LG_VERIFY_MERCHANT_ID = 4;
	// const LG_VERIFY_FROM = "Picklon";
	const  LG_VERIFY_FROM = "CMK";
	const LG_PAY_MERCHANT_ID = 5;
	const LG_PAY_KEY = "jRi2PMja98U";
	const LG_UPFILE_STORE_ROOT = "/home/washplatform/media/shopImg/";
	const LG_UPFILE_STORE_URL = "/media/shopImg/";
	const LG_FILE_URL = "http://ateam.ticp.io:8800/media/";
	const LG_PUSH_PEM_DEV = "/home/apns_dev.pem";
	const LG_PUSH_PEM = "/home/apns_distribution.pem";
	const LG_PUSH_PASS = "picklon112233";
	const LG_PUSH_ENV = "";
	const UID_PREFIX = "cs"; //hippo客服前缀
}
 ?>