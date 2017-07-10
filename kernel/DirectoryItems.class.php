<?php  
  class DirectoryItems
  {
      var $filearray = array();
      var $file_line;
           
      function dirItems($directory)
      {
          $dir = "";
          if(is_dir($directory))
          {
              $dir = opendir($directory) or die("...");
              while(false !== ($file = readdir($dir)))
              {
                  if(is_file("{$directory}/{$file}"))
                  {
                      $filearray[] = $file;
                  }
              }
              closedir($dir);  
          }
          else
          {
              die("Укажите имя каталога.");
          }
          return  $filearray;
      }
      function checkAllTxt($filearray)
      {
          //$bln = true;
          $extension = "";
          $type = "txt";
          foreach($filearray as $value)
          {
              $extension = substr($value,(strpos($value,".")+1));
              $extension = strtolower($extension);
              if($extension == $type)
              {
                  $files[] = $value;
              }
               
          }
          $filearray = $files;
          return $filearray;
      }
      function indexOrder()
      {
         sort($this->filearray); 
      }
      function naturalCaseInsensinsitiveOrder()
      {
          natcasesort($this->filearray);
      }
      function getCount()
      {
          return count($this->filearray);
      }
      function getOnlyImages($directory)
      {
          $this->filearray = "";
          $this->dirItems($directory);
          $extension = "";
          $type = array("jpg","jpeg","gif","png");
          foreach($this->filearray as $key => $value)
          {   
              $extension = strpos($value,".")+1; 
              $extension = substr($value, $extension);
              $extension = strtolower($extension);
              if(!in_array($extension,$type))
              {
                  unset($this->filearray[$key]);
              }
          }
      }     
      function getOnlyType($filearray, $type)
      {   
          $extension = "";
          foreach($filearray as $value)
          {
              $extension = substr($value,(strpos($value,".")+1));
              $extension = strtolower($extension);
              if($extension == $type)
              {
                  $files[] = $value;
              }
          }
          $filearray = $files;
          return $filearray; 
      }
      function countFileLine($filename)
      {
          if(!file_exists($filename))
          {
              return "Файл не существует.";
          }
          $file_line = count(file($filename));
          return $file_line;
      }
      function deleteFile($path)
      {
         if(isset($_GET['delete']))
         {
            $item = "{$path}/{$_GET['delete']}"; 
            if(is_file($item))
            {
               unlink($item);
               echo "<meta http-equiv=refresh content=0; url='{$_SERVER['PHP_SELF']}'>";
            }
         }
       }
  }
?>
