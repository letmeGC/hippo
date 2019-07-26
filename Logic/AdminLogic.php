<?php
class AdminLogic extends AdminLogicBase
{

     //显示表格列表
    public static function mineShow()
    {
        $table = $_REQUEST["tb"];
        $model = self::getMyModel($table);
        $ret  = array('__tplData'=>$model);

        // if($table == 'product')
        // {
        //     require_once("Models/Model_Product.php");

        //     $ret['product_choice'] = Model_Product::showproduct();

        // }

   
        self::renderPartial('mineshowView', $ret);
    }

      //新增数据
    public static function minecreate()
    {
        $table = $_REQUEST["tb"];
        $model = self::getMyModel($table);
        $tpl   = self::getTpl($table, 'create');
        $tpl = "mine".$tpl;
        $ret = array();
        $ret['__tplData'] = $model;
        $ret['__val'] = $val;

        if($table == 'Product')
        {
            require_once("Models/Model_Product.php");
            $ret['product_choice'] = Model_Product::show_product_choice();
            $ret['product_ptype'] = Model_Product::show_product_ptype();
            $ret['product_tag'] = Model_Product::show_product_tag();
            $ret['imgs'] = "<input type=\"file\"  name=\"imgs[]\"><input type=\"file\"  name=\"imgs[]\"><input type=\"file\"  name=\"imgs[]\"><input type=\"file\"  name=\"imgs[]\"><input type=\"file\"  name=\"imgs[]\">";
        }

        // if($table == 'Tag')
        // {
        //     require_once("Models/Model_Tag.php");
        //     $ret['tab_id'] = Model_Tag::show_tab_id();
        // }


        self::renderPartial($tpl, $ret);
    }


     //删除数据
    public static function minedelete()
    {
        $table = $_REQUEST["tb"];
        $id = $_REQUEST["id"];
       $model = self::getMyModel($table);
       $model::delete($id);
        
     header("Location: /Admin/mineShow?tb=".ucwords($table));
    }


     public static function mineedit(){
        $table = $_REQUEST["tb"];
        $model = self::getMyModel($table);
        $tpl   = self::getTpl($table, 'edit');
        $ret = array();
        $ret['__tplData'] = $model;
        $ret['__val'] = $val;
        $tpl   = "mine".$tpl;

        if($table == 'Product')
        {
            require_once("Models/Model_Product.php");
            $id = $_REQUEST["id"];
            $ret['product_choice'] = Model_Product::show_product_choice($id);
            $ret['product_ptype'] = Model_Product::show_product_ptype($id);
            $ret['product_tag'] = Model_Product::show_product_tag($id);
            $ret['imgs'] = "<input type=\"file\"  name=\"imgs[]\">\n<input type=\"file\"  name=\"imgs[]\">\n<input type=\"file\"  name=\"imgs[]\">\n<input type=\"file\"  name=\"imgs[]\">\n<input type=\"file\"  name=\"imgs[]\">\n";


        }

        self::renderPartial($tpl,$ret);
    }



