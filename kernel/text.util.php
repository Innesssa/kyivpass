<?php

	
class Text 
{
	
    static function cutStr($str,$cword){
    $nstr='';
    $str_exp = explode(" ", $str);
    for ($a = 0; $a <= $cword; $a++) {
    $nstr.=$str_exp[$a].' ';
    }
    $nstr=trim($nstr);
    if(isset($str_exp[$a+1]))$nstr.='&nbsp;...';

    return $nstr;
    }
  
    static function cutStrBySymbol($text, $start, $limit)
    {
        $text=substr($text,$start,$limit);
        /*если не пустая обрезаем до  последнего  пробела*/
        if(substr($text,strlen($text)-1,1) && strlen($text)==$limit)
        {
            $textret=substr($text,0,strlen($text)-strlen(strrchr($text,' ')));
            if(!empty($textret))
            {
                return $textret;
            }
        }
        return $text;
    }
  
    static function cut($string)
    {
        $string = trim($string);
        $string = strip_tags($string);
        $string = htmlspecialchars($string);
        $string = mysql_escape_string($string);
        $string = ereg_replace("\""," ",$string);
        $string = ereg_replace("'"," ",$string);
        $string = ereg_replace("`"," ",$string);
        return $string;
    }

    static function cutString($string)
    {
        $string = trim($string);
        $string = ereg_replace("\""," ",$string);
        $string = ereg_replace("'"," ",$string);
        $string = ereg_replace("`"," ",$string);
        $string = htmlspecialchars($string);
        return $string;
    }

    static function cutStr4Price($string)
    {
        $string = trim($string);
        $string = ereg_replace("\"","",$string);
        $string = ereg_replace("'","",$string);
        $string = ereg_replace("`","",$string);
        $string = htmlspecialchars($string);
        return $string;
    }
    
    static function wordBreak($str, $max) 
    { 
        $array_words=explode(' ', $str); 
        $array_words_count=count($array_words); 
        $str=null; 
        for($i=0; $i<$array_words_count; $i++) 
        { 
            $word=$array_words[$i]; 
            if(strlen($word)>$max) $word=chunk_split($word, $max," "); 
            $str.=$word." "; 
        } 
        $str=trim($str); 
        return $str; 
    }
    
    static function checkStrEmpty($str)
    {   
        if($str !== "")
        {
            return $str;
        }
        else
        {
            return false;
        }
    } 
 
    static function cirilToLatin($str) 
    { 

    $arr = array( 
    'А' => 'A', 
    'Б' => 'B', 
    'В' => 'V', 
    'Г' => 'G', 
    'Д' => 'D', 
    'Е' => 'E', 
    'Ё' => 'E', 
    'Ж' => 'ZH', 
    'З' => 'Z', 
    'И' => 'I', 
    'Й' => 'Y', 
    'К' => 'K', 
    'Л' => 'L', 
    'М' => 'M', 
    'Н' => 'N', 
    'О' => 'O', 
    'П' => 'P', 
    'Р' => 'R', 
    'С' => 'S', 
    'Т' => 'T', 
    'У' => 'U', 
    'Ф' => 'F', 
    'Х' => 'KH', 
    'Ц' => 'TS', 
    'Ч' => 'CH', 
    'Ш' => 'SH', 
    'Щ' => 'SCH', 
    'Ъ' => '', 
    'Ы' => 'Y', 
    'Ь' => '', 
    'Э' => 'EH', 
    'Ю' => 'U', 
    'Я' => 'YA',
    'І' => 'I',    
    'Ї' => 'YI',
    'Є' => 'YE',
    'Ґ' => 'G',    
    'а' => 'a', 
    'б' => 'b', 
    'в' => 'v', 
    'г' => 'g', 
    'д' => 'd', 
    'е' => 'e', 
    'ё' => 'e', 
    'ж' => 'zh', 
    'з' => 'z', 
    'и' => 'i', 
    'й' => 'y', 
    'к' => 'k', 
    'л' => 'l', 
    'м' => 'm', 
    'н' => 'n', 
    'о' => 'o', 
    'п' => 'p', 
    'р' => 'r', 
    'с' => 's', 
    'т' => 't', 
    'у' => 'u', 
    'ф' => 'f', 
    'х' => 'kh', 
    'ц' => 'ts', 
    'ч' => 'ch', 
    'ш' => 'sh', 
    'щ' => 'sch', 
    'ъ' => '', 
    'ы' => 'y', 
    'ь' => '', 
    'э' => 'eh', 
    'ю' => 'u', 
    'я' => 'ya',
    'і' => 'i',    
    'ї' => 'yi',
    'є' => 'ye',
    'ґ' => 'g',                    
    "'" => '',
    ' ' => '-',
    '+' => 'plus',
    '&#352;' => 's',
    '&euml;' => 'e',            
    '.' => '',
    ',' => '',
    '"' => '',
    '?' => '',
    '!' => '',
    '№' => '-',
    '(' => '',
    ')' => '',
    '«' => '',
    '»' => '',
    '%' => '',
    ' "' => '',
    '\r' => '',
    ':' => '',
    ';' => '',
    '/' => '-',
    '&' => 'and',
    '—' => '',
    '–' => '',    
    '--' => '-',
    "'" => "",
    "“" => "",    
    "”" => "",
    "’" => "",                            
    "'" => ''
    ); 
    $key = array_keys($arr);
    $val = array_values($arr);
    $transl = str_replace($key,$val,trim($str)); 

    return strtolower($transl); 
    }

