<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>TagEdit</title>
    <style>

        .basic-grey {
            margin-left:auto;
            margin-right:auto;
            max-width: 800px;
            background: #F7F7F7;
            padding: 25px 15px 25px 10px;
            font: 15px Georgia, "Times New Roman", Times, serif;
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

        function edit(id) {
            var formData = new FormData($("#add_tag")[0]);
            formData.append("action", 1);
            formData.append("id", id);
            $.ajax({
                url: '/Admin/tagEdit' ,
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

    <h1>Edit Tag
        <span></span>
    </h1>

    <label>
        <span>Name</span>
        <input id="name" type="text" name="name" value="<?php echo  $data->name;?>" />
    </label>

<br />

    <?php

             foreach ($tab  as  $key => $value){
                 if(in_array($value->id,$tab_id)){
                        echo " <label><span><input type='checkbox' name='tab[]' value='".$value->id."'  style='width:11px;height:11px;'  checked = 'checked'  /> {$value->name}</span></label>
                      <input  type='text'  class ='sort' name ='{$value->id}-sort' style='width: 300px;'  value ='{$sort[$value->id]}' />
                                 <br/>";
                 }else{

                                 echo " <label><span> <input type='checkbox' name='tab[]' value='".$value->id."'  style='width:11px;height:11px;'   />  {$value->name}</span></label>
                                <input  type='text'  class ='sort' name ='{$value->id}-sort' style='width: 300px;'  />
                              <br/>";

                  }

             }
        ?>


<br />

    <label>
        <span>Type</span>
        <select name="type">
            <option value ="">select type</option>
            <option value ="1"  <?php  echo $data->type == 1? "selected='selected'":''; ?>  > product list view</option>
            <option value ="2" <?php  echo $data->type == 2 ? "selected='selected'":''; ?>>brand list view</option>
            <option value ="3" <?php  echo $data->type ==3? "selected='selected'":''; ?> >promotion - hour of day</option>
            <option value ="4" <?php  echo $data->type == 4? "selected='selected'":''; ?>>promotion - day of week</option>
            <option value ="5" <?php  echo $data->type == 5? "selected='selected'":''; ?>>promotion - month of year</option>
        </select>
    </label>

    <br />

    <label>
        <span>&nbsp;</span>
        <input type="button" class="button" onclick="edit(<?php echo $data->id?>)" value="save" />
    </label>

</form>



</body>
</html>