    public static function mineupdate()
    {
     
        require_once("cnf/const.php");
        $table = $_REQUEST["tb"];
        $data = $_REQUEST["data"];
        lgj("data");
        lgj($data);
        lgj("datadatadatadatadatadatadatadata");
        

        if($_REQUEST['rich_text']!='')//处理富文本
        {
            $data['rich_text'] = $_REQUEST['rich_text'];
        }

        if($table == 'Product')
        {
            if($_REQUEST['spesification']!='')//处理富文本
            {
                $data['spesification'] = $_REQUEST['spesification'];
            }
            
            if($_REQUEST['introduction']!='')//处理富文本
            {
                $data['introduction'] = $_REQUEST['introduction'];
            }
            
            if($_REQUEST['warranty']!='')//处理富文本
            {
                $data['warranty'] = $_REQUEST['warranty'];
            }
            
            if($_REQUEST['related']!='')//处理富文本
            {
                $data['related'] = $_REQUEST['related'];
            }
        }

        if($table == 'Char')
        {

            $password = trim($data['password']);
            if(strlen($password) >0)
            {
                $data['password'] = md5($password);
            }else
            {
                 unset($data['password']);
            }
            if(strlen($data['dealer_code'])<=0)
            {
                require_once("Models/Model_Dealer_code.php");
                $data['dealer_code'] = Model_Dealer_code::create_dealer_code();
            }
            
        }
       
        $model = self::getMyModel($table);

      
        //存储图片
        foreach ($_FILES as $key => $value) {
            if(($key == 'imgs') && ($table == 'Product'))//多文件上传
            {
                lgj("ProductProductProductProductProductProduct");
                $file = $value;
                if(!empty($file['tmp_name']))
                {
                    $uploadimage1 = "productImgs2";//存储图片的文件夹
                    $str_imgs = "";
                    for ($i=0; $i <count($file['tmp_name']) ; $i++) 
                    { 
                        if($file['tmp_name'][$i] != "")
                        {
                            
                            if(is_uploaded_file($file['tmp_name'][$i])){
                                //如果是通过HTTP POST上传的
                                $upload_path = Consts::imgurl.$uploadimage1."/".date('Ymd')."/";//存放路径
                                $file_name = time().rand(10,100).$file['name'][$i];//文件名
                                  if (!file_exists($upload_path) && !mkdir($upload_path, 0777, true)) {
                                    lgj("创建文件失败");
                                } else if (!is_writeable($upload_path)) {
                                    lgj("没有读写权限");
                                }else
                                {
                                    //开始移动文件到相应的文件夹
                                    if(move_uploaded_file($file['tmp_name'][$i],$upload_path.$file_name)){
                                        $imgurl = $uploadimage1."/".date('Ymd')."/".$file_name;//图像
                                        lgj($imgurl);
                                        $str_imgs .= $imgurl.",";//存储上传图片路径
                                        lgj($str_imgs);
                                        lgj("----------------------------=========");

                                    }
                                }
                               
                                
                            }
                            $data['imgs'] =  rtrim($str_imgs, ','); 
                        }
                    }
                    

                }
            }else
            {
                $file = $value;
                if($file['tmp_name']!="")
                {
                    $getDocumentImg = $model->getDocumentImg();
                    $uploadimage = $getDocumentImg[$key];//存储图片的文件夹

                    if(is_uploaded_file($file['tmp_name'])){
                        //如果是通过HTTP POST上传的
                        $upload_path = Consts::imgurl.$uploadimage."/".date('Ymd')."/";//存放路径
                        $file_name = time().rand(10,100).$file['name'];//文件名
                          if (!file_exists($upload_path) && !mkdir($upload_path, 0777, true)) {
                            lgj("创建文件失败");
                        } else if (!is_writeable($upload_path)) {
                            lgj("没有读写权限");
                        }else
                        {
                            //开始移动文件到相应的文件夹
                            if(move_uploaded_file($file['tmp_name'],$upload_path.$file_name)){
                                $imgurl = $uploadimage."/".date('Ymd')."/".$file_name;//图像
                                $data[$key] = $imgurl;//存储上传图片路径
                            }
                        }
                       
                        
                    }

                }
            }
           
        }
        
        //处理特殊值b
         foreach ($data as $key => $value) {
            if(is_array($value))
            {
                $data[$key] = $model->getOtherValue($value);//特殊字符处理
            }
        }

        
        $ret = $model->update($data);
        
        //将数据，插入到关联表中，则需要在tplModel中写上insertAssociatedTable方法即可
        if(method_exists($model,'insertAssociatedTable'))
        {
            $model->insertAssociatedTable($ret->id,$_REQUEST["data"]['type']);
        }

        if($table == 'Product')
        {
            require_once("Models/Model_Product.php");
            Model_Product::update_product_choice($ret->id,$_REQUEST['product_choice']);
            Model_Product::update_product_ptype($ret->id,$_REQUEST['product_ptype']);
            Model_Product::update_product_tag($ret->id,$_REQUEST['product_tag']);

        }


        // if($table == 'Tag')
        // {
        //     require_once("Models/Model_Tag.php");
        //     Model_Tag::update_tab_id($ret->id,$_REQUEST['tag_id']);
        // }


        
        header("Location: /Admin/mineshow?tb=".ucwords($table));
    }

    // //展示产品
    // public static function showproduct()
    // {
    //     $table = $_REQUEST["tb"];
    //     $model = self::getMyModel($table);


