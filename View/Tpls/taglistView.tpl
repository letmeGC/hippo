<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>订单列表</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link href="/css/admin/list.css" rel="stylesheet" type="text/css" />
    <link href="/css/admin/page.css" rel="stylesheet" type="text/css" />

    <script>
        function   aliParcel( parcel,orderId ){
            console.log(parcel,orderId);
            $.post("/Admin/aliParcel",{ 'parcel':parcel, 'order_id' :  orderId },
                function(data){
                    console.log(data);
                    if(data.ret[0].d.code == 1){
                        var ct = data.ret[0].d.data.result.list.length;
                        var str = "";

                        for( var i =0; i< ct; i++){
                            str +=  data.ret[0].d.data.result.list[i].time + '  ' +  data.ret[0].d.data.result.list[i].status  + "\n"
                        }
                        alert(str);
                        if(  data.ret[0].d.data.result.deliverystatus == 3){
                            document.getElementById("status" + orderId ).innerHTML = '已签收' ;
                        }
                    }else{
                        alert('暂无');
                    }
                },
                "json");
        }


        function search() {
            var parcel = $('#parcel').val();
            var order_status = $('#order_status').val();
            window.location.href="/Admin/orderList?parcel=" + parcel + '&order_status=' + order_status;
        }
        function addparcel(orderId) {
            var  parcel_code  =  prompt("请输入运单号:","");
            if (parcel_code  != null){
                $.post("/Admin/addParcel",{ 'parcel': parcel_code, 'order_id' :  orderId },
                    function(data){
                        alert(data.ret[0].d.msg);
                        document.getElementById("parcel" + orderId ).innerHTML = parcel_code ;
                    },
                    "json");
            }else{
                alert("不能为空");
            }
        }



        function del(id) {
            if(confirm("确认删除?")){
                $.post("/Admin/tagDel",{ 'id': id },
                    function(data){
                        alert(data.msg);
                        if(data.code){
                            window.location.href = '/Admin/tagList';
                        }

                    },
                    "json");
            }

        }

    </script>

</head>
<body>

<div class="filemgr_head" style="padding-left:0px;">
    <ul class="filemgr_menu">
        <li class="marginleft0"><a href="/Admin/tagAdd">ADD NEW</a></li>
    </ul>
    <span class="clearall"></span>
</div>


<div class="wrap">
    <table class="list-style Interlaced">
        <tr>
            <th>ID</th>
            <th>Tag name</th>
            <th>Location (tab name)</th>
            <th>Type of tag</th>
            <th>Sort</th>
            <th>Manager</th>
        </tr>

        <?php
          	$type = array( '1'=>'product list view', '2'=>'brand list view', '3'=>'promotion - hour of day','4'=>'promotion - day of week','5'=>'promotion - month of year','6'=>'Coming soon');
         foreach ($data  as  $key => $value): ?>
        <tr>
            <td  class="center" > <?php echo $value->id; ?> </td>
            <td  class="center" > <?php echo $value->name; ?> </td>
            <td class="center"> <?php echo $value->tab_name ;?> </td>
            <td class="center"> <?php echo $type[$value->type] ; ?> </td>
            <td class="center"> <?php  $array = explode(',',$value->mix_sort) ; echo max($array)>0  ?   $value->mix_sort  : ''; ?> </td>
            <td class="center">
                <a href="/Admin/tagEdit?id=<?php echo $value->id;?>" class="inline-block" title="编辑预览"><img src="/images/editor.png"/></a>
                 <a class="inline-block" title="删除" onclick="del(<?php echo $value->id ; ?>)"><img src="/images/admin/trash.png"/></a>
            </td>
        </tr>

        <?php endforeach; ?>
    </table>
</div>
    <?php  echo $page_str; ?>
</body>
</html>