    static  function cutPriceBrandToDBBrand($string)
    {
        $string = trim($string);
        $string = strtolower($string); 
        //$string = htmlspecialchars($string);
        $string = str_replace("`","",$string);
        $string = str_replace(",","",$string);
        $string = str_replace("-","",$string);
        $string = str_replace(" ","",$string);
        $string = str_replace("'","",$string);
        $string = str_replace("%","",$string);
        $string = str_replace(":","",$string);
        $string = str_replace("/","",$string);
        $string = str_replace("@","",$string);
        $string = str_replace("(","",$string);
        $string = str_replace(")","",$string); 
        $string = str_replace("\"","",$string);
        $string = str_replace("\\","",$string);
        return $string;
    }

    static  function cutForCirilicSeolink($string)
    {
        $string = trim($string);
        //$string = htmlspecialchars($string);
        $string = str_replace("`","",$string);
        $string = str_replace(",","",$string);
        $string = str_replace("-","_",$string);
        $string = str_replace(" ","_",$string);
        $string = str_replace("'","",$string);
        $string = str_replace("%","_",$string);
        $string = str_replace(":","_",$string); 
        $string = str_replace("\"","",$string);
        $string = str_replace("\\","",$string);
        return $string;
    }

    static function uncutForCirilicSeolink($string)
    {
        $string = trim($string);
        $string = ereg_replace(" ","_",$string);
        
        return $string;
    }

    static function cirilToCirilSeolink($str) 
    { 
        $arr = array(
        '-' => '_',
        ' ' => '_',
        '.' => '',
        ',' => '',
        '"' => '',
        '?' => '',
        '!' => '',
        '№' => '',
        '(' => '',
        ')' => '',
        '«' => '_',
        '»' => '_',
        '%' => '_',
        ' "' => '_',
        '\r' => '_',
        ':' => '_',
        ';' => '_',
        '/' => '_',
        '&' => '_',
        '—' => '_',
        '–' => '_',    
        '--' => '_',
        "'" => "",
        "“" => "",    
        "”" => "",
        "’" => "",                            
        "'" => '',
        "і" => 'i',
        ); 
        $key = array_keys($arr);
        $val = array_values($arr);
        $transl = str_replace($key,$val,trim($str)); 
    
        return strtolower($transl); 
        return $str;
    } 

    static function alp_data_ru()
    {
        $alphavit_ru = array( 
        'А' => 'r1', 
        'Б' => 'r2', 
        'В' => 'r3', 
        'Г' => 'r4', 
        'Д' => 'r5', 
        'Е' => 'r6', 
        'Ё' => 'r7', 
        'Ж' => 'r8', 
        'З' => 'r9', 
        'И' => 'r10', 
        'Й' => 'r11', 
        'К' => 'r12', 
        'Л' => 'r13', 
        'М' => 'r14', 
        'Н' => 'r15', 
        'О' => 'r16', 
        'П' => 'r17', 
        'Р' => 'r18', 
        'С' => 'r19', 
        'Т' => 'r20', 
        'У' => 'r21', 
        'Ф' => 'r22', 
        'Х' => 'r23', 
        'Ц' => 'r24', 
        'Ч' => 'r25', 
        'Ш' => 'r26', 
        'Щ' => 'r27', 
        'Э' => 'r28', 
        'Ю' => 'r29', 
        'Я' => 'r30',
         );
         return $alphavit_ru;
    }
 
    static function alp_data_en()
    {
        $alphavit_en = array(
        'A' => 'e1',
        'B' => 'e2',
        'C' => 'e3',
        'D' => 'e4',
        'E' => 'e5',
        'F' => 'e6',
        'G' => 'e7',
        'H' => 'e8',
        'I' => 'e9',
        'J' => 'e10',
        'K' => 'e11',
        'L' => 'e12',
        'M' => 'e13',
        'N' => 'e14',
        'O' => 'e15',
        'P' => 'e16',
        'Q' => 'e17',
        'R' => 'e18',
        'S' => 'e19',
        'T' => 'e20',
        'U' => 'e21',
        'V' => 'e22',
        'W' => 'e23',
        'X' => 'e24',
        'Y' => 'e25',
        'Z' => 'e26'
        );
         return $alphavit_en;
    }

    
    
//------------------ Перевод чисел: Арабские в Римские - dec2roman ($number) и обратно - roman2dec ($linje) ------------------//
function dec2roman ($number) { 

    # Making input compatible with script. 
    $number = floor($number); 
    if($number < 0) { 
        $linje = "-"; 
        $number = abs($number); 
    } 
     
    # Defining arrays 
    $romanNumbers = array(1000, 500, 100, 50, 10, 5, 1); 
    $romanLettersToNumbers = array("M" => 1000, "D" => 500, "C" => 100, "L" => 50, "X" => 10, "V" => 5, "I" => 1); 
//    $romanLettersToNumbers = array("mu" => 1000, "da" => 500, "cu" => 100, "la" => 50, "xo" => 10, "va" => 5, "pi" => 1); 

    $romanLetters = array_keys($romanLettersToNumbers); 
     
    # Looping through and adding letters. 
    while ($number) { 
        for($pos = 0; $pos <= 6; $pos++) { 
             
            # Dividing the remaining number with one of the roman numbers. 
            $dividend = $number / $romanNumbers[$pos]; 

            # If that division is >= 1, round down, and add that number of letters to the string. 
            if($dividend >= 1) { 
                $linje .= str_repeat($romanLetters[$pos], floor($dividend)); 

                # Reduce the number to reflect what is left to make roman of. 
                $number -= floor($dividend) * $romanNumbers[$pos]; 
            } 
        } 
    } 


    # If I find 4 instances of the same letter, this should be done in a different way. 
    # Then, subtract instead of adding (smaller number in front of larger). 
    $numberOfChanges = 1; 
    while($numberOfChanges) { 
        $numberOfChanges = 0; 

        for($start = 0; $start < strlen($linje); $start++) { 
            $chunk = substr($linje, $start, 1); 
            if($chunk == $oldChunk && $chunk != "M") { 
                $appearance++; 
            } else { 
                $oldChunk = $chunk; 
                $appearance = 1; 
            } 

            # Was there found 4 instances. 
            if($appearance == 4) { 
                $firstLetter = substr($linje, $start - 4, 1); 
                $letter = $chunk; 
                $sum = $firstNumber + $letterNumber * 4; 

                $pos = Text::array_search($letter, $romanLetters); 

                # Are the four digits to be calculated together with the one before? (Example yes: VIIII = IX Example no: MIIII = MIV 
                # This is found by checking if the digit before the first of the four instances is the one which is before the digits in the order 
                # of the roman number. I.e. MDCLXVI. 

                if($romanLetters[$pos - 1] == $firstLetter) { 
                    $oldString = $firstLetter . str_repeat($letter, 4); 
                    $newString = $letter . $romanLetters[$pos - 2]; 
                } else { 
                    $oldString = str_repeat($letter, 4); 
                    $newString = $letter . $romanLetters[$pos - 1]; 
                } 
                $numberOfChanges++; 
                $linje = str_replace($oldString, $newString, $linje); 
             
            } 

        } 

    } 
    return $linje; 
} 

function roman2dec ($linje) { 
    # Fixing variable so it follows my convention 
    $linje = strtoupper($linje); 
     
    # Removing all not-roman letters 
    $linje = ereg_replace("[^IVXLCDM]", "", $linje); 

    print("\$linje    = $linje<br>"); 
     
    # Defining variables 
    $romanLettersToNumbers = array("M" => 1000, "D" => 500, "C" => 100, "L" => 50, "X" => 10, "V" => 5, "I" => 1); 

    $oldChunk = 1001; 

    # Looping through line 
    for($start = 0; $start < strlen($linje); $start++) { 
        $chunk = substr($linje, $start, 1); 
         
        $chunk = $romanLettersToNumbers[$chunk]; 
         
        if($chunk <= $oldChunk) { 
            $calculation .= " + $chunk"; 
        } else { 
            $calculation .= " + " . ($chunk - (2 * $oldChunk)); 
        } 
         
     
        $oldChunk = $chunk; 
    } 
     
    # Summing it up 
    eval("\$calculation = $calculation;"); 
    return $calculation; 

} 

# Implementation of the array_search function. Works only with numerical arrays. 
function array_search($searchString, $array) { 
    foreach ($array as $content) { 
        if($content == $searchString) { 
            return $pos; 
        } 
        $pos++; 
    } 
} 
    
//------------------------------------------------- Перевод чисел: Арабские в Римские и обратно -----------------------------//
    
    
    static function decodingQuery($str) 
    { 
        $str = strtolower($str);
        $arr = array(
        'update' => 'Обновил',
        'insert' => 'Добавил',
        'into' => 'в таблицу',
        'set' => 'значениями:',
        'where' => 'где',
        'values' => 'значения',
        'id' => 'порядковый номер',
        'connect' => 'связи',
        'caption' => 'название',
        'seolink' => 'ссылка',
        'description' => 'описание',
        'desc_short' => 'краткое описание',
        'desc_full' => 'полное описание',
        'show' => 'показ на сайте',
        'hit' => 'хит показов',
        'meta_t' => 'мета(тайтл)',
        'meta_d' => 'мета(описание)',
        'meta_k' => 'мета(ключевые слова)',
        'date' => 'дата',
        'pos' => 'UNIX-время',
        //',' => '<br />',
        'and' => 'и',
            
        ); 
        $key = array_keys($arr);
        $val = array_values($arr);
        $transl = str_replace($key,$val,trim($str)); 
        $transl = iconv("CP1251", "CP1251", $str);
    
        return strtolower($transl); 
        return $str;
    }
    
}

?>