    //     self::renderPartial('showproduct', array('table'=>$table));
    // }
 public static function news()
 {
    $action  = $_REQUEST['action'] ? $_REQUEST['action'] : 'List';
    $action  = 'news'.$action;
    self::$action();
 }

private static function newsList()
{
   require_once "Models/Model_News.php";
   $search_category = $_REQUEST['search_category'] ? $_REQUEST['search_category'] : 0;
   $where = '';
   if($search_category){
       $where = "where c.id = {$search_category}";
   } 
   $page  = $_REQUEST['page'] ? : 1;
   $show  = 10;
   $start = ($page-1)*$show;
   $tpl = self::getTpl('', 'newslist');
 //  $data =Model_News::selectBySql("select * from `news`n order by `id` desc");
   $data =Model_News::selectBySql("select n.*,c.name as category_name from `news` n LEFT JOIN `news_category` nc on n.id = nc.news_id LEFT JOIN `ncategory` c on c.id = nc.category_id {$where} order by n.id desc limit {$start},{$show}");

   $total = count(Model_News::select());
   $pageNum=ceil($total/$show);
   if ($page > $pageNum)
   echo "<script>window.location.href='Admin/news?action=List'</script>";

   foreach ($data as $key=>$val){
       $data[$key]->status = $status[$val->status];
       $data[$key]->url_type = $url_types[$val->url_type];
       if($val->type){
           $data[$key]->type = $types[$val->type];
       }else{
           $data[$key]->type =' ';

       }

   }
   self::renderPartial($tpl, ["data"=>$data,'pageNum'=>$pageNum,'page'=>$page]);
}

private static function newsAdd(){
    require_once "Models/Model_News.php";
    require_once "Models/Model_Ncategory.php";
    require_once "Models/Model_News_category.php";
    if($_REQUEST['title']){
        $result_upload = self::upload(array('path'=>'images/ad','fileName'=>'news_pic','custom'=>'news'));
        try{
            if($result_upload['code']) {
                $news_model = Model_News::create();
                $news_category = Model_News_category::create();
                $news_model->img = 'images/ad/'.$result_upload['newFile'];
                $news_model->title =  $_REQUEST['title'];
                $news_model->desc =  $_REQUEST['desc'];
                $news_model->body =  $_REQUEST['body'];
                $news_model->save();
                $news_category->news_id = $news_model->id;
                $news_category->category_id =$_REQUEST['category_id'];
                $news_category->save();
                echo json_encode(array('code' => 1, 'msg' => 'ok'));die;
            }else{
                throw new Exception($result_upload['msg']);
            }
        }catch (Exception $e){
            $message = $e->getMessage();
            echo json_encode(array('code'=>0,'msg'=>$message)) ;die;
        } 
    }else{
        $category = Model_Ncategory::select();
        $tpl = self::getTpl('', $table.'newsadd');
        self::renderPartial($tpl, array('category'=>$category));
    }
}
public static function delete(){
    $id = $_REQUEST['id'];
    $table = $_REQUEST['table'];
    try{
        $obj =  idb()->runSql("delete from `{$table}` where `id` = {$id}");
        echo json_encode(array('code'=>1,'msg'=>'delete complete'));die;
    }catch(Exception $e){
        echo json_encode(array('code'=>0,'msg'=>$e->getMessage()));die;  
    }
  
}
public static function preview(){
    require_once "Models/Model_Ncategory.php";
    $id = $_REQUEST['id'];
    $table = $_REQUEST['table'];
    $obj = Model_Ncategory::selectBySql("select n.*,c.`id` as cid ,c.`name` as cname from (select * from `news` where `id` = {$id}) as n left join  `news_category` nc on n.id = nc.news_id LEFT JOIN `ncategory` c on c.`id` = nc.`category_id`");
    $category = Model_Ncategory::select();
    $tpl = self::getTpl('', 'newsedit');
    self::renderPartial($tpl, array("data"=>$obj[0],"category"=>$category));
}
private  static function newsEdit(){
    require_once "Models/Model_News.php";
    require_once "Models/Model_News_category.php";
    $id = $_REQUEST['id'];
    try{
           $obj = Model_News::selectOne("id = ?",array($id));
            if ($_FILES['news_pic']['error'] == 0) {

                $result_upload = self::upload(array('path'=>'images/ad','fileName'=>'news_pic','custom'=>'news'));
                if ($result_upload['code'] ) {
                    $old = $obj->img;
                    $obj->img = 'images/ad/'. $result_upload['newFile'];
                        if ($old) { unlink($old); }
                } else {
                    throw new Exception($result_upload['msg']);
                }
                     
            }  
            $obj->title =  $_REQUEST['title'];
            $obj->desc =   $_REQUEST['desc'];
            $obj->body =   $_REQUEST['body'];
            $obj->save();
            $news_category = Model_News_category::selectOne("news_id= ?",array($id));
            $news_category->category_id = $_REQUEST['category_id'];
            $news_category->save();
            echo json_encode(array('code'=>1,'msg'=>'success!')) ;die;
  } catch (Exception $e) {
            $message = $e->getMessage();
            echo json_encode(array('code'=>0,'msg'=>$message)) ;die;
  }

}

public static function ncategoryList(){
    require_once "Models/Model_Ncategory.php";
   $page  = $_REQUEST['page'] ? : 1;
   $show  = 10;
   $start = ($page-1)*$show;
   $tpl = self::getTpl('', 'ncategorylist');
   $data =Model_Ncategory::selectBySql("select * from `ncategory` order by n.id desc limit {$start},{$show}");

   $total = count(Model_News::select());
   $pageNum=ceil($total/$show);
   if ($page > $pageNum)
   echo "<script>window.location.href='Admin/ncategoryList'</script>";

   foreach ($data as $key=>$val){
       $data[$key]->status = $status[$val->status];
       $data[$key]->url_type = $url_types[$val->url_type];
       if($val->type){
           $data[$key]->type = $types[$val->type];
       }else{
           $data[$key]->type =' ';

       }

   }
   self::renderPartial($tpl, ["data"=>$data,'pageNum'=>$pageNum,'page'=>$page]);

}

private  static function upload($param){
    require_once "Models/Model_File_Upload.php";
    $up = new Model_File_Upload;
    $path = $param['path'];
    $custom = $param['custom'];
    $file = $param['fileName'];
    $up -> set("path", $path);
    $up -> set("maxsize", 20000000);
    $up -> set("custom" ,$custom);
    $up -> set("allowtype", array("gif", "png", "jpg","jpeg"));
    $up -> set("israndname", false);
    if($up->upload($file)) {
          $res = array('code'=>1,'newFile'=>$up->getFileName());
    }else{
         $res = array('code'=>0,'msg'=>$up->getErrorMsg());
    }
    return $res;
}

