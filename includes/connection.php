<?
if(!$already_do_connection) {
	$already_do_connection = true; //do not change

	/*START CONNECTION*/
	$connection = mysql_connect($CONNECTION_DATA['server'], $CONNECTION_DATA['login'], $CONNECTION_DATA['pass']) or die('Database connection error: '. mysql_error());

//	mysql_query("SET NAMES 'utf8'");

	if(!empty($CONNECTION_DATA['base'])) mysql_select_db($CONNECTION_DATA['base'], $connection) or die('Error database selection: '. mysql_error());
	$CONNECTION_DATA = array(); //Clear data

} else {

	/*STOP CONNECTION*/
	mysql_close($connection);
}
?>
