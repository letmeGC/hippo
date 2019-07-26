<?php
if($_SESSION["id"]==1){
	return array(
		// 'index_page'=>'/Admin/index',
		'menu'=>array(
			'PRODUCT'=>array(
				'/Admin/mineshow?tb=product'=>'PRODUCT ITEM',//产品
				'/Admin/mineshow?tb=choice'=>'PRODUCT SPEC',//商品颜色，大小等选择
				'/Admin/mineshow?tb=ptype'=>'PRODUCT_TYPE',//产品种类  
				'/Admin/tagList'=>'PRODUCT_TAG',
				'/Admin/mineshow?tb=tag_brand'=>'BRAND_TAG',//品牌标签
				'/Admin/mineshow?tb=brand'=>'BRAND DETAIL',//商标
				
				
			//	'/Admin/mineshow?tb=tag'=>'tag',//第二级标签
				
				
				
				// '/Admin/news?action=List'=>'NEWS',
				
				
			),
			'PROMOTION'=>array(
				'/Admin/mineshow?tb=banner'=>'BANNER',//banner 
				'/Admin/mineshow?tb=tab'=>'HOME - TAB',//第一级标签
				'/Admin/mineshow?tb=selltime'=>'PROMOTIONAL ACTIVITIES',//促销时间
			),
			'MEMBERS'=>array(
				'/Admin/mineshow?tb=char'=>'DEALER INFO',//char 
				'/Admin/mineshow?tb=orders_cancel_reasons'=>'REASON of order revised',//banner
			),
			'NEWS'=>array(
				'/Admin/news?action=List'=>'NEWS LIST',
			),

            'SET'=>array(
                '/Admin/masterSettingList'=>'LIST',
            ),
			'Management'=>array(
				// '/Admin/show?tb=staff'=>'User',
				// '/Admin/show?tb=right'=>'Auth',
				// '/Admin/show?tb=group'=>'Group',
			),
		),
	);
}else{
	return array(
		// 'index_page'=>'/Admin/index',
		'menu'=>array(
			'PRODUCT'=>array(
				'/Admin/mineshow?tb=product'=>'PRODUCT ITEM',//产品
				'/Admin/mineshow?tb=choice'=>'PRODUCT SPEC',//商品颜色，大小等选择
				'/Admin/mineshow?tb=ptype'=>'PRODUCT_TYPE',//产品种类  
				'/Admin/tagList'=>'PRODUCT_TAG',
				'/Admin/mineshow?tb=tag_brand'=>'BRAND_TAG',//品牌标签
				'/Admin/mineshow?tb=brand'=>'BRAND DETAIL',//商标
				
				
			//	'/Admin/mineshow?tb=tag'=>'tag',//第二级标签
				
				
				
				// '/Admin/news?action=List'=>'NEWS',
				
				
			),
			'PROMOTION'=>array(
				'/Admin/mineshow?tb=banner'=>'BANNER',//banner 
				'/Admin/mineshow?tb=tab'=>'HOME - TAB',//第一级标签
				// '/Admin/mineshow?tb=selltime'=>'PROMOTIONAL ACTIVITIES',//促销时间
			),
			'MEMBERS'=>array(
				'/Admin/mineshow?tb=char'=>'DEALER INFO',//char 
				'/Admin/mineshow?tb=orders_cancel_reasons'=>'REASON of order revised',//banner
			),
			'NEWS'=>array(
				'/Admin/news?action=List'=>'NEWS LIST',
			),
            'SET'=>array(
                '/Admin/masterSettingList'=>'LIST',
            ),
			'Management'=>array(
				// '/Admin/show?tb=staff'=>'User',
				// '/Admin/show?tb=right'=>'Auth',
				// '/Admin/show?tb=group'=>'Group',
			),
		),
	);
}

?>