    public static function tagList()
    {
        require_once "Models/Model_Tag.php";
        require_once "Models/Model_Page.php";
        $page  = $_REQUEST['page'] ? $_REQUEST['page'] :1;
        $show  = 20;
        $start = ($page-1)*$show;
        $total = count(Model_Tag::select());
        $tag = Model_Tag::selectBySql("select * from `tag` limit {$start},{$show}");
        $tag_tab = Model_Tag::selectBySql("select `tag_id`, GROUP_CONCAT(`name`) as tab_name  ,  GROUP_CONCAT(`sort`) as mix_sort  from ( select tgb.`tag_id`, tgb.`sort`,tgb.`tab_id`,tb.name  from `tag_tab` tgb  left join `tab` tb  on tgb.tab_id = tb.id) as r GROUP BY r.tag_id");
        $arr = array();
        foreach ($tag_tab as $kk=>$vv){
          //  $arr[$vv->tag_id] = $vv->tab_name;
            $arr[$vv->tag_id] =array('tab_name'=>$vv->tab_name,'mix_sort'=>$vv->mix_sort);
        }

        foreach ($tag as $key=>$value){
            $tag[$key]->tab_name = $arr[$value->id]['tab_name'];
            $tag[$key]->mix_sort = $arr[$value->id]['mix_sort'];
        }

        if ($total > $show) {//总记录数大于每页显示数，显示分页
            $pageObj = new page($total, $show, $page, 'tagList?page={page}', 10);
            $page_str =  $pageObj->myde_write();
        }
        $tpl   = self::getTpl('', 'taglist');
        self::renderPartial($tpl, array("data"=>$tag,'page_str'=>$page_str));
    }


