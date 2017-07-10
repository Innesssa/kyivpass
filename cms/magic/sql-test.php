<?
	echo getenv("DOCUMENT_ROOT");

$filewrite=fopen(getenv("DOCUMENT_ROOT")."/magic/MySQL.log","w+");
$paper='';
fwrite($filewrite,$paper);
fclose($filewrite);

include_once("../../kernel/kernel.php");

$query = "SELECT * FROM `tag_item`";
$res = mysql_query($query);

//mysql_close($dbh);?>
<pre>
	<?
$mainFile = @file(getenv("DOCUMENT_ROOT")."/magic/MySQL.log");
$i = 0;$html='';
while (!empty ($mainFile[$i])){
$html.= stripslashes($mainFile[$i]);
$i = $i + 1; }
echo $html;
?>
</pre>
