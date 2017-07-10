<?php
	
class GD 
{
	
function imageResize($outfile,$infile,$neww,$newh,$quality) {

    $im=imagecreatefromjpeg($infile);
    $k1=$neww/imagesx($im);
    //echo "<br>";
    $k2=$newh/imagesy($im);

  	$k=$k1>$k2?$k1:$k2;
     //$k=$k2;

   $w=intval(imagesx($im)*$k);
   $h=intval(imagesy($im)*$k);
   //$w=$neww;
   //$h=$newh;  

    $im1=imagecreatetruecolor($w,$h);
    imagecopyresampled($im1,$im,0,0,0,0,$w,$h,imagesx($im),imagesy($im));

    imagejpeg($im1,$outfile,$quality);
    imagedestroy($im);
    imagedestroy($im1);

    }
    
    static function saveImageFromRemoteUrl($img_url,$home_dir) // сохранение изо с удаленного адреса
    {
		// $img_url - url изо
		// $home_dir - папка, куда сохраняем изо
		$path_parts = pathinfo($img_url);  // разбиваем img_url на компоненты
        $img_name = str_replace("/", "_", $path_parts['basename']); // выковыриваем имя изо
		$path = $home_dir."/".$img_name; // полный путь в нашу папку с именем изо
		
		if($path !== "")
		{
			if(!file_exists($path) || filesize($path) == "0") // проверяем, а вдруг такое изо уже есть у нас на сервере и размер оного больше чем 0
            {
                $url = trim($img_url);
                file_put_contents($path, 
                file_get_contents($url)); // Сохраняем изо в $home_dir
            }
        }	
	}

}

?>
