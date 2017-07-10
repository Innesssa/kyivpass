<?
define('FEEDBACK_ITEM_TABLE','gallery_item');
define('FEEDBACK_TAG_TABLE','gallery_tag');
define('FEEDBACK_ITEM_IMG_PATH', "/image/gallery/item");
define('FEEDBACK_TAG_IMG_PATH', "/image/gallery/tag");
define('FEEDBACK_FORM_PATH',"site/feedback/tpl");
define('FEEDBACK_NAME', 'Обратная связь');	
class Feedback
{
 	 
    function viewFeedbackModPage()
    {
	    foreach ($_REQUEST as $key=>$val)
        {
            // echo $key." - ".$val."<br />";
            $str="$".$key."=\$val;";
            eval($str);
        }                          

        $seodata=explode("/",$seolink);
        $mod=$seodata[0];  //echo $mod." - \$mod<br />";
        $rub=$seodata[1];  //echo $rub." - \$rub<br />";

        $tpl_art = new SiteTpl;    
        $tpl_art->assign('page', $page);
        
        $rub=urldecode($rub);
        if($rub == "Запись")
        {
            $page["content"] = $tpl_art->get('feedback');
            $page["meta"]["title"] = "Запись в клинику";
            $page["meta"]["description"] = "Запись в клинику";
            $page["meta"]["keywords"] = "Запись в клинику"; 
            $arr_value['type'] = "feedback"; 
        }
        if($rub == "online")
        {
            $page["content"] = $tpl_art->get('feedback_sps');
            $page["meta"]["title"] = "Обратная связь";
            $page["meta"]["description"] = "Обратная связь";
            $page["meta"]["keywords"] = "Обратная связь";
            $arr_value['type'] = "online";  
        }
        if($rub == "заказать_рекламу")
        {
            $page["content"] = $tpl_art->get('adver_form');
            $page["meta"]["title"] = "Заказать рекламу";
            $page["meta"]["description"] = "Заказать рекламу";
            $page["meta"]["keywords"] = "Заказать рекламу";
            $arr_value['type'] = "adver";  
        }
        
        
        $page["meta"]["some_meta"] = "";
        
        if(isset($_POST['send_feedback']) || isset($_POST['feedback_online']) || isset($_POST['send_adver']))
        {
            foreach($_REQUEST as $key=>$val)
            {
                $var = explode("__",$key);
                if($var['0'] == "form")
                {
                    $val = Text::wordBreak($val,"20"); 
                    $arr_value[$var['1']] = Text::cutString($val); 
                }
            }
//Sys::varDump($arr_value,__FILE__,__LINE__);
            
            
            $db = new mysql;
            $table = "feedback";
            
            $arr_value['ip'] = $_SERVER['REMOTE_ADDR'];
            $arr_value['date'] = date("Y-m-d H:i");
//echo date("Y-m-d | H:i", $arr_value['date']);
            
            $db->insertSQL ($arr_value, $table);
            
            //$msg = Feedback::completeMessage($_POST); 
//Sys::varDump($msg);
            if($rub == "Запись")
            {
                $ok = Feedback::sendFeedback(); 
            }
            if($rub == "online")
            {
                $ok = Feedback::sendOnline(); 
            }
            if($rub == "заказать_рекламу")
            {
                $ok = Feedback::sendAdver(); 
            }
            //var_dump($msg);
            $page["content"] = $tpl_art->get('feedback_sps');        
        }
        
        return $page;
    }
    
    function completeMessage($var)
    {    
          if(is_array($var))
          {   
              foreach($var as $key=>$val)
              { 
                  $ms = explode("__",$key);
                  if($ms['0'] == "form")
                  {
                      $msg[$ms['1']] = $val;  
                  }
              }
          }
          else
          {
              $msg = $var;
          }
          return $msg;
    }
    
    function sendOnline()             
    {
        //$charset = "windows-1251";
        $charset = "utf-8";
        //$charset = "KOI8-R";
        
        $my_email = EMAIL_FEEDBACK;
        $subject = "Письмо с сайта";
        
$headers  = 'MIME-Version: 1.0' . "\r\n";
$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
        
        $txt = new Text;
        $email = $txt->cut($_POST['form__email']);
        $content = 
"
Имя - {$_POST['form__name']}<br />\r\n
E-mail - {$email}<br />\r\n
Телефон - {$_POST['form__phone']}
<br /><br /><hr />
Вопрос:<br />\r\n".stripcslashes($txt->cut($_POST['form__message']));
                    
        //$content = iconv('UTF-8', 'Windows-1251', $content);

// На случай если какая-то строка письма длиннее 70 символов мы используем wordwrap()
$message = wordwrap($content, 70);
// Отправляем
        $send = mail($my_email, $subject, $message, $headers); 
        if($send)
        {
           //Sys::varDump($send); 
        }
        else
        {
            return false;
        }
        
        
    }
    
