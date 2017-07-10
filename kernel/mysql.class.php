<?php
$GLOBALS["SQL_DEBUG"]="";
$db = mysql_connect (DB_HOST, DB_USER, DB_PASS) or die("Подключение к базе данных не состоялось!"); 
mysql_select_db(DB_NAME,$db);
//mysql_query("SET NAMES latin1");
mysql_query("SET NAMES utf8");

class mysql
{

	var $table;
    var $prefix="";

	function put_error ($msg)
	{
		print "<p><b>$msg</b>";
		exit();
	}

	function sql_error ($msg)
	{
		print "<p><b>$msg</b>";
		print "<p><b>Ошибка базы данных:</b>".mysql_error();
		exit();
	}

	function selectSQL ($select, $from, $where, $order="", $limit="")
	{
        $from = $this->prefix.$from;
		if (!is_array ($where)) $this->put_error ("В метод selectSQL(".$from.") передан НЕ массив");
		if ($select == "") $select = "*";
		$q = "SELECT $select FROM $from WHERE ";
		foreach ($where as $field=>$val)
		{
			$q .= "$field = '$val' AND ";
		}
		$q .= " 1=1 ";
		if ($order != "") $q .= "ORDER BY $order ";
		if ($limit != "") $q .= "LIMIT $limit";
        //print $q."<br />";
        SYS::varSQL($q,__FILE__,__LINE__,'selectSQL');
        $GLOBALS["SQL_DEBUG"].=$q."<br />";
        $raw = mysql_query ($q);
		if (!raw) $this->sql_error ("Error ".$q." in selectSQL(".$from.")!");
		else
		if (mysql_num_rows($raw) > 0)
		{
			while ($result = mysql_fetch_assoc ($raw))
			{
				$itog[] = $result;
			}
			return $itog;
		}
		else return 0;
	}

    function origSelectSQL ($select, $from, $where, $order="", $limit="", $group="")
	{
        $from = $this->prefix.$from;
		if (!isset ($where)) $this->put_error ("В метод origSelectSQL(".$from.") не передан WHERE");
		if ($select == "") $select = "*";
		$q = "SELECT $select FROM $from WHERE ";
		$q .= $where;
		$q .= " AND 1=1 ";
		if ($group != "") $q .= "GROUP BY $group ";	
		if ($order != "") $q .= "ORDER BY $order ";
		if ($limit != "") $q .= "LIMIT $limit";
		$q .= ";";
		//print $q."<br />";
        $raw = mysql_query ($q);
        SYS::varSQL($q,__FILE__,__LINE__,'origSelect');
        $GLOBALS["SQL_DEBUG"].=$q."<br />";        
		if (!$raw) $this->sql_error ("Error ".$q." in origSelectSQL(".$from.")!");
		else
		if (mysql_num_rows($raw) > 0)
		{
			while ($result = mysql_fetch_assoc ($raw))
			{
				$itog[] = $result;
			}
			return $itog;
		}
		else return 0;
	}

    function insertSQL ($arr_value, $table)
	{ 
		$table = $this->prefix.$table;
		if (!is_array ($arr_value)) $this->put_error ("Â ìåòîä insertSQL(".$table.") ïåðåäàí ÍÅ ìàññèâ");
		
		$field = ""; $var_field = "";
		foreach ($arr_value as $key=>$val)
		{
			$field .= "`$key`, ";
			$var_field .= "'$val', ";
		}

		$field = substr_replace($field, "", -2, 1);
		$var_field = substr_replace($var_field, "", -2, 1);
		$q = "INSERT INTO $table ($field) VALUES ($var_field)";
		$GLOBALS["SQL_DEBUG"].=$q."<br />";
Auth::userDo($q);  // Запись того, что юзер делает
        //print $q;
		if (!mysql_query ($q)) $this->sql_error ("Error ".$q." in insertSQL(".$table.")!");
		else
		{
			$id = mysql_insert_id();
			return $id;
		}

	}

