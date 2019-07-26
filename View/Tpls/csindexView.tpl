<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Customer Service</title>
    <link rel="stylesheet" href="/css/cs/login.css" />
    <link rel="stylesheet" href="/css/cs/talk.css" />

    <style>
        /**重置标签默认样式*/
        * {
            margin: 0;
            padding: 0;
            list-style: none;
            font-family: '微软雅黑'
        }
        .container {
            width: 65%;
            height: 780px;
            background: #eee;
            margin: 80px auto 0;
            position: relative;
            box-shadow: 20px 20px 55px #777;
        }
        .header {
            background: #000;
            height: 40px;
            color: #fff;
            line-height: 34px;
            font-size: 20px;
            padding: 0 10px;
        }
        .footer {
            width: 97%;
            height: 50px;
            background: #666;
            position: absolute;
            bottom: 0;
            padding: 10px;
        }
        .footer input {
            width: 90%;
            height: 45px;
            outline: none;
            font-size: 20px;
            text-indent: 10px;
            position: absolute;
            border-radius: 6px;
            right: 80px;
        }
        .footer span {
            display: inline-block;
            width: 62px;
            height: 48px;
            background: #ccc;
            font-weight: 900;
            line-height: 45px;
            cursor: pointer;
            text-align: center;
            position: absolute;
            right: 10px;
            border-radius: 6px;
        }
        .footer span:hover {
            color: #fff;
            background: #999;
        }
        #user_face_icon {
            display: inline-block;
            background: red;
            width: 60px;
            height: 60px;
            border-radius: 30px;
            position: absolute;
            bottom: 6px;
            left: 14px;
            cursor: pointer;
            overflow: hidden;
        }
        img {
            width: 60px;
            height: 60px;
        }
        .content {
            font-size: 20px;
            width: 99%;
            height: 662px;
            overflow: auto;
            padding: 5px;
        }
        .content li {
            margin-top: 10px;
            padding-left: 10px;
            width: 99%;
            display: block;
            clear: both;
            overflow: hidden;
        }
        .content li img {
            float: left;
        }
        .content li span{
            background: #7cfc00;
            padding: 10px;
            border-radius: 10px;
            float: left;
            margin: 6px 10px 0 10px;
            max-width: 310px;
            border: 1px solid #ccc;
            box-shadow: 0 0 3px #ccc;
        }
        .content li img.imgleft {
            float: left;
        }
        .content li img.imgright {
            float: right;
        }
        .content li span.spanleft {
            float: left;
            background: #fff;
        }
        .content li span.spanright {
            float: right;
            background: #7cfc00;
        }

        .content li div.divleft {
            float: left;
            background: #66FFFF;
            max-width: 300px;
            word-wrap:break-word; 
            word-break:break-all; 
            padding:10px 20px;
            border-radius:5px;
        }

        .content li div.divright {
            float: right;
            background: #7cfc00;
            max-width: 300px;
            word-wrap:break-word; 
            word-break:break-all; 
            padding:10px 20px;
            border-radius:5px;
        }
        
    </style>
    <script type="text/javascript" src="/js/jquery.min.js"></script>

        <script type="text/javascript">


        ws = new WebSocket("ws://192.168.1.103:65160");
        // 当socket连接打开时，输入用户名
        ws.onopen = onopen;
        // 当有消息时根据消息类型显示不同信息
        ws.onmessage = onmessage;

         function onopen(e) {
            var tk = "<?php  echo $tk;?>";
            var uid = "<?php  echo $user->id;?>";
            ws.send('{ "path":"13001","d":{ "tk": "'+ tk +'",' + '"fromUser":"'+ uid +'"}}');
        };

        function onmessage(e)
        {

           
            var data = JSON.parse(e.data);

            var msg ='';
            if(data.type == 'ping'){ return;}
            if(data.type == 'init'){ return;}

            var toUser = data.fromUser;
            var fromName = data.fromName;
            var User_list_arr=new Array()
            $(".User_list").each(function(){
                 User_list_arr.push($(this).val());
             });
            if($.inArray(toUser, User_list_arr))
            {
                 //有新对话
                 fun_append_html(toUser,fromName);
            }
        
            


            switch(data.type){
                // 发言
                case '0':
                    msg = data.msg;
                    break;
                // 发言
                case '1':
                    msg = "<a href='"+data.msg+"'>"+data.msg+"</a>";
                    break;
                case '2':
                    msg = "< img src = '"+data.msg+"' width ='50' height='50'  />";
                    break;
            }
            // var content = document.getElementsByTagName('ul')[0];
            
            var content_id = "content_"+toUser;
            var content = document.getElementById(content_id);


            content.innerHTML += '<li><img  class ="imgleft" src=""><div class="divleft"><font size="4">'+msg+'</font></div></li>';
            text.value = '';
            // 内容过多时,将滚动条放置到最底端
            content.scrollTop=content.scrollHeight;
        }

        function p(s) {
            return s < 10 ? '0' + s: s;
        }

        //返回当前时间
        function re_time()
        {
            var myDate = new Date();
            //获取当前年
            var year=myDate.getFullYear();
            //获取当前月
            var month=myDate.getMonth()+1;
            //获取当前日
            var date=myDate.getDate(); 
            var h=myDate.getHours();       //获取当前小时数(0-23)
            var m=myDate.getMinutes();     //获取当前分钟数(0-59)
            var s=myDate.getSeconds();  

            var now=year+'-'+p(month)+"-"+p(date)+" "+p(h)+':'+p(m)+":"+p(s);
            return now;
        }


        function startTalk(toUser)
        {
            $('.container').hide();
             $('#container_id_'+toUser).css('display','show');
        }

        function fun_append_html(toUser,fromName)
        {
            
            //增加div框
            $body_html = "<div class=\"container\" id=\"container_id_"+toUser+"\"  style=\"display: none\"><div class=\"header\"><span style=\"float: left;\">"+fromName+"</span><span style=\"float: right;\">14:21</span></div><ul class=\"content\"  id=\"content_"+toUser+"\"></ul><div class=\"footer\"><input id=\"text_"+toUser+"\" type=\"text\" placeholder=\"说点什么吧...\"><span id=\"btn\" onclick=\"sendMsg("+toUser+")\">发送</span></div></div>";
            $("#body").append($body_html);

            //增加user list
            $div_list_html ="<div  style=\"cursor:pointer;\" onclick=\"startTalk("+toUser+")\">" + fromName + "</div><input type=\"hidden\" class=\"User_list\" value='"+toUser+"'>";
            $("#div_list").append($div_list_html);





        }

        // 发送聊天消息
        function sendMsg(toUser){
           
            //    var User_list_arr=new Array()
            // $(".User_list").each(function(){
            //      User_list_arr.push($(this).val());
            //  });
            // if($.inArray(toUser, User_list_arr))
            // {
            //     //有新对话
            //     fun_append_html('1111');
            // }
            var content_id = "content_"+toUser;
            var content = document.getElementById(content_id);
           
              var tk = "<?php  echo $tk;?>";
              var uid = "<?php  echo $user->id;?>";
              var username = "<?php  echo $user->username;?>";
              var text =$("#text_"+toUser);
            console.log(username);

            if(text.value ==''){
                alert('不能发送空消息');
            }else {
                var inputvalue = $("#text_"+toUser).val();
                console.log(' { "path" : "13002","d" : { "tk": "'+ tk +'",' + '"fromName":"'+ username + '","fromUser":"'+ uid +'","toUser" :'+toUser+',"msgType" :"0", "msg" : "' + inputvalue+'"}}');
                ws.send(' { "path" : "13002","d" : { "tk": "'+ tk +'",' + '"fromName":"'+ username + '","fromUser":"'+ uid +'","toUser" :'+toUser+',"msgType" :"0", "msg" : "' + inputvalue+'"}}');
                
                content.innerHTML += '<li><img  class ="imgright" src=""><div class="divright"><font size="1" color="red">'+re_time()+'</font><br><font size="4">'+inputvalue+'</font></div></li>';
                    
                $("#text_"+toUser).val('');
                content.scrollTop=content.scrollHeight;
            }

        }

        function keySend(event,uid) {
            if (event.keyCode == 13) {
                 sendMsg(uid);
            }
        }

    </script>
