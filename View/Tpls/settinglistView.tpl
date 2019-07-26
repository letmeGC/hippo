<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>news list</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link href="/css/admin/list.css" rel="stylesheet" type="text/css" />

    <script src="/js/admin/public.js"></script>
</head>
<body>

<!--
<div class="filemgr_head" style="padding-left:0px;">
    <ul class="filemgr_menu">
        <li class="marginleft0"><a href="/Admin/masterSettingAdd">ADD NEW</a></li>
    </ul>
    <span class="clearall"></span>
</div>
-->
<div class="wrap">


    <table class="list-style Interlaced">
        <tr>
            <th>ID</th>
            <th>name</th>
            <th>content</th>
            <th>manage</th>
        </tr>

        <?php foreach ($data  as  $key => $value): ?>
        <tr>
            <td  class="center" > <?php echo $value->id; ?> </td>
            <td  class="center "  > <?php echo $value->name; ?> </td>
            <td  class="center "  > <?php echo $value->content; ?> </td>
            <td class="center">
                 &nbsp;<a class="inline-block" href="/Admin/masterSettingEdit?action=p&id=<?php echo $value->id; ?>" ><input type="button" value="edit & preview"></a>
            </td>

        </tr>

        <?php endforeach; ?>
    </table>


</body>
</html>