    function updateSQL ($from, $arr_val, $where)
	{
        $from = $this->prefix.$from;
		$q = "UPDATE $from SET ";
		if (!is_array($arr_val)) $this->put_error("Â ïàðàìåòðàõ çíà÷åíèé íà îáíîâëåíèå updateSQL(".$from.") ïåðåäàí ÍÅ ìàññèâ!");

		$q_var = "";
		foreach ($arr_val as $field=>$var)
		{
			$q_var .= "`$field` = '$var', ";
		}
		$q_var = substr_replace($q_var, "", -2, 1);

		if (!is_array($where)) $this->put_error("Â ïàðàìåòðàõ óñëîâèé íà îáíîâëåíèå updateSQL(".$from.") ïåðåäàí ÍÅ ìàññèâ!");
		$q_where = "";
		foreach ($where as $field=>$val)
		{
			$q_where .= "`$field` = '$val' AND ";
		}
		$q_where .= " 1=1";

		$q .= "$q_var WHERE $q_where";
		$GLOBALS["SQL_DEBUG"].=$q."<br />";
Auth::userDo($q);  // Запись того, что юзер делает
//print $q." - MySQL::updateSQL";
//SYS::varDump($q,__FILE__,__LINE__,"UPDATE");
		if (!mysql_query ($q))
		{
			$this->sql_error ("Error ".$q." in updateSQL(".$from.")!");
			return;
		}
		else return 1;

	}

    function deleteSQL ($from, $where)
	{
		$from = $this->prefix.$from;
		$q = "DELETE FROM $from WHERE ";

		if (!is_array($where)) $this->put_error("Â ïàðàìåòðàõ çíà÷åíèé íà óäàëåíèå deleteSQL(".$from.") ïåðåäàí ÍÅ ìàññèâ!");
		foreach ($where as $field=>$val)
		{
			$q .= "$field = '$val' AND ";
		}
		$q .= " 1=1";
		$GLOBALS["SQL_DEBUG"].=$q."<br />";
Auth::userDo($q);  // Запись того, что юзер делает		
        //print $q;
		if (!mysql_query ($q)) $this->sql_error ("Error ".$q." in deleteSQL(".$from.")!");
		else
		return true;

	}
	
	function origSQL($sql)
	{
		$q = $sql;
		$GLOBALS["SQL_DEBUG"].=$q."<br />";
		$raw = mysql_query ($q);		
		if (!raw) $this->sql_error ("Error ".$q." in origSQL(".$from.")!");
		else
		if (is_array($raw)){
		if (mysql_num_rows($raw) > 0)
		{
			while ($result = mysql_fetch_assoc ($raw))
			{
				$itog[] = $result;
			}
			return $itog;
		}}
		else return 0;
	}
    
    static function checkUniqRow($table, $row, $value, $row1, $value1)
    {
        $query = "SELECT count(*) FROM {$table} WHERE `{$row}`='{$value}' AND `{$row1}`='{$value1}'";
        $GLOBALS["SQL_DEBUG"].=$query."<br />";
//echo $query;
        $res = mysql_query($query);
        if(mysql_result($res,0) > 0)
        {   
            return false;
        }
        else
        {   
            return true;
        }
    }	
    
    static function backupTables($file/*, $host, $user, $pass, $name*/)
    {
//        $link = mysql_connect($host, $user, $pass);
//        mysql_select_db($name, $link);
        file_put_contents($file, '');
        
        //ïîëó÷åíèå ñïèñêà òàáëèö
        $tables = array();
        $result = mysql_query('SHOW TABLES;'/*, $link*/);
        while($row = mysql_fetch_row($result)){
            $tables[] = $row[0];
        }
        
        //îáðàáîòêà òàáëèö
        if(count($tables)>0){
            foreach($tables as $table){
                mysql::backupTableStructure($file, $table/*, $link*/);
                mysql::backupTableData($file, $table/*, $link*/);
            }
        }
    }

