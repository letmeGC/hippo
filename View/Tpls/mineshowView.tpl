
    <?php 
        $__tplTbName = $__tplData->getName();
        $__tplTbData = $__tplData->getData();
        $__tplTbDesc = $__tplData->getDesc();
        $__tplTbColCount = $__tplData->getColCount();
        $__tplTbMaxPage = $__tplData->getMaxPage();
        require_once("cnf/const.php");

    ?>

    <SCRIPT LANGUAGE=javascript> 
        function delconfirm() { 
            var msg = "您真的确定要删除吗？\n\n请确认！"; 
            if (confirm(msg)==true){ 
                return true; 
            }else{ 
            return false; 
            } 
        } 
    </SCRIPT> 

      <div class="filemgr_head" style="padding-left:0px;">
       <ul class="filemgr_menu">
        <li class="marginleft0"><a href="<?=$__tplData->getNewUrl()?>">ADD NEW</a></li>
       </ul>
       <span class="clearall"></span>
      </div>



    <table border="0" cellpadding="0" cellspacing="0" class="stdtable">
    <!-- <caption><?=$__tplTbName?></caption> -->
        <thead style="background-color: #f7f7f7;">
            <tr>
                <?php foreach ($__tplTbDesc as $key=>$value): ?>
                <?php if(isset($value["is_password"])?$value["is_password"]:false): ?>
                    <?php continue; ?>
                <?php elseif(in_array($value["Name"],array('product_spec','product_type','product_tag','spesification','introduction','warranty','related'))): ?>
                    <?php continue; ?>  
                <?php elseif(in_array($value["Name"],array('type'))&&$__tplTbName=='Choice'): ?>
                    <?php continue; ?>  
                <?php endif; ?>
                    <th scope="col"><?=$value["Name"]?></th>
                <?php endforeach;?>
                <th>Manager</th>
            </tr>
        </thead>
        <tbody>
             <?php foreach ($__tplTbData as $key => $value): ?>
                <tr>
                <?php for ($i=0;$i<$__tplTbColCount;$i++): ?>
                    <?php if(isset($__tplTbDesc[$i]["is_password"])?$__tplTbDesc[$i]["is_password"]:false): ?>
                        <?php continue; ?>
                    <?php endif; ?>
                    <?php if ($__tplTbDesc[$i]["Field"]=='id') :?>
                        <td>
                            <?= "<a href=\"".$__tplData->getEditUrl($value[$__tplTbDesc[$i]["Field"]])."\"  title=\"edit\"/>".$value[$__tplTbDesc[$i]["Field"]]."</a>" ?>
                        </td>
                    <?php elseif(($__tplTbDesc[$i]["Field"]=='product_choice' || $__tplTbDesc[$i]["Field"]=='product_ptype' || $__tplTbDesc[$i]["Field"]=='product_tag'  || in_array($__tplTbDesc[$i]["Field"],array('spesification','introduction','warranty','related') )) &&  $__tplTbName=='Product'): ?>
                    <?php elseif(in_array($__tplTbDesc[$i]["Field"],array('type'))&&$__tplTbName=='Choice'): ?>
                     

                    <?php elseif($__tplTbDesc[$i]["Field"]=='imgs' &&  $__tplTbName=='Product'): ?>
                        <td>
                            <?php 
                                $arr = explode(",",$value[$__tplTbDesc[$i]["Field"]]);
                                if(!empty($arr)){
                                    foreach($arr as $v) {
                                       echo "<img width=\"30px\"  height=\"30px\" src=\"".Consts::imgurlhttp.$v."\">";
                                    }
                                }

                                 

                            ?>
                        </td>

                    <?php else: ?>
                      <?php if ($__tplTbDesc[$i]["FieldSelectType"]=="select"): ?>
                          <td><?=$__tplTbDesc[$i]["FieldSelectValue"][$value[$__tplTbDesc[$i]["Field"]]] ?></td>
                        <?php elseif( $__tplTbDesc[$i]["FieldSelectType"]=='ueditor'): ?>
                            <td></td>
                       
                        
                         <?php elseif( $__tplTbDesc[$i]["FieldSelectType"]=='img'): ?>
                            <td><img width="30px"  height="30px" src="<?=$value[$__tplTbDesc[$i]["Field"]]?>"/></td>
                       <?php else: ?>
                         <td><?=$value[$__tplTbDesc[$i]["Field"]] ?></td>
                       <?php endif; ?>
                    <?php endif; ?>
                <?php endfor; ?>
                <td>
                    <?php echo sprintf("<a href=\"%s\" class=\"edit_record\" title=\"edit\"/>",$__tplData->getEditUrl($value["id"])); ?>
                    <?php if($__tplTbName!='Game'): ?>
                        <?php echo sprintf("<a href=\"%s\" class=\"delete_record\"  onclick=\"javascript:return delconfirm()\" title=\"delete\"/>",$__tplData->getDeleteUrl($value["id"])); ?>
                    <?php endif; ?>

                </td>
                </tr>
            <?php endforeach;?>
        </tbody>
    </table>
    <br/>
    <br/>
    <?php if ($__tplTbMaxPage>1): ?>
        <nav class="pagination">
        <?php for ($i=-5;$i<=5;$i++): ?>
            <?php $id=$_REQUEST["page"]+$i; ?>
            <?php if ($id<1 || $id >$__tplTbMaxPage): ?>
                <?php continue; ?>          
            <?php endif; ?>
            <?php if ($i==0): ?>
                <span class="page current"><?=$id?></span>
            <?php else: ?>
                <span class="page"><a href="<?=$__tplData->getPageUrl($id)?>" rel="next"><?=$id?></a></span>
            <?php endif; ?>
        <?php endfor; ?>
        </nav>
    <?php endif ?>
</div>