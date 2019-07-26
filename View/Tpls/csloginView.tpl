<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<head>
    <title>Customer Service</title>
    <link rel="stylesheet" href="/css/cs/login.css" />
    <script type="text/javascript" src="/js/jquery.min.js"></script>
    <script type="text/javascript">

      function  send(){
               var name =  $("#username").val();
              var   password  =  $("#password").val();
          $.post("/User/talkLogin",{ "username" : name, "password" : password},
              function(data){
                console.log(data);
                  if(data.code){
                      window.location.href="/Cs/index";
                   }else{
                       alert(data.msg);
                  }
              },
              "json");
      }

    </script>
</head>
<body>

<div class="lg-container">
    <h1> Customer Service</h1>
    <form  id="lg-form" name="lg-form" method="post">

        <div>
            <label for="username">Username:</label>
            <input type="text" name="username" id="username" placeholder="username"/>
        </div>

        <div>
            <label for="password">Password:</label>
            <input type="password" name="password" id="password" placeholder="password" />
        </div>

        <div>
            <button type="submit" id="login" onclick="send()">Login</button>
        </div>

    </form>
    <div id="message"></div>
</div>


</body>
</html>


