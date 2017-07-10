<?
if($_GET['ext']=='crop'){require('crop.php');}else{
	
require(getenv("DOCUMENT_ROOT").'/kernel/kernel.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
$upload_path="/image/gallery/item/tmp/";
$uploaddir=SITE_PATH.$upload_path;
@mkdir($uploaddir, 0755);

if($_FILES!=NULL){
//var_dump($_FILES);
}
$uploadfile=$_POST['prodid']."-".$_POST['numer'].".png";
if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploaddir.$uploadfile)){
	
GD::imageResize($uploaddir.$uploadfile,$uploaddir.$uploadfile,740,480,100);

//header("Location: /cms/gallery/magic/crop.php?img=".$upload_path.$uploadfile."&size=740x480");

header("Location: /cms/gallery/magic/load_item_image.php?ext=crop&img=".$upload_path.$uploadfile."&size=740x480");    
    //print "File is valid, and was successfully uploaded.";
} else {
    print "&nbsp;&nbsp;&nbsp;<b>Ошибочка вышла!</b>";
}
}
?>
<html>
<body style="padding:0;margin:0;">
<div id="body" style="position: fixed;top:0px;left:0px;height: 100%;width:100%;overflow:auto;" align="center" onClick="javascript:parent.offWhiteSlimbox();">
<div style="background-color:#eee;width:800px;" onClick="event.cancelBubble = true" align="left" class="r10b">

<style>
.fileform { 
    background-color: #FFFFFF;
    border: 1px solid #CCCCCC;
    border-radius: 2px;
    cursor: pointer;
    height: 26px;
    overflow: hidden;
    padding: 2px;
    position: relative;
    text-align: left;
    vertical-align: middle;
    width: 230px;

}
 
.fileform .selectbutton { 
    background-color: #A2A3A3;
    border: 1px solid #939494;
    border-radius: 2px;
    color: #FFFFFF;
    float: right;
    font-size: 13px;
    height: 20px;
    line-height: 20px;
    overflow: hidden;
    padding: 2px 6px;
    text-align: center;
    vertical-align: middle;
    width: 50px;
}
 
.fileform #upload{
    position:absolute; 
    top:0; 
    left:0; 
    width:100%; 
    -moz-opacity: 0; 
    filter: alpha(opacity=0); 
    opacity: 0; 
    height: 30px; 
    z-index:20;
    cursor:pointer;
}
body {
font-size: 13px;
font-family: Verdana, Tahoma, Helvetica, Calibri, Geneva, BernhardFashion BT, Arial, "Trebuchet MS", sans-serif;
color: #000;
}
.r5{
-webkit-border-radius: 5px;
-moz-border-radius: 5px;
border-radius: 5px;
}
.r10{
-webkit-border-radius: 10px;
-moz-border-radius: 10px;
border-radius: 10px;
}
.r5b{
-webkit-border-bottom-right-radius: 5px;
-webkit-border-bottom-left-radius: 5px;
-moz-border-radius-bottomright: 5px;
-moz-border-radius-bottomleft: 5px;
border-bottom-right-radius: 5px;
border-bottom-left-radius: 5px;
}
.r10b{
-webkit-border-bottom-right-radius: 10px;
-webkit-border-bottom-left-radius: 1px;
-moz-border-radius-bottomright: 10px;
-moz-border-radius-bottomleft: 10px;
border-bottom-right-radius: 10px;
border-bottom-left-radius: 10px;
}
</style>
<?if($_GET['loadimg']!=''){
$bigimg=$_GET['loadimg'];
$medimg=str_replace(".png","m.png",$bigimg);
$smallimg=str_replace(".png","s.png",$bigimg);	
GD::imageResize(getenv("DOCUMENT_ROOT").$medimg,getenv("DOCUMENT_ROOT").$bigimg,422,274,90);
GD::imageResize(getenv("DOCUMENT_ROOT").$smallimg,getenv("DOCUMENT_ROOT").$bigimg,98,64,90);	
var_dump(getenv("DOCUMENT_ROOT").$bigimg);
var_dump($medimg);
var_dump($smallimg);
	?>
<script>
var zimg='prodimg<?=$_GET['num']?>';
var h_url=parent.document.getElementById(zimg);	
//alert(h_url.src);	
h_url.src = '<?=$_GET["loadimg"]?>?<?=time()?>';
parent.offWhiteSlimbox();	
</script>
<?}?>

<table style="width:800px;" class="r10b">
<tr><td align="center" valign="top" style="height:100px;padding:20px;font-size:12px;">
<div style="margin-bottom:7px;"><b>Изображение №<?=$_GET["numer"]?></b></div>
<form name="uplimg" enctype="multipart/form-data" action="" method="post" style="padding:0;margin:0;">
<input type="hidden" name="MAX_FILE_SIZE" value="">
<input type="hidden" name="prodid" value="<?=$_GET['prodid']?>">	
<input type="hidden" name="numer" value="<?=$_GET['numer']?>">		
<div style="margin-bottom:7px;">Заменить изображение:</div> 
<div class="fileform" style="cursor:pointer;">
<div class="selectbutton" style="cursor:pointer;">Обзор</div>
<input id="upload" name="userfile" type="file" onchange="uplimg.submit()" style="cursor:pointer;">
</form>	
</div>

</td></tr><tr><td align="center" valign="top" style="padding:0px 25px 27px 25px;">
<?if(!file_exists(getenv("DOCUMENT_ROOT").$_GET['img']))$_GET['img']="/index_files/noimage_740x480.png";?>
<img src="<?=$_GET['img']?>?<?=time()?>" width="740" border="1" />
</td></tr></table>

<div></div>
</body>
</html>
<?}?>