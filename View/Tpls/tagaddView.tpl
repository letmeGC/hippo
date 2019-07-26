<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>Tag Add</title>
    <style>

        .basic-grey {
            margin-left:auto;
            margin-right:auto;
            max-width: 700px;
            background: #F7F7F7;
            padding: 25px 15px 25px 10px;
            font: 12px Georgia, "Times New Roman", Times, serif;
            color: #888;
            text-shadow: 1px 1px 1px #FFF;
            border:1px solid #E4E4E4;
        }
        .basic-grey h1 {
            font-size: 25px;
            padding: 0px 0px 10px 40px;
            display: block;
            border-bottom:1px solid #E4E4E4;
            margin: -10px -15px 30px -10px;;
            color: #888;
        }
        .basic-grey h1>span {
            display: block;
            font-size: 11px;
        }
        .basic-grey label {
            display: block;
            margin: 0px;
        }
        .basic-grey label>span {
            float: left;
            width: 20%;
            text-align: right;
            padding-right: 10px;
            margin-top: 10px;
            color: #888;
        }
        .basic-grey input[type="text"], .basic-grey input[type="email"], .basic-grey textarea, .basic-grey select {
            border: 1px solid #DADADA;
            color: #888;
            height: 30px;
            margin-bottom: 16px;
            margin-right: 6px;
            margin-top: 2px;
            outline: 0 none;
            padding: 3px 3px 3px 5px;
            width: 70%;
            font-size: 12px;
            line-height:15px;
            box-shadow: inset 0px 1px 4px #ECECEC;
            -moz-box-shadow: inset 0px 1px 4px #ECECEC;
            -webkit-box-shadow: inset 0px 1px 4px #ECECEC;
        }
        .basic-grey textarea{
            padding: 5px 3px 3px 5px;
        }
        .basic-grey select {
            background: #FFF url('down-arrow.png') no-repeat right;
            background: #FFF url('down-arrow.png') no-repeat right);
            appearance:none;
            -webkit-appearance:none;
            -moz-appearance: none;
            text-indent: 1px;
            text-overflow: '';
            width: 70%;
            height: 35px;
            line-height: 25px;
        }
        .basic-grey textarea{
            height:100px;
        }
        .basic-grey .button {
            background: #E27575;
            border: none;
            padding: 10px 25px 10px 25px;
            color: #FFF;
            box-shadow: 1px 1px 5px #B6B6B6;
            border-radius: 3px;
            text-shadow: 1px 1px 1px #9E3F3F;
            cursor: pointer;
        }
        .basic-grey .button:hover {
            background: #CF7A7A
        }


    </style>
    <script>

        function send() {
            var formData = new FormData($("#add_tag")[0]);
            $.ajax({

                url: '/Admin/tagAdd' ,
                type: 'POST',
                data: formData,
                async: false,
                cache: false,
                dataType:'json',
                contentType: false,
                processData: false,
                success: function (data) {
                    alert(data.msg);
                    if(data.code){
                         window.location.href="/Admin/tagList";
                     }
                }
            });

        }

    </script>

</head>
<body>

<form id="add_tag" class="basic-grey">
    <h1>ADD Tag
        <span></span>
    </h1>

    <label>
        <span>Name</span>
        <input id="name" type="text" name="name"  />
    </label>

 <br />

        <?php
            foreach ($tab  as  $key => $value):
        ?>
        <label>
             <span><input type="checkbox" name="tab[]" value=" <?php echo  $value->id; ?>"  style="width:12px;height:12px;"  /> <?php echo $value->name?></span>
            <input  type='text'  class ='sort'  name ="<?php echo $value->id.'-sort';?>" style='width: 300px;'  placeholder='sort' />
        </label>

  <br />

        <?php endforeach; ?>


    <label>
        <span>Type</span>
        <select name="type">
            <option value ="">select type</option>
            <option value ="1">product list view</option>
            <option value ="2">brand list view</option>
            <option value ="3">promotion - hour of day</option>
            <option value ="4">promotion - day of week</option>
            <option value ="5">promotion - month of year</option>
        </select>
    </label>

    <br />

    <label>
        <span>&nbsp;</span>
        <input type="button" class="button" onclick="send()" value="commit" />
    </label>

</form>



</body>
</html>

