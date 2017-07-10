<?php
	
include_once("item.gallery.admin.php");
include_once("tag.gallery.admin.php");	

define('GALLERY_ITEM_TABLE','gallery_item');    
define('GALLERY_TAG_TABLE','gallery_tag');
define('GALLERY_ITEM_IMG_PATH', SITE_PATH."/image/gallery/item");
define('GALLERY_TAG_IMG_PATH', SITE_PATH."/image/gallery/tag");

class Gallery {	

function admGallery() {

define('GALLERY_NAME', 'Галлерея');

foreach ($_REQUEST as $key=>$val){
$str="$".$key."=\$val;";
eval($str);}

if($type=="tag"){
$c_cont=GalleryTag::admTag();	
}
if($type=="item"){
$c_cont=GalleryItem::admItem();
}


return $c_cont;
}


}
?>
