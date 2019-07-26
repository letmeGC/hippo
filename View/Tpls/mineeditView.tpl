<?php 
   	$__tplTbName = $__tplData->getName();
	$__tplTbData = $__tplData->getData();
	$__tplTbDesc = $__tplData->getDesc();
	$__tplTbColCount = $__tplData->getColCount();
	$__tplTbMaxPage = $__tplData->getMaxPage();

    require_once("cnf/const.php");
	
?>
  <script language="javascript" type="text/javascript" src="/js/My97DatePicker/WdatePicker.js"></script>
<script type="text/javascript" charset="utf-8" src="/js/editor/ueditor.config.js"></script>
    <script type="text/javascript" charset="utf-8" src="/js/editor/ueditor.all.min.js"> </script>
    <!--建议手动加在语言，避免在ie下有时因为加载语言失败导致编辑器加载失败-->
    <!--这里加载的语言文件会覆盖你在配置项目里添加的语言类型，比如你在配置项目里配置的是英文，这里加载的中文，那最后就是中文-->
    <!-- <script type="text/javascript" charset="utf-8" src="/js/editor/lang/zh-cn/zh-cn.js"></script> -->
   <script type="text/javascript" charset="utf-8" src="/js/editor/lang/en/en.js"></script>
     <script type="text/javascript">
        var ue = UE.getEditor('container', {
		});
    </script>

    <script type="text/javascript">
    function blurfunc(file,value)
    {
    	var price_low = parseInt($("#price_low").val());
    	var price_mid = parseInt($("#price_mid").val());
    	var price_high = parseInt($("#price_high").val());

    	switch(file)
    	{
    		case "price_low":
    			
    			if((price_low>price_mid) || (price_low>price_high))
    			{
    				alert("invalid data");
    			}
    			break;
    		case "price_mid":
    			if((price_mid<price_low) || (price_mid>price_high))
    			{
    				alert("invalid data");
    			}
    			break;
    		case "price_high":
    			if((price_high<price_low) || (price_high<price_mid))
    			{
    				alert("invalid data");
    			}
    			break;
    	}
    	
    }
    </script>