    static function backupTableStructure($file, $table/*, $link*/)
    {
        //ïîëó÷åíèå è ñîõðàíåíèå ñòðóêòóðû òàáëèöû
        $content = 'DROP TABLE IF EXISTS `'.$table."`;\n\n";
        $GLOBALS["SQL_DEBUG"].=$content."<br />";
        $result = mysql_fetch_row(mysql_query('SHOW CREATE TABLE `'.$table.'`;'/*, $link*/));
        $content .= $result[1].";\n\n";
        file_put_contents($file, $content, FILE_APPEND);
    }

    static function backupTableData($file, $table/*, $link*/)
    {
        //ïîëó÷åíèå è ñîõðàíåíèå äàííûõ òàáëèöû
        $result = mysql_fetch_row(mysql_query('SELECT COUNT(*) FROM `'.$table.'`;'/*, $link*/));
        $count = $result[0];
        $delta = 500;
        
        //åñëè äàííûå ñóùåñòâóþò
        if($count>0){
            //îïðåäåëÿåì íå ÷èñëîâûå ïîëÿ
            $not_num = array();
            $result = mysql_query('SHOW COLUMNS FROM `'.$table.'`;'/*, $link*/);
            $start = 0;
            while($row = mysql_fetch_row($result)){
                if (!preg_match("/^(tinyint|smallint|mediumint|bigint|int|float|double|real|decimal|numeric|year|NULL)/", $row[1])) {
                    $not_num[$start] = 1;
                }
                $start++;
            }
            //íà÷èíàåì ïðîèçâîäèòü âûáîðêè äàííûõ
            $start = 0;
            while($count>0){
                $result = mysql_query('SELECT * FROM `'.$table.'` LIMIT '.$start.', '.$delta.';'/*, $link*/);
                $content = 'INSERT INTO `'.$table.'` VALUES ';
                $GLOBALS["SQL_DEBUG"].=$content."<br />";
                $first = true;
                while($row = mysql_fetch_row($result)){
                    $content .= $first ? "\n(" : ",\n(";
                    $first2 = true;
                    foreach($row as $index=>$field){
                        if(isset($not_num[$index])){
                            //$field = addslashes($field);
                            $field = str_replace("\n", "\\n", $field);
                            $content .= !$first2 ? (",'".$field."'") : ("'".$field."'");
                        }else{
                            $content .= !$first2 ? (",".$field) : $field;
                        }
                        $first2 = false;
                    }
                    $content .= ")";
                    $first = false;
                }
                //ñîõðàíÿåì ðåçóëüòàòû âûáîðêè
                file_put_contents($file, $content.";\n\n", FILE_APPEND);
                $count -= $delta;
                $start += $delta;
            }
        }
    }

    static function countRowsInTable($table_name,$where)
    {
        if($where == ""){$where = "1=1";}
        $query = "SELECT COUNT(1) FROM `{$table_name}` WHERE {$where}";
        $GLOBALS["SQL_DEBUG"].=$query."<br />";
        $res = mysql_query($query);
        $cnt  = mysql_fetch_array($res);

        return $cnt[0];
    }

    static function queryError($res,$query,$f="",$l="") 
    {
    	$GLOBALS["SQL_DEBUG"].=$query."<br />";
        if(!$res) 
        {
            $message  =  PAGE_URL . "\n\r<br />";
            $message .= 'Неверный запрос: ' . mysql_error() . "\n\r<br />";
            $message .= 'Запрос целиком: ' . $query;
            mail("you@mkr.com.ua,toltecos@ukr.net","SQL_ERROR",$message);
            die($message);
        }       
    }
 
    static function deleteAllFields($table){
		$query = "TRUNCATE  `{$table}`";
		$GLOBALS["SQL_DEBUG"].=$query."<br />";
		$res = mysql_query($query);
        mysql::queryError($res,$query);
	} 

}
 ?>
