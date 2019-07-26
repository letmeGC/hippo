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
        <script>
            var ue = UE.getEditor('editor');
            function send() {
             
                    var formData = new FormData($("#uploadForm")[0]);
                    formData.append("body", UE.getEditor('editor').getContent());
                    $.ajax({
                        url: '/Admin/news?action=Add' ,
                        type: 'POST',
                        data: formData,
                        async: false,
                        cache: false,
                        dataType:'json',
                        contentType: false,
                        processData: false,
                        success: function (returndata) {
                            alert(returndata.msg);
                            if(returndata.code){ window.location.href="/Admin/news"; }
                        }
                    });

                }
            

        </script>

    </head>
    <body>
<a href="javascript:history.go(-1)">back</a>
    <form action="" method="post" class="basic-grey news-add"  id="uploadForm" enctype="multipart/form-data" >

      
          <label>
               <span>title</span>
               <input id="rfid1" type="text" name="title"  />
           </label>

          <label>
               <span>description</span>
               <input id="rfid1" type="text" name="desc"  />
           </label>

         <label>
            <span>type</span>
            <select name ='category_id'>
                <option value ="0" selected="selected">select type</option>
           <?php  foreach ($category  as  $key => $value): ?>
                <option value ="<?php echo $value->id ?>" ><?php echo $value->name; ?></option>
           <?php  endforeach;?>    
            </select>
        </label>
           
           <label>
               <span>product Image</span>
               <input id="rfid1" type="file" name="news_pic"  />
           </label>

        <div  id="url_type_click"></div>
        <script id="editor" type="text/plain" style="width:100%;height:500px;margin-top:20px;  "></script>



        <label>
        <span>&nbsp;</span>
        <input type="button" class="button" onclick="send()" value="commit" />
        </label>

    </form>



    </body>
    </html>