    public static function  tagDel()
    {
         require_once "Models/Model_Tag.php";
         require_once "Models/Model_Tag_tab.php";
         try{
              $id = $_REQUEST['id'];
              Model_Tag::delete("id = ?",array($id));
              Model_Tag_tab::delete("tag_id = ?",array($id));
              echo json_encode(array('code'=>1,'msg'=>'ok'));die;
         }catch (Exception $e){
              echo  json_encode(array('code'=>0,'msg'=>$e->getMessage()));
         }
    }



    public static function  tagAdd(){
        require_once "Models/Model_Tag.php";
        require_once "Models/Model_Tab.php";
        require_once "Models/Model_Tag_tab.php";
        if($_POST){
            try{
                $tab = $_POST['tab'];
                $name= $_POST['name'];
                $type = $_POST['type'];
                $tag = Model_Tag::create();
                $tag->name = $name;
                $tag->type = $type;
                $tag->save();
                if($tab && $tag->id){
                      foreach ( $tab  as $val){
                            $tag_tab = Model_Tag_tab::create();
                            $tag_tab->tag_id = $tag->id;
                            $tag_tab->tab_id = trim($val);
                            $tag_tab->sort = $_POST[trim($val."-sort")];
                            $tag_tab->save();
                      }
                }
                   echo json_encode(array('code'=>1,'msg'=>'success'));die;
            }catch(Exception $e){
                    echo  json_encode(array('code'=>0,'msg'=>$e->getMessage()));die;
            }

        }else{
              $tpl   = self::getTpl('', 'tagadd');
              $tab = Model_Tab::select();
              self::renderPartial($tpl, array("tab"=>$tab));
        }


    }

    public static function  tagEdit(){
        require_once "Models/Model_Tag.php";
        require_once "Models/Model_Tab.php";
        require_once "Models/Model_Tag_tab.php";
        $action = $_REQUEST['action'];
        $id = $_REQUEST['id'];
        $tag = Model_Tag::selectOne("id = ?",array($id));
        if($action){
            try{
                $tab = $_POST['tab'];
                $name= $_POST['name'];
                $type= $_POST['type'];
                $sort= $_POST['sort'] ? $_POST['sort']  : 0 ;
                $tag->name = $name;
                $tag->type = $type;
                $tag->sort = $sort;
                $tag->save();
                Model_Tag_tab::delete('tag_id = ?',array($id));

                if($tab && $tag->id){
                    foreach ( $tab  as $val){
                        $tag_tab = Model_Tag_tab::create();
                        $tag_tab->tag_id = $tag->id;
                        $tag_tab->tab_id = $val;
                        if( $_POST[$val."-sort"]){
                              $tag_tab->sort = $_POST[$val."-sort"];
                         }
                        $tag_tab->save();
                    }
                }
                echo json_encode(array('code'=>1,'msg'=>'success'));die;
            }catch(Exception $e){
                  echo  json_encode(array('code'=>0,'msg'=>$e->getMessage()));die;
            }

        }else{

               $tpl   = self::getTpl('', 'tagedit');
               $tab = Model_Tab::select();
               $tag_tab  = Model_Tag_tab::select("tag_id = ?",array($id));
               $tab_id = array();
               $sort = array();
               if($tag_tab){
                   foreach ($tag_tab as $value){
                       $tab_id[] = $value->tab_id;
                       $sort[$value->tab_id] = $value->sort;
                   }
               }

               self::renderPartial($tpl, array("tab"=>$tab,'data'=>$tag,'sort'=>$sort,'tab_id'=>$tab_id));
        }

    }
public static function  masterSettingList(){
    require_once "Models/Model_Master_setting.php";
    $tpl = self::getTpl('', 'settinglist');
    $data = Model_Master_Setting::select();
    self::renderPartial($tpl, ["data"=>$data]);
}

public static function  masterSettingEdit(){
    require_once "Models/Model_Master_setting.php";
    $id = $_REQUEST['id'];
    $data = Model_Master_Setting::selectOne("id =? ",array($id));
    if($_POST){
        try{
            $content = $_POST['content'];

            $data->content = $content;
            $data->save();
            echo json_encode(array('code'=>1,'msg'=>'success'));die;
        }catch(Exception $e){
            echo  json_encode(array('code'=>0,'msg'=>$e->getMessage()));die;
        }

    }else{

        $tpl   = self::getTpl('', 'settingedit');


        self::renderPartial($tpl, array('data'=>$data));
    }

}

}//end_class
?>