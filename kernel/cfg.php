<?php

    date_default_timezone_set('Europe/Kiev');
    //define('CHARSET', 'windows-1251');
    define('CHARSET', 'utf-8');
    
    //define('WEB2NET', '1');

// Авторизация
    define('USERS_TABLE','user_item');
    define('SES_ID',session_id());
// /Авторизация 
// Таблици связанные с товарами
    //define('TABLE_ITEMS_CATEGORY','items_category_new');
    define('TABLE_ITEMS_CATEGORY','items_category');
    define('TABLE_ITEMS_FILTERS','items_filter');
    define('TABLE_ITEMS','shop_item');
    define('TABLE_TEMP_ITEMS','items_temp');
// /Таблици связанные с товарами   

    define('DB_HOST', 'localhost');    
    define('DB_NAME', 'kyivpass');
    define('DB_USER', 'root');
    define('DB_PASS', '');
    
    define('USER_IP', getenv ("REMOTE_ADDR"));
    define('PAGE_REF', getenv("HTTP_REFERER"));    
    define('SITE_PATH', getenv("DOCUMENT_ROOT"));
    define('SITE_URL', "http://".getenv("HTTP_HOST"));
    define('ADM_SITE_URL',SITE_URL.'/cms');
    define('PAGE_URL', SITE_URL.getenv("REQUEST_URI"));    
    
    define('PATH_EMAIL_BASE', SITE_PATH.'/cms/sender/e-mail_base'); // Рассылка  
     
    define('PATH_DUMP',       SITE_PATH.'/archives');  // Где лежат дампы БД
    define('EXT_DUMP',  'sql');                           // Разширение для дампов БД
    
    define('PATH_DESIGN',        '/index_files');
    define('PATH_SITE_DESIGN',       '/site/design');
    //define('PATH_DESIGN',       '/site/design/t_1');
    
    define('PATH_LIB',          '/lib');
    define('PATH_MAGIC',        '/magic');
    define('PATH_TPL',          'site/tpl');
    define('PATH_IMG',          '/index_files/img');                             
    define('PATH_INC',          PATH_TPL.'/inc');
    define('PATH_IMG_ITEMS',    '/product-image');
    define('PATH_IMG_ART',      PATH_IMG.'/art');

    define('PATH_TPL_ADMIN',          '/cms/tpl');
    define('PATH_INC_ADMIN',          PATH_TPL_ADMIN.'/inc');
    define('PATH_IMG_ADMIN',          '/cms/design');
    
    define('PATH_INC_MOBI',          PATH_TPL.'/mobi/inc');
    define('PATH_DESIGN_MOBI',       '/site/design/mobi');
    define('PATH_IMG_MOBI',          PATH_DESIGN_MOBI.'/img');
    
   
?>