    function sendFeedback()             
    {
        //$charset = "windows-1251";
        $charset = "utf-8";
        //$charset = "KOI8-R";
        
        $my_email = EMAIL_BOSS;
        $subject = "Запись на прием";
        
$headers  = 'MIME-Version: 1.0' . "\r\n";
$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
        
        $txt = new Text;
        $email = $txt->cut($_POST['form__email']);
        $content = "
IP - ".$_SERVER['REMOTE_ADDR']."<br />\r\n
Имя - {$_POST['form__name']}.<br />\n
Дата рождения - {$_POST['form__birth']}.<br />\n 
Адрес - {$_POST['form__address']}.<br />\n
e-mail - {$email}.<br />\n 
причина обращения - {$_POST['form__why']}.<br />\n 
телефон - {$_POST['form__tel']}.<br />\n 
желаемая дата - {$_POST['form__your_date']}.<br />\n 
желаемое время - {$_POST['form__your_time']}.<br />\n 
способ подтверждения заявки - {$_POST['form__method']}.<br />\n
Обращение<hr />"
.stripcslashes($txt->cut($_POST['form__message']));
                    
        //$content = iconv('UTF-8', 'Windows-1251', $content);

// На случай если какая-то строка письма длиннее 70 символов мы используем wordwrap()
$message = wordwrap($content, 70);
// Отправляем
        $send = mail($my_email, $subject, $message, $headers); 
        if($send)
        {
           //Sys::varDump($send); 
        }
        else
        {
            return false;
        }
        
        
    }
    
    function sendAdver()             
    {
        //$charset = "windows-1251";
        $charset = "utf-8";
        //$charset = "KOI8-R";
        
        $my_email = EMAIL_BOSS;
        $subject = "Заявка на рекламу";
        
$headers  = 'MIME-Version: 1.0' . "\r\n";
$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
        
        $txt = new Text;
        $email = $txt->cut($_POST['form__email']);
        $content = "
IP - ".$_SERVER['REMOTE_ADDR']."<br />\r\n
Имя - {$_POST['form__name']}.<br />\n
Дата рождения - {$_POST['form__birth']}.<br />\n 
Адрес - {$_POST['form__address']}.<br />\n
e-mail - {$email}.<br />\n 
вид рекламы (строчная/блочная) - {$_POST['form__why']}.<br />\n 
телефон - {$_POST['form__tel']}.<br />\n 
период действия (от/до) - {$_POST['form__your_date']}.<br />\n 
период оплаты - {$_POST['form__your_time']}.<br />\n 
способ оплаты - {$_POST['form__method']}.<br />\n
текст рекламы/код банера<hr />"
.stripcslashes($txt->cut($_POST['form__message']));
                    
        //$content = iconv('UTF-8', 'Windows-1251', $content);

// На случай если какая-то строка письма длиннее 70 символов мы используем wordwrap()
$message = wordwrap($content, 70);
// Отправляем
        $send = mail($my_email, $subject, $message, $headers); 
        if($send)
        {
           //Sys::varDump($send); 
        }
        else
        {
            return false;
        }
        
        
    }
    
    function sendMail($msg)
    {
        $my_email = EMAIL_BOSS;
        $subject = SITE_URL;
        $txt = new Text;
        $email = $txt->cut($_POST['form__email']);
        $content = "
                   Имя - {$msg['name']}.<br />\n
                   Дата рождения - {$msg['birth']}.<br />\n 
                   Адрес - {$msg['address']}.<br />\n
                   e-mail - {$msg['email']}.<br />\n 
                   причина обращения - {$msg['why']}.<br />\n 
                   телефон - {$msg['tel']}.<br />\n 
                   желаемая дата - {$msg['your_date']}.<br />\n 
                   желаемое время - {$msg['your_time']}.<br />\n 
                   способ подтверждения заявки - {$msg['method']}.<br />\n
                   Обращение<hr />
                   {$msg['message']}.<br />\n  ";
        
        $EOL = "\r\n";
        $headers = "Content-Type: text/html; charset='".CHARSET."'{$EOL}";
        $headers .= "From: {$my_email}{$EOL}";
        $headers .= "Reply-To: {$my_email}{$EOL}";
        $headers .= "Return-Path: {$my_email}{$EOL}";
        $headers .= "Errors-To: {$my_email}{$EOL}";
        $headers .= "Disposition-Notification-To: {$my_email}{$EOL}{$EOL}";
        $header.="Subject: {$subject}";
        $header.="Content-type: text/html; charset='".CHARSET."'{$EOL}";
        $msg = "<HTML>{$EOL}";
        $msg .= "<HEAD>{$EOL}";
        $msg .= "</HEAD>{$EOL}";
        $msg .= "<BODY>{$EOL}";
        $msg .= $content;
        $msg .= "</BODY>{$EOL}";
        $msg .= "</HTML>";

        $send = mail(EMAIL_ADMIN, $subject, $msg, $headers);
        if($send)
        {
           //Sys::varDump($send); 
        }
        else
        {
            return false;
        }
        
        
    }
    
    function checkEmptyForm($val)
    {
         if($val == "")
         {
             $val = 0;
         }
         else
         {
             return trim($val);
         }
    }

}
?>
