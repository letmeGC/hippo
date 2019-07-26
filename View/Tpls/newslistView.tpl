<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>news list</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link href="/css/admin/list.css" rel="stylesheet" type="text/css" />

    <script src="/js/admin/public.js"></script>
</head>
<body>

<div class="filemgr_head" style="padding-left:0px;">
    <ul class="filemgr_menu">
        <li class="marginleft0"><a href="/Admin/news?action=Add">ADD NEW</a></li>
    </ul>
    <span class="clearall"></span>
</div>
<div class="wrap">


  <div id="operate" style=" margin-bottom:8px;">
            <select id="search_category" style=" padding:4px;border:1px #999 solid;" >
                <option value="0" <?php echo $_REQUEST['search_category']? '' : 'selected = "selected"';?> >select type</option>
                <option value="1" <?php echo $_REQUEST['search_category'] == 1 ?'selected = "selected"': '';?> >Top Stories</option>
                <option value="2" <?php echo $_REQUEST['search_category'] == 2 ?'selected = "selected"': '';?> >New product</option>
                <option value="3" <?php echo $_REQUEST['search_category'] == 3 ?'selected = "selected"': '';?> >Tips</option>
                <option value="4" <?php echo $_REQUEST['search_category'] == 4 ?'selected = "selected"': '';?> >Bisnis</option>
                <option value="5" <?php echo $_REQUEST['search_category'] == 5 ?'selected = "selected"': '';?> >Display</option>
            </select>
            <input type="button" value="search" class="tdBtn" onclick="search('news?action=List','search_category')"/>
    </div>
    <table class="list-style Interlaced">
        <tr>
            <th>ID</th>
            <th>title</th>
            <th>description</th>
            <th>type</th>
            <th>count of clicks</th>
            <th>manage</th>
        </tr>

        <?php foreach ($data  as  $key => $value): ?>
        <tr>
            <td  class="center" > <?php echo $value->id; ?> </td>
            <td  class="center "  > <?php echo $value->title; ?> </td>
            <td  class="center "  > <?php echo $value->desc; ?> </td>
            <td  class="center "  > <?php echo $value->category_name; ?> </td>
            <td  class="center "  > <?php echo $value->hit; ?></td>
            <td class="center">
                 <a class="inline-block" title="删除" id="<?php echo $value->id; ?>" onclick="del(this.id,'news')"><img src="/images/admin/trash.png"/></a>
                 &nbsp;<a class="inline-block" href="/Admin/preview?table=news&id=<?php echo $value->id; ?>" ><input type="button" value="edit & preview"></a>
            </td>

        </tr>

        <?php endforeach; ?>
    </table>


    <div id ="edit_category">
        <form>
            <?php echo $category;?>
            <input type="hidden" value="" id = 'edit_category_id'>
            <input type="button" class="button" onclick="editCategory()" value="commit" />
            <input type="button" class="button" onclick="oc('none','xx')"  value="cancel" />
        </form>

    </div>


    <nav class="pagination">
        <?php
        $search_category = $_REQUEST['search_category'];
        for ($i = 0; $i < $pageNum; $i ++) {
            $s= $i+1;
            if($page == $s){
               echo "<span color='red'><a href= news?action=List&page=$s&search_category={$search_category}><h4 style='color:red;font-weight:bold;'>$s</h4></a></span>";
        }else{
        echo "<span><a href=news?action=List&page=$s&search_category={$search_category} ><h5>$s</h5></a></span>";
        }
        }
        ?>
    </nav>
</body>
</html>