</head>
<body id="body">

<div  id="div_list">

    <ul>
<?php
 
    foreach($data  as $key=>$value):
   
?>

<input type="hidden" class="User_list" value='<?php  echo $value->id;?>'>

    <li class="block" style="cursor:pointer;"  onclick="startTalk(<?php  echo $value->id;?>)" >
        <label for="item1">
            <i aria-hidden="true" class="icon-users"></i> <?php echo $value->nickname ; ?>
          <!--  <span>124</span>-->
        </label>

    </li>


    <?php  endforeach;?>

    </ul>
</div>


<?php
    $i =0;
    foreach($data  as $key=>$value):
    $style = ($i==0)? "style=\"display: show\"" :"style=\"display: none\"";
    $i++;
?>

 

<div class="container" id="container_id_<?=$value->id?>"  <?=$style?> ">
    <div class="header">
        <span style="float: left;"><?=$value->nickname?></span>
    </div>
    <ul class="content"  id="content_<?=$value->id?>">

    </ul>
    <div class="footer">
        <input id="text_<?=$value->id?>" type="text" placeholder="说点什么吧..."  onkeydown="keySend(event,'<?=$value->id?>')" />
        <span id="btn" onclick="sendMsg('<?=$value->id?>')"    >send</span>
    </div>
</div>


<?php  endforeach;?>




</body>
</html>


