<?php
global $routes;
$routes = array(
		// 短连接
		"1"=>"Test#upload",
		"2"=>"Test#download",

		// 长连接 
		"3"=>"Test#test",
		"1001"=>"User#login",
		"1002"=>"User#sendCode",
		"1003"=>"Tab#lt",
		"1004"=>"Tag#lt",
		"1005"=>"Banner#top_banner",
		"1006"=>"Tab#tab_data",
		"1007"=>"Brand#brand_index",
		"1008"=>"Brand#product_list",
		"1009"=>"Tag#tag_detail",
		"1010"=>"Product#product_detail",
		"1011"=>"User#phone_regist",
		"1012"=>"User#upload",
		"1013"=>"News#categoryList",
		"1014"=>"News#newsList",
		"1015"=>"News#newsDetail",

		"1016"=>"Cart#addCart",//加入购物车
		"1017"=>"Cart#midifyCart",//修改购物车商品数量
		"1018"=>"Cart#detailCart",//用户购物车列表
		"1019"=>"Cart#deleteCart",//移除购物车
		"1020"=>"User#changePwd", //修改密码(登录)
		"1021"=>"User#editProFile", //修改信息
		"1022"=>"User#forgetPwd", //忘记密码
		"1023"=>"User#feedBack", //反馈

		"1024"=>"Address#addAddress",//增加快递地址
		"1025"=>"Address#getAddress",//返回该用户的地址列表
		"1026"=>"Address#setdefaultAddress",//设置默认地址
		"1027"=>"Address#removeAddress",//删除地址
		"1028"=>"Address#getdefaultAddress",//返回用户的默认地址


		"1029"=>"Order#preOrder",//选择购物车的商品 下单
		"1030"=>"Order#preOrderProductDetail",//Item List 选择购物车的商品 下单产品的具体列表
		"1031"=>"Order#Order",//确定下单


		"1032"=>"Order#MyOrder_On_Progress",//我的订单--On Progress
		"1033"=>"Order#MyOrder_On_Progress_detail",//我的订单--On Progress的某条具体记录
		"1034"=>"Order#MyOrder_On_Progress_cancel",//我的订单--On Progress  取消订单

		"1035"=>"Order#MyOrder_Approved",//我的订单--Approved  
		"1036"=>"Order#MyOrder_Approved_detail",//我的订单--Approved的某条具体记录
		"1037"=>"Order#MyOrder_Approved_detail_confirm2Delivery",//我的订单--Approved的某条具体记录 ---Comfirm to Delivery
		"1038"=>"Order#agentorder_OnProgress",//Agent Order--on progress列表
		"1039"=>"Order#agentorder_OnProgress_detail",//Agent Order--on progress列表--某条具体记录

		"1040"=>"Order#agentorder_OnProgress_detail_cancel",//Agent Order--on progress列表--某条具体记录----取消订单


		"1041"=>"Order#agentorder_OnProgress_detail_submit",//Agent Order--on progress列表--某条具体记录----确定订单
		"1042"=>"Order#agentorder_Approved",//Agent Order--Approved列表
		"1043"=>"Order#agentorder_Approved_detail",//Agent Order--Approved列表--某条具体记录

		"1044"=>"Order#MyOrder_Delivering",//我的订单--Delivering列表
		"1045"=>"Order#MyOrder_Delivering_detail",//我的订单--Delivering列表--具体记录
		"1046"=>"Order#MyOrder_Complete",//我的订单--Complete列表
		"1047"=>"Order#MyOrder_Complete_detail",//我的订单--Complete列表--具体记录
		"1048"=>"Order#MyOrder_Comfirm_receipt",//我的订单--Comfirm receipt
		"1049"=>"Order#MyOrder_Comfirm_receipt_submit",//我的订单--Comfirm receipt--提交评论

		"1050"=>"Order#agentorder_Delivering",//Agent Order--Delivering列表
		"1051"=>"Order#agentorder_Delivering_detail",//Agent Order--Delivering列表--某条具体记录
		"1052"=>"Order#agentorder_Complete",//Agent Order--Complete列表
		"1053"=>"Order#agentorder_Complete_detail",//Agent Order--Complete列表--某条记录
		
		"1054"=>"User#Profile_unread",//Profile--My Order/Agent Order未读记录数

		"1055"=>"Order#agentorder_Confirm2Delivery",//Agent Order--Delivering列表--具体记录---Confirm to Delivery
		"1056"=>"Order#agentorder_Complete_detail_viewReceipt",//Agent Order--Complete列表--某条记录---View Receipt
		"1057"=>"Order#MyOrder_view_receipt",//我的订单--查看评论

		"1058"=>"Address#alterAddress",//修改用户地址
		"1059"=>"Message#msgList",//用户消息列表
		"1060"=>"Message#msgDetail",//用户消息详情
		"1061"=>"User#ndBuildMd",//new dealer 绑定 MD
		"1062"=>"User#mdList",// MD列表
		"1063"=>"User#storeInfo", //店铺信息
		"1064"=>"Search#search",//搜索商品
		"1065"=>"Search#lastedSearch",//搜索历史
		"1066"=>"Search#mostViewed",//最多搜索
		"1067"=>"MasterSetting#aboutUs",
		"1068"=>"MasterSetting#termsOfService",


        //talk
     "13000"=>"Talk#login", //hippo客服登录
     //长连接
    "13001"=>"Talk#wsLogin", //hippo客服长连接登录
    "13002"=>"Talk#sendToUid",
     "13010"=>"User#login", //用户登录
     "13011"=>"User#sendToUid", //用户发消息




	); 

global $routesSw;
$routesSw = array(
	// 'admin/'=>"Admin#autoRoutes"
	);
?>