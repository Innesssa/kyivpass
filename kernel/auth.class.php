<?php
  class Auth 
  {
      function login($username,$password) //Вход.
      {
          $password = md5($password);
          $query = "SELECT * FROM `".USERS_TABLE."` WHERE `login`='{$username}' AND `password`='{$password}' AND `ban`='0'";
          $result = mysql_query($query);// or die(mysql_error());
          mysql::queryError($result,$query);
          $USER = mysql_fetch_array($result,1); 
          if(!empty($USER)) 
          { 
          	  foreach($USER as $key=>$val){
              $USER2["user_".$key]=$val;
              }              
              $_SESSION = array_merge($_SESSION,$USER2);
              $query = "UPDATE `".USERS_TABLE."` SET `sid`='".SES_ID."' WHERE `id`='".$USER['id']."'";
              mysql_query($query);// or die(mysql_error());
              mysql::queryError($result,$query);
              return true;
          }
          else 
          {
              return false;
          }
      }
      
      function logout() // Выход
      {
          unset($_SESSION['user_name']);
          unset($_SESSION['user_level']);
          unset($_SESSION['user_id']); //Не понятно почему, но перестало работать session_unregister('id'); (LAMPP 1.8, PHP Version 5.5.6) session_unregister('id');
          die(header('Location: '.$_SERVER['PHP_SELF']));
//SYS::varDump($_SESSION,__FILE__,__LINE__);
      }
      function check_user($uid) //Проверка залогинности пользователя.
      {   
          $query = "SELECT `sid` FROM `".USERS_TABLE."` WHERE `id`='{$uid}'";
          $result = mysql_query($query);// or die(mysql_error());
          mysql::queryError($result,$query);
          $sid = mysql_result($result,0);
          return $sid==SES_ID ? true : false;
      }
      function userLogged()  //Если пользователь авторизирован...
      {
         if(isset($_SESSION['user_id'])) 
         { 
             define('USER_LOGGED',true);
             define('USER_ID', $_SESSION['user_id']); 
             define('USER_NAME', $_SESSION['user_name']);
             define('USER_PASS', $_SESSION['user_password']);
             define('USER_PRIVILEGE', $_SESSION['user_level']);
             if($_SESSION['user_level'] == 9){
             define('Web2Net',true);
		     }
         }
         else 
         {
             define('USER_LOGGED',false);
         } 
      }    
  
      static function userDo($sql) 
      {	
	      define('MOD_ACTION_TABLE','user_action');
          $dt = date("Y-m-d H:i");
          $sql = Text::cut($sql);

          $query = "INSERT INTO `user_action` (`uid`,`date`,`action`) VALUE ('{$_SESSION['user_id']}','{$dt}','{$sql}')";
          $res = mysql_query($query);
          mysql::queryError($res,$query);
      }
  }    
      
?>