<form accept-charset="UTF-8" action="/Admin/mineupdate?tb=<?=$__tplTbName?>" class="stdform" enctype="multipart/form-data" method="post">
	<?php foreach ($__tplTbDesc as $key => $value): ?>
		<?php if ($value["IsUserData"] == true): ?>
			<?php continue; ?>
		<?php endif; ?>
		<p>
			      <?php if(  (($value["Field"]=='related')&&  (  $__tplTbName=='Product' ))||(($value["Field"]=='type')&&  (  $__tplTbName=='Choice' )) ): ?>
            <?php else: ?>
            <label><?=$value["Name"]?></label>
            <?php endif; ?>
			<span class="field">
				<?php if(isset($value["is_password"])?$value["is_password"]:false):?>
					<?php echo sprintf("<input class=\"normalinput\" id=\"data[%s]\" name=\"data[%s]\" size=\"30\" type=\"text\" value=\"\">",$value["Field"],$value["Field"]); ?>
				<?php elseif($value["Extra"]=="auto_increment" || $value["readonly"]==true): ?>
					<?php echo sprintf("<input class=\"normalinput\" id=\"data[%s]\" name=\"data[%s]\" size=\"30\" type=\"text\" value=\"%s\" readonly=\"readonly\">",$value["Field"],$value["Field"],$__tplTbData[$value["Field"]]); ?>
				<?php elseif(  ($value["Field"]=='product_choice')&&  (  $__tplTbName=='Product' ) ): ?>
					<?=$product_choice?>
				<?php elseif(  ($value["Field"]=='product_ptype')&&  (  $__tplTbName=='Product' ) ): ?>
					<?=$product_ptype?>
				<?php elseif(  ($value["Field"]=='product_tag')&&  (  $__tplTbName=='Product' ) ): ?>
					<?=$product_tag?>
				<?php elseif(  ($value["Field"]=='imgs')&&  (  $__tplTbName=='Product' ) ): ?>
					<?php  
				
                      $arr = explode(",",$__tplTbData[$value["Field"]]);
                        if(!empty($arr)){
                            foreach($arr as $v) {
                               echo "<img width=\"50px\"  height=\"50px\" src=\"".Consts::imgurlhttp.$v."\">&nbsp;&nbsp;&nbsp;&nbsp;";
                            }
                        }

						
                        echo "<br>\n".$imgs;

					?>
				<?php else: ?>

						<?php if ($value["FieldSelectType"]=="select"): ?>
							<?php echo sprintf("<select class=\"normalinput\" id=\"data[%s]\" name=\"data[%s]\"   >",$value["Field"],$value["Field"]); ?>
							<?php 
								foreach ($value["FieldSelectValue"] as $key => $val) {
									if($__tplTbData[$value["Field"]] == $key){
								     echo sprintf("<option value=\"%s\" selected=\"selected\" >%s</option>",$key,$val);
									}else
									{
								     echo sprintf("<option value=\"%s\"  >%s</option>",$key,$val);
									}
								}
							?>
							</select>
					<?php elseif( $value["FieldSelectType"]=='img'): ?>
					<?php
					echo sprintf("<img width=\"50px\"  height=\"50px\" src=\"%s\">",$__tplTbData[$value["Field"]]);  
					echo "<br>";
					 echo sprintf("<input  id=\"%s\" name=\"%s\" size=\"30\" type=\"file\" >",$value["Field"],$value["Field"]);  
					
					?>

					<?php elseif( $value["FieldSelectType"]=='time'): ?>
						<?php echo sprintf("<input class=\"normalinput\" id=\"data[%s]\" name=\"data[%s]\" size=\"30\" type=\"text\" value=\"%s\" onClick=\"WdatePicker({el:this,dateFmt:'yyyy-MM-dd HH:mm'})\" >",$value["Field"],$value["Field"],$__tplTbData[$value["Field"]]); ?>
					<?php elseif( $value["FieldSelectType"]=='textarea'): ?>
						<?php echo sprintf("<textarea  rows=\"7\" cols=\"20\" id=\"data[%s]\" name=\"data[%s]\" size=\"30\" >%s</textarea>",$value["Field"],$value["Field"],$__tplTbData[$value["Field"]]); ?>
					
					<?php elseif( $value["FieldSelectType"]=='ueditor'): ?>
						 <script   id="<?php echo  $value["Field"]; ?>" name="<?php echo  $value["Field"]; ?>" type="text/plain" style="width:800px;height:300px;"></script>
					<?php elseif( $value["FieldSelectType"]=='other'): ?>
						  <?=$__tplData->getOther($__tplTbData[$value["Field"]]);?>
          <?php elseif( $value["FieldSelectType"]=='no_display'): ?>
					<?php else: ?>
						<?php if(in_array($value["Field"],array('price_low','price_mid','price_high')) &&  $__tplTbName=='Product'): ?>
							<?php echo sprintf("<input class=\"normalinput\" onblur=\"blurfunc('".$value["Field"]."','".$__tplTbData[$value["Field"]]."')\" id=\"%s\" name=\"data[%s]\" size=\"30\" type=\"text\"   value=\"%s\">",$value["Field"],$value["Field"],$__tplTbData[$value["Field"]]); ?>

						<?php elseif(in_array($value["Field"],array('password')) ): ?>
              <?php echo sprintf("<input class=\"normalinput\" id=\"data[%s]\" name=\"data[%s]\" size=\"30\" type=\"text\" value=\"\">",$value["Field"],$value["Field"]); ?>
            <?php elseif(in_array($value["Field"],array('dealer_code')) ): ?>
              <?php echo sprintf("<input readonly=\"readonly\" style=\"background:#CCCCCC\" class=\"normalinput\"  id=\"data[%s]\" name=\"data[%s]\" size=\"30\" type=\"text\" value=\"%s\">",$value["Field"],$value["Field"],$__tplTbData[$value["Field"]]); ?>
              
						<?php else: ?>
                       		<?php echo sprintf("<input class=\"normalinput\" id=\"data[%s]\" name=\"data[%s]\" size=\"30\" type=\"text\" value=\"%s\">",$value["Field"],$value["Field"],$__tplTbData[$value["Field"]]); ?>
					
							

                    	<?php endif; ?>
						
					<?php endif; ?>
					
				<?php endif; ?>	
			</span>
		</p>
	<?php endforeach; ?>
	<p class="stdformbutton"><input class="radius2" id="paper_submit" name="commit" type="submit" value="Submit"><input id="paper_submit" name="commit" type="reset" value="Clear"></p></form>

<script type="text/javascript">

    //实例化编辑器
    //建议使用工厂方法getEditor创建和引用编辑器实例，如果在某个闭包下引用该编辑器，直接调用UE.getEditor('editor')就能拿到相关的实例
  //   var ue = UE.getEditor('rich_text');

  // $(document).ready(function(){  
  //       var ue = UE.getEditor('rich_text');  
          
  //       ue.ready(function() {//编辑器初始化完成再赋值  
  //           ue.setContent('<?=$__tplTbData['rich_text']?>');  //赋值给UEditor  

  //       });  
          
  //   }); 
  //   
    var ue = UE.getEditor('spesification');

  $(document).ready(function(){  
        var ue = UE.getEditor('spesification');  
      
        ue.ready(function() {//编辑器初始化完成再赋值  
            ue.setContent('<?=$__tplTbData["spesification"]?>');  //赋值给UEditor  
        });  
          
    }); 

  var ue = UE.getEditor('introduction');

  $(document).ready(function(){  
        var ue = UE.getEditor('introduction');  
          
        ue.ready(function() {//编辑器初始化完成再赋值  
            ue.setContent('<?=$__tplTbData["introduction"]?>');  //赋值给UEditor  
        });  
          
    });

  var ue = UE.getEditor('warranty');

  $(document).ready(function(){  
        var ue = UE.getEditor('warranty');  
          
        ue.ready(function() {//编辑器初始化完成再赋值  
            ue.setContent('<?=$__tplTbData["warranty"]?>');  //赋值给UEditor  
        });  
          
    });

  var ue = UE.getEditor('related');

  $(document).ready(function(){  
        var ue = UE.getEditor('related');  
          
        ue.ready(function() {//编辑器初始化完成再赋值  
            ue.setContent('<?=$__tplTbData["related"]?>');  //赋值给UEditor  
        });  
          
    });
 
 
</script>
