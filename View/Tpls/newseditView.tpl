<!DOCTYPE html>
    <html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width,initial-scale=1.0">
        <title>add news</title>
        <link href="/css/admin/add.css" rel="stylesheet" type="text/css" />

        <script type="text/javascript" src="/js/editor/ueditor.config.js"></script>
        <script type="text/javascript" src="/js/editor/ueditor.all.min.js"></script>
        <script type="text/javascript" src="/js/editor/lang/en/en.js"></script>
        

    </head>
    <body>
<a href="javascript:history.go(-1)">back</a>
    <form action="" method="post" class="basic-grey news-add"  id="uploadForm" enctype="multipart/form-data" >

      
          <label>
               <span>title</span>
               <input id="rfid1" type="text" name="title"  value="<?php echo $data->title;?>" />
           </label>

          <label>
               <span>description</span>
               <input id="rfid1" type="text" name="desc" value="<?php echo $data->desc;?>"  />
           </label>

         <label>
            <span>type</span>
            <select name ='category_id'>
           <?php
                foreach ($category  as  $key => $value){
                        if($data->cid == $value->id){
                           echo  "<option value ='{$value->id}' selected='selected' >{$value->name}</option>";    
                        }else{
                            echo "<option value ='{$value->id}'  >{$value->name}</option>";    
                        }
                }
           ?>
            </select>
        </label>
           
             <label>
        <span></span>
           <img src="<?php  echo '/'.$data->img; ?>"  alt="no image for this news" width='600px'height='400' />
        </label>
<br>
       <label>
          <span>product Image</span>
          <input id="rfid1" type="file" name = 'news_pic'/>
      </label>


        <div  id="url_type_click"></div>
        <script id="editor" type="text/plain" style="width:100%;height:500px;margin-top:20px;  "></script>



        <label>
        <span>&nbsp;</span>
        <input type="button" class="button" onclick="send(<?php  echo $data->id;?>)" value="save" />
        </label>

    </form>



    </body>
    </html>


<script>
           $("#editor").html('<?php  echo html_entity_decode($data->body); ?>');
            var ue = UE.getEditor('editor');
            function send(id) {
                    var formData = new FormData($("#uploadForm")[0]);
                    formData.append("body", UE.getEditor('editor').getContent());
                    $.ajax({
                        url: '/Admin/news?action=Edit&id='+ id ,
                        type: 'POST',
                        data: formData,
                        async: false,
                        cache: false,
                        dataType:'json',
                        contentType: false,
                        processData: false,
                        success: function (returndata) {
                            alert(returndata.msg);
                            if(returndata.code){ window.location.href="/Admin/news?action=List&page=" + "<?php echo $_GET['page'];?>"; }
                        }
                    });

                }
            

        </script>