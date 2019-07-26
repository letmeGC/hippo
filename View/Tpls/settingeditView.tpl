<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>my-project</title>

    <link href="/css/admin/add.css" rel="stylesheet" type="text/css" />
    <script>

        function send(id) {
            var formData = new FormData($("#am")[0]);
            formData.append("id", id);

            $.ajax({
                url: '/Admin/masterSettingEdit' ,
                type: 'POST',
                data: formData,
                async: false,
                cache: false,
                dataType:'json',
                contentType: false,
                processData: false,
                success: function (data) {

                    if(data.code){
                        window.location.href='/Admin/masterSettingList';
                    }else{
                        alert(data.msg);
                    }

                }

            });
        }

    </script>
</head>

<body>
<a href="javascript:history.go(-1)">返回上一步</a>

<form action="" method="post" class="basic-grey" id="am" enctype="multipart/form-data">



    <label>
        <span>name</span>
        <input  type="text" name="name" value="<?php echo $data->name; ?>" />
    </label>


    <label>
        <span>content</span>

        <textarea rows="3" cols="20" name="content">
<?php  echo $data->content ;?>
</textarea>
    </label>






    <label>
        <span>&nbsp;</span>
        <input type="button" class="button" onclick="send(<?php echo $data->id;?>)" value="save" />
    </label>

</form>



</body>
</html>


