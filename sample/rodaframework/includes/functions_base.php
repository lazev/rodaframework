<?php
/*
BASE FUNCTIONS
File used by all the pages
*/


function getSubdomain() {
	return substr($_SERVER['HTTP_HOST'], 0, strpos($_SERVER['HTTP_HOST'], '.'));
}


/**********************************************\FILTERS FUNCTIONS/**********************************************/

function filterword() {
	$filterword = $_SERVER['REQUEST_URI'] .'a';
	$filterword = dirname($filterword);
	return substr($filterword, strrpos($filterword, '/')+1);
}

function getFilter($name) {
	return $_SESSION['FILTER'][filterword()][$name];
}

function setFilter($name, $value) {
	$_SESSION['FILTER'][filterword()][$name] = $value;
	return $value;
}

function defineFilters($changefilter, $clearfilter, $orderby, $nowpage, $registers) {
	global $filters, $register_per_page;

	if(is_array($filters)) {
		foreach($filters as $name) {
			$temp = 'filter_'. $name;
			global $$temp;
		}
	}

	if($changefilter) {
		foreach($filters as $name) {
			$temp = $_REQUEST['filter_'. $name];
			setFilter($name, $temp);
		}
		setFilter('activefilter', true);
		$nowpage = 1;
	}

	if($clearfilter) {
		if(is_array($filters)) foreach($filters as $name) setFilter($name, null);
		setFilter('activefilter', null);
		$nowpage = 1;
	}

	if($orderby) {
		$actual = getFilter('orderby');
		$tempold = explode(' ', $actual);
		$tempnew = explode(' ', $orderby);

		//If same old filter then reverse
		if($tempold[0] == $tempnew[0]) {
			if($tempold[1] == 'desc') $so = 'asc';
			else $so = 'desc';
			$orderby = $tempnew[0] .' '. $so;
		}
		setFilter('orderby', $orderby);
	}

	if($registers) {
		setFilter('registers', $registers);
		$nowpage = 1;
	}

	if($nowpage) setFilter('nowpage', $nowpage);
}

function iffilter() {
	$f = filterword();
	if($_SESSION['FILTER'][$f]['activefilter']) {
		echo '<script>'. chr(13);
		foreach($_SESSION['FILTER'][$f] as $chave => $valor) echo 'FILTER["'. $chave .'"] = "'. $valor .'";'. chr(13);
		echo '</script>';
	}
}

/**********************************************\FORMAT, VERIFY and MASK FUNCTIONS/**********************************************/

/*
ZEROFILL
Fill the extra space with zeros.
*/
function zerofill($value, $totalsize=3) {
	while(strlen($value) < $totalsize) $value = '0' . $value;
	return $value;
}

/*
DOT
Transform , to .
*/
function dot($texto) {
	$resp = str_replace('.', '', $texto);
	$resp = trim(str_replace(',', '.', $resp));
	if(is_numeric($resp)) return $resp;
	else return $texto;
}


/*
COMMA
Transform . to ,
*/
function comma($value, $decimals=2, $ifzero=null) {
	if(($ifzero!==null) && ((float)$value==0)) return $sezero;
	else {
		$value = (float)$value;
		if(is_numeric($value)) return number_format($value, $decimals, ',', '');
		else return $value;
	}
}

/*
COMMAIF
Transform . to , and show decimals only if the value have it
*/
function commaif($value, $decimals=2) {
	if(is_numeric((float)$value)) {
		$value = (float)$value;
		if($value == round($value)) return $value;
		return number_format($value, $decimals, ',', '');
	} else {
		return $value;
	}
}

/*
ONLYNUMBERS
Clear all data but numbers
*/
function onlynumbers($x){
	for($i=0;$i<strlen($x);$i++) if(is_numeric($x[$i])) $response .= $x[$i];
	return $response;
}

/*
STRIPCOMMA
Transform 123.45 to 12345 format
*/
function stripcomma($number) {
	return number_format($number, 2, '', '');
}

/*
INVERTDATE
Transform dd/mm/YYYY to YYYY-mm-dd
*/
function invertdate($date='') {
	if($date == '') return '';
	else {
		$split = explode('/',$date);
		if(strlen($split[2]) == 2) {
			if($split[2] < 35) $split[2] = '20'. $split[2];
			else $split[2] = '19'.$split[2];
		}
		return $split[2].'-'. str_pad($split[1], 2, '0', STR_PAD_LEFT) .'-'. str_pad($split[0], 2, '0', STR_PAD_LEFT);
	}
}

/*
DATEMASK
Transform YYYY-mm-dd to dd/mm/YYYY
*/
function datemask($date='') {
	if($date == '') return '';
	else {
		//If it's datetime format
		if(strlen($date) > 12) {
			$prima = explode(' ', $date);
			$hour = ' '. $prima[1];
			$date = $prima[0];
		}
		$split = explode('-', $date);
		if($split[2].'/'.$split[1].'/'.$split[0] != '00/00/0000') return $split[2].'/'.$split[1].'/'.$split[0] . $hour;
		else return '';
	}
}

/*
CEPMASK
Transform 00000000 to 00000-000 format
*/
function cepmask($x) {
	$x = onlynumbers($x);
	return substr($x, 0, 5) .'-'. substr($x, 5);
}

/*
CPFCNPJMASK
Format CPF or CNPJ according to size
*/
function cpfcnpjmask($x) {
	$x = onlynumbers($x);
	if(strlen($x) == 0) return '';
	if(strlen($x) > 12) {
		if(strlen($x) == 14) {
			return substr($x, 0, 2) .'.'. substr($x, 2, 3) .'.'. substr($x, 5, 3) .'/'. substr($x, 8, 4) .'-'. substr($x, 12);
		} else {
			return substr($x, 0, 3) .'.'. substr($x, 3, 3) .'.'. substr($x, 6, 3) .'/'. substr($x, 9, 4) .'-'. substr($x, 13);
		}
	}
	else return substr($x, 0, 3) .'.'. substr($x, 3, 3) .'.'. substr($x, 6, 3) .'-'. substr($x, 9);
}


/*PHONEMASK*/
function phonemask($x) {
	$x = onlynumbers($x);
	if(empty($x)) return '';
	$r3 = substr($x, -4);
	$r2 = substr($x, -8, 4);
	$r1 = substr($x, -10, 2);
	if(!empty($r1)) $r1 .= '-';
	return $r1 . $r2 .'-'. $r3;
}


/*
STRIPACCENT
Strip accents and replace strange chars
*/
function stripaccent($string) {
	return str_replace(
		array('à','á','â','ã','ä', 'ç', 'è','é','ê','ë', 'ì','í','î','ï', 'ñ', 'ò','ó','ô','õ','ö', 'ù','ú','û','ü', 'ý','ÿ', 'À','Á','Â','Ã','Ä', 'Ç', 'È','É','Ê','Ë', 'Ì','Í','Î','Ï', 'Ñ', 'Ò','Ó','Ô','Õ','Ö', 'Ù','Ú','Û','Ü', 'Ý'),
		array('a','a','a','a','a', 'c', 'e','e','e','e', 'i','i','i','i', 'n', 'o','o','o','o','o', 'u','u','u','u', 'y','y', 'A','A','A','A','A', 'C', 'E','E','E','E', 'I','I','I','I', 'N', 'O','O','O','O','O', 'U','U','U','U', 'Y'),
		$string);
}


/*
FRIENDNAME
Convert a name to a friendly domain/shell name
*/
function friendname($x) {
	$permitidos = 'abcdefghijklmnopqrstuvwxyz0123456789-._';
	$x = trim($x);
	$x = str_replace(' ', '-', $x);
	$x = str_replace('--', '-', $x);
	$x = str_replace('-.', '.', $x);
	$x = str_replace('.-', '.', $x);
	$x = str_replace('-.-', '.', $x);
	$x = str_replace('.-.', '.', $x);
	$x = str_replace('..', '.', $x);
	$x = str_replace('&quot;', '', $x);
	$x = strtolower($x);
	$x = stripaccent($x);
	$conta = strlen($x);
	for($ii=0; $ii<$conta; $ii++) if(stripos($permitidos, $x{$ii}) !== false) $resp .= $x{$ii};
	while(strrpos($resp, '.') == (strlen($resp)-1)) $resp = substr($resp, 0, (strlen($resp)-1));
	while(strpos($resp, '.') === 0) $resp = substr($resp, 1);
	while(strpos($resp, 'www.') === 0) $resp = substr($resp, 4);
	while(strpos($resp, 'wwww.') === 0) $resp = substr($resp, 5);
	if(empty($resp)) $resp = 'general';
	return $resp;
}


/*BBCODE
Transform BBCode to HTML code*/
function bbcode($x) {
	$x = strip_tags($x);
	$x = str_replace('[b]', '<b>', $x);
	$x = str_replace('[/b]', '</b>', $x);
	$x = str_replace('[i]', '<i>', $x);
	$x = str_replace('[/i]', '</i>', $x);
	$x = str_replace('[u]', '<u>', $x);
	$x = str_replace('[/u]', '</u>', $x);
	$x = preg_replace('/\[img=(.+)\]/Usi', '<img src="$1">', $x);
	return nl2br($x);
}

/*CLEARBBCODE
Clear all the bbcodes and html tags*/
function clearbbcode($x) {
	$x = strip_tags($x);
	$x = str_replace('[b]', '', $x);
	$x = str_replace('[/b]', '', $x);
	$x = str_replace('[i]', '', $x);
	$x = str_replace('[/i]', '', $x);
	$x = str_replace('[u]', '', $x);
	$x = str_replace('[/u]', '', $x);
	$x = preg_replace('/\[img=(.+)\]/Usi', '', $x);
	return $x;
}



/*
ARRAY2XML
Transform an Array to XML.
*/
function array2xml($x, $debug=false, $header=true) {
	if(empty($x)) return false;

	if($debug) $enterchar = chr(13);

	if($header) {
		header('Content-Type: text/xml; charset=UTF-8');
		header('Content-Disposition: inline; filename=file.xml');

		echo '<?'.'xml version="1.0" encoding="UTF-8"'.'?'.'>'. $enterchar;
		echo '<root>'. $enterchar;
	}

	foreach($x as $field => $value) {
		$temp = explode(' ', $field);
		$field2 = $temp[0];
		if(is_array($value)) {
			if(is_numeric($field)) {
				$field = 'reg id="'. $field .'"';
				$field2 = 'reg';
			}
			echo '<'. $field .'>'. $enterchar;
			array2xml($value, $debug, false);
			echo '</'. $field2 .'>'. $enterchar;
		}
		else {

			if(!is_numeric($field)) {
				if((strpos($value, '<') !== false) || (strpos($value, '>') !== false) || (strpos($value, '&') !== false)) {
					echo '<'. $field .'><![CDATA['. $value .']]></'. $field2 .'>'. $enterchar;
				}
				else echo '<'. $field .'>'. $value .'</'. $field2 .'>'. $enterchar;
			}

			//Strip numeric keys to economize
/*
			if(!is_numeric($field)) {
				if((is_numeric($value)) || empty($value) || (!$usarcdata)) echo "<$field>$value</$field2>$enterchar";
				else echo "<$field><![CDATA[$value]]></$field2>$enterchar";
			}
*/
		}
	}

	if($header) echo '</root>';
}

//REPENSAR ESSA
function diexml($coderror, $obs=null) {
		global $status_list;

		header('Content-Type: text/xml; charset=UTF-8');
		header('Content-Disposition: inline; filename=api.xml');
		echo '<?'.'xml version="1.0" encoding="UTF-8"'.'?'.'>';
		echo '<error>';
		echo '<status>'. $coderror .'</status>';
		echo '<msg>'. $status_list[$coderror] .'</msg>';
		if(!empty($obs)) echo '<obs>'. $obs .'</obs>';
		echo '</error>';
		die();
}


/*UTF8
Convert array or string to utf8 charset
onlyutf exclude all non utf8 chars
*/
function utf8($x, $onlyutf=true) {
	if(is_numeric($x)) return $x;
	elseif(is_array($x)) {
		foreach($x as $key => $value) {
			if(is_array($value)) $resp[$key] = utf8($value);
			else {
				if(mb_detect_encoding($value .' ', 'UTF-8,ISO-8859-1') != 'UTF-8') $value = utf8_encode($value);
				if($onlyutf) $value = iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', $value);
				$resp[$key] = $value;
			}
		}
		return $resp;
	}
	else {
		if(mb_detect_encoding($x .' ', 'UTF-8,ISO-8859-1') != 'UTF-8') $x = utf8_encode($x);
		if($onlyutf) $x = iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', $x);
		return $x;
	}
}


/*ISO88591
Convert array or string to iso88591 charset
*/
function iso88591($x) {
	if(is_numeric($x)) return $x;
	elseif(is_array($x)) {
		foreach($x as $key => $value) {
			if(is_array($value)) $resp[$key] = iso88591($value);
			else {
				if(mb_detect_encoding($value .' ', 'UTF-8,ISO-8859-1') != 'ISO-8859-1') $resp[$key] = utf8_decode($value);
				else $resp[$key] = $value;
			}
		}
		return $resp;
	}
	else {
		if(mb_detect_encoding($x .' ', 'UTF-8,ISO-8859-1') != 'ISO-8859-1') return utf8_decode($x);
		else return $x;
	}
}


/*SUPERTRIM
Trim strings and arrays
*/
function supertrim($x) {
	if(is_array($x)) {
		foreach($x as $key => $value) {
			if(is_array($value)) $resp[$key] = supertrim($value);
			else $resp[$key] = supertrim($value);
		}
		return $resp;
	}
	else return trim($x);
}



/*
IS_EMAIL
Basic check of an email structure
*/
function is_mail($email) {
	return eregi('^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$', $email);
}


/*
IS_CPF
Return true if the value is Brazilian CPF
*/
function is_cpf($x) {
	if(strlen(onlynumbers($x)) > 12) return false;
	return true;
}


/*
IS_DATE
Check if the value is a date (yyyy-mm-dd)
*/
function is_date($date) {

	if(strlen($date) > 12) { //Datetime
		if(preg_match('/^(\d{4})-(\d{2})-(\d{2}) ([01][0-9]|2[0-3]):([0-5][0-9]):([0-5][0-9])$/', $date, $matches)) {
			if(checkdate($matches[2], $matches[3], $matches[1])) return true;
		}
	} else { //Only date
		if(preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $date, $matches)) {
			if(checkdate($matches[2], $matches[3], $matches[1])) return true;
		}
	}

	return false;
}


/**********************************************\FILE & FOLDERS FUNCTIONS/**********************************************/


/*
SIZE_HUM_READ
Return human readable size format
*/
function size_hum_read($size) {
	$i=0;
	$iec = array('B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
	while(($size/1024) > 1) {
		$size=$size/1024;
		$i++;
	}
	return comma(substr($size,0,strpos($size,'.')+4)).$iec[$i];
}

/*
CLIENTFOLDER
Return the client default folder. Create it if doesn't exist.
*/
function clientFolder($subfolder='') {
	if(!empty($subfolder)) {
		$folder = CLIENTS_FOLDER . clientKey($_SESSION['user_logged']);
		if(!file_exists($folder)) mkdir($folder);
		$sub = '/'. $subfolder;
	}

	$folder = CLIENTS_FOLDER . clientKey($_SESSION['user_logged']) . $sub;
	if(!file_exists($folder)) {
		if(mkdir($folder)) return $folder;
		else return false;
	} else return $folder;
}

/*
CLIENTKEY
Subfunction, return the name of the client's folder.
*/
function clientKey($x) {
	return $x . substr(md5($x . 'lazevroda'), 0, 3);
}


/*
LS
List the files in a folder
*/
function ls($dir, $filter='') {
	if(is_dir($dir)) {
		if($handle = opendir($dir)) {
			while(($file = readdir($handle)) !== false) {
				if(($file != '.') && ($file != '..') && (!is_dir($dir .'/'. $file))) {
					if(!empty($filter)) {
						$temp = pathinfo($file);
						if(strpos($filter, $temp['extension']) !== false) $resp[] = $file;
					} else $resp[] = $file;
				}
			}
		} else return false;
		closedir($handle);
		return $resp;
	}
}


/*
DELTREE
Like DOS command
*/
function deltree($dir) {
	if(is_dir($dir)) {
		if($handle = opendir($dir)) {
			while(false !== ($file = readdir($handle))) {
				if(($file != '.') && ($file != '..')) {
					if(is_dir($dir .'/'. $file)) $counter += deltree($dir .'/'. $file);
					else if(unlink($dir .'/'. $file)) $counter++;
				}
			}
		} else return false;
		closedir($handle);
		if(rmdir($dir)) $counter++;
	} else {
		if(unlink($dir)) $counter++;
	}
	return $counter;
}




/**********************************************\DATABASE FUNCTIONS/**********************************************/

/*
SQL
used to work with database

TO INSERT AND UPDATE:
$ins['field'] = 'data';
sql("insert into table", $ins);

TO SELECT:
$response = sql("select * from table where ...");
*/
function sql($com, $insert='', $alternative_connection='', $debug=false) {

	$com = trim($com);

	//If is defined, use the alternative connection
	if(!empty($alternative_connection)) $connection = $alternative_connection;
	//Else, use the global connection
	else global $connection;

	//If $insert is empty, just run the $com command
	if(!empty($insert)) {
		if(is_array($insert)) {
			foreach($insert as $key => $value) {
				if(!empty($key)) {
					$fields[] = $key;
					if(!get_magic_quotes_gpc()) $values[] = addslashes($value);
					else $values[] = $value;
				}
			}
		}
		//If it is an insert command
		if(strtolower(substr($com, 0, 6)) == 'insert') { //INSERT
			$field = implode(', ', $fields);
			$value = implode('\', \'', $values);
			$com = $com .' ('. $field .') values (\''. $value .'\')';
		}
		//If it is an update command
		elseif(strtolower(substr($com, 0, 6)) == 'update') { //UPDATE
			foreach($fields as $key => $value) $texts[] = $value .'=\''. $values[$key] .'\'';
			$text = implode(', ', $texts);
			$com = str_replace('[fields]', $text, $com);
		}
	}

	if($debug) echo '<p>'. $com .'</p>';

	//Do the sql command
	$sql = mysql_query($com, $connection) or die('<b>Mysql error:</b> '. mysql_error() .'<br><b>Command:</b> '. $com);

	if(!$sql) return false;
	else {
		//If it is a select command, return an array;
		if(strtolower(substr($com, 0, 6)) != 'select') return true;
		else {
			if(strtolower(substr($com, -7)) == 'limit 1') { //If is 'limit 1' set, return the value directly
				$response = mysql_fetch_array($sql, MYSQL_ASSOC);
			}
			else { //Else, return into a list
				$response = array();

				//Get the fields name
				$fields_name = mysql_num_fields($sql);
				for($i=0; $i<$fields_name; $i++) $fnames[] = mysql_field_name($sql, $i);

				//Put all the data in array
				while($resp = mysql_fetch_row($sql)) {
					$temp = array();
					foreach($fnames as $key => $value) $temp[$value] = stripslashes($resp[$key]);
					$response[] = $temp;
				}
			}

			return $response;
		}
	}
}

function orderby($default) {
	$temp = getFilter('orderby');
	$orderby = (empty($temp)) ? $default : $temp;
	if(empty($orderby)) return '';
	else return ' order by '. $orderby .' ';
}


function limit() {
	global $register_per_page;

	$registers = getFilter('registers');
	$nowpage   = getFilter('nowpage');
	$limitpage = $nowpage*$registers-$registers;

	if(empty($registers)) $registers = setFilter('registers', $register_per_page);
	if((empty($nowpage)) or ($nowpage < 1)) $nowpage = setFilter('nowpage', 1);

	return ' limit '. $limitpage .', '. $registers;
}


function showTagList($table, $field, $where='') {
	//Get all tags
	$sql = sql('select '. $field .' from '. $table .' '. $where);
	if($sql) {
		foreach($sql as $arr) {
			$cru = str_replace('  ', ' ', $arr[$field]);
			$cru = str_replace(', ', ',', $cru);
			$temp = explode(',', $cru);
			foreach($temp as $value) { //Get only searched tags
				if((!empty($value)) && ((strpos($value, $_REQUEST['q']) !== false) || ($_REQUEST['q'] == '  '))) {
					$combo[$value]++;
				}
			}
		}
		if(is_array($combo)) {
			ksort($combo);
			foreach($combo as $key => $value) echo $key . chr(13);
		}
	}
}

/*MYSQL_UPDATE_CHAGES
Used with sql() function, look for changes before update a table
*/
function mysqlUpdateChanges($sql, $fields, $exclude=null) {
	$sql = trim($sql);
	if(strtolower(substr($sql, -7)) != 'limit 1') $sql = $sql .' limit 1';

	$select = sql($sql);
	foreach($fields as $key => $value) {
		$go = true;

		if(!empty($exclude)) {
			if(is_array($exclude)) {
				if(array_search($key, $exclude) !== false) $go = false;
			}
			elseif(strpos($exclude, $key) !== false) $go = false;
		}

		if($go) {
			if($value != $select[$key]) {
				if(is_date($value)) $value = datemask($value);
				if(is_float($value)) $value = comma($value);
				$resp[] = $key .': '. $value;
			}
		}
	}

	$r = false;
	if(is_array($resp)) $r = implode('; ', $resp) .'.';

	return $r;
}


/*SETLISTINFO
 * Return some info about the list. $sql must be a select count*/
function setListInfo($sql, $filterdescr) {
	global $register_per_page;

	if(substr(strtolower($sql), -7) != 'limit 1') $sql .= ' limit 1';
	$temp = sql($sql);

	if($temp['total_reg'] !== null) $resp['total_reg'] = $temp['total_reg'];
	elseif($temp['total'] !== null) $resp['total_reg'] = $temp['total'];

	$resp['reg_per_page'] = getFilter('registers');;
	if(empty($resp['reg_per_page'])) $resp['reg_per_page'] = $register_per_page;

	$resp['actual_page'] = getFilter('nowpage');
	if(empty($resp['actual_page'])) $resp['actual_page'] = 1;

	if(is_array($filterdescr)) {
		foreach($filterdescr as $label => $descr) {
			$descrs[] = $label .': '. $descr .'.';
		}
		$resp['filter_descr'] = implode(' ', $descrs);
	}

	return $resp;
}

/**********************************************\EMAIL FUNCTIONS/**********************************************/

/*
SENDMAIL
Send an email using smtp
Optimized to GMail
*/
function sendMail($to, $subject, $msg, $fromName='', $fromMail='', $html=false) {
	global $MAIL;

	require_once('smtp/class.phpmailer.php');

	$mail = new PHPMailer();
	$mail->SMTP_PORT = $MAIL['SMTP_PORT']; //Port do SMTP connection. GMail uses 587.
	$mail->Host      = $MAIL['SMTP_HOST']; //Your e-mail address
	$mail->Username  = $MAIL['SMTP_USERNAME']; //User to connect
	$mail->Password  = $MAIL['SMTP_PASSWORD']; //Password to connect

	$mail->SetLanguage('br', ''); //Language to use.
	$mail->SMTPSecure = 'tls'; //Communication secure type. GMail uses TLS.
	$mail->IsSMTP(); //To use SMTP protocol
	$mail->SMTPAuth = true; //GMail requires SMTP authentication.
	$mail->WordWrap = 75; //Break the line when hit the char lenght (default: 50)

	$mail->IsHTML($html);
	$mail->From     = $fromMail;
	$mail->FromName = $fromName;
	$mail->Subject  = $subject;
	$mail->Body     = $msg;
	$mail->AddAddress($to);
	$mail->AddReplyTo($fromMail, $fromName);

	/*
	Extras:
	$mail->AddAttachment("/var/tmp/file.tar.gz");         // File attached
	$mail->AddAttachment("/tmp/image.jpg", "new.jpg");    // File attached with other name
	$mail->AltBody = "Text to show to users with HTML view desactived";
	*/

	return $mail->Send(); //Returns true or false
}



/*
ERRORMONITOR
Send warnings and errors to an email
*/
function errorMonitor($msg) {
	global $ALERT_EVENT_EMAIL;
	$text = date('d/m/Y H:i') .chr(13). $_SERVER['PHP_SELF'] .chr(13). $msg . chr(13);
	foreach($ALERT_EVENT_EMAIL as $mail) {
		sendMail($mail, 'Error on '. SYSTEM_NAME, $texto);
	}
}




/**********************************************\OTHER FUNCTIONS/**********************************************/


/*
DEBUG
used to... debug!
*/
function debug($x) {
	echo '<pre>';
	if(is_array($x)) print_r($x);
	else echo $x;
	echo '</pre>';
}

/*
CREATEPASS
Create a random pass with easy chars
*/
function createPass($size=6, $initpass='') {
	$base = 'abcdefghijklmnopqrstuvwxyz123456789';
	for($ii=0; $ii<$size; $ii++) $initpass .= $base{rand(1, 36)};
	return $initpass;
}

/*
CHANGEDATE
Increase/decrease year, month and/or day of some date
*/
function changeDate($date, $year=0, $month=0, $day=0) {
	$split = explode('-', $date);
	return date('Y-m-d', mktime (0, 0, 0, $split[1]+$month, $split[2]+$day, $split[0]+$year));
}

/*
DIFFDATE
Returns the difference between two dates, in days
*/
function diffDate($date1, $date2) {
	//first date
	$date1 = explode('-', $date1);
	$year1 = $date1[0];
	$month1 = $date1[1];
	$day1 = $date1[2];
	//second date
	$date2 = explode('-', $date2);
	$year2 = $date2[0];
	$month2 = $date2[1];
	$day2 = $date2[2];
	//calc
	$date1 = mktime(0, 0, 0, $month1, $day1, $year1);
	$date2 = mktime(0, 0, 0, $month2, $day2, $year2);
	$days = ($date2 - $date1)/86400;
	$days = ceil($days);
	return $days;
}

/*
PLURAL
*/
function plural($text, $number, $doublechar=null) {
	if($number == 1) return str_replace('#', '', $text);
	else {
		if($doublechar) $text = str_replace('##', $doublechar, $text);
		return str_replace('#', 's', $text);
	}
}

/*
MOD11 (Modulo 11)
Create a mod 11 check digit
*/
function mod11($base_val) {
   $result = '';
   $weight = array(2, 3, 4, 5, 6, 7,
                   2, 3, 4, 5, 6, 7,
                   2, 3, 4, 5, 6, 7,
                   2, 3, 4, 5, 6, 7);

	/* For convenience, reverse the string and work left to right. */
	$reversed_base_val = strrev($base_val);

	/* Calculate product and accumulate. */
	for($i=0, $sum=0; $i<strlen($reversed_base_val); $i++) $sum += substr( $reversed_base_val, $i, 1 ) * $weight[ $i ];

	/* Determine check digit, and concatenate to base value. */
	$remainder = $sum % 11;
	switch($remainder) {
	case 0:
		$result = 0;
		break;
	case 1:
		$result = 'n/a';
		break;
	default:
		$check_digit = 11 - $remainder;
		$result = $check_digit;
		break;
	}

	return $result;
}



/*CRIPT and DECRIPT a text with a passkey*/
function cript($text, $passkey) {
	$text = algorithm($text, $passkey);
	for($i=0, $r=chr(rand(65, 90)); $i<strlen($text); $i++) $r .= ord(strval(substr($text, $i, 1))) . chr(rand(65, 90));
	return $r;
}
function decript($text, $passkey) {
	$text = join(array_map('chr', preg_split('/[A-Z]/', substr(substr($text, 1), 0, strlen($text) - 2))));
	return algorithm($text, $passkey);
}
function algorithm($text, $privatekey) {
	$k   = 0;
	$l   = 0;
	$r   = '';
	$len = strlen($privatekey);
	for($j=0; $j<=255; $j++) {
		$key[$j]  = ord(substr($privatekey, $j % $len, 1));
		$sbox[$j] = $j;
	}
	for($k=0; $k<=255; $k++) {
		$l        = ($l + $sbox[$k] + $key[$k]) % 256;
		$i        = $sbox[$k];
		$sbox[$k] = $sbox[$l];
		$sbox[$l] = $i;
	}
	for($j=1; $j<=strlen($text); $j++) {
		$k        = ($k + 1) % 256;
		$l        = ($l + $sbox[$k]) % 256;
		$i        = $sbox[$k];
		$sbox[$k] = $sbox[$l];
		$sbox[$l] = $i;
		$i1       = $sbox[($sbox[$k] + $sbox[$l]) % 256];
		$j1       = ord(substr($text, $j - 1, 1)) ^ $i1;
		$r       .= chr($j1);
	}
	return $r;
}


/*GENERATEKEY
Create a key with the table name, any id and MD5 check security
Returns string with the key
Example: 00.1134.48ae.1b8f.c24
*/
function generateKey($table, $id, $passkey) {
	$id = (int)$id;

	$md5 = substr(md5($table . $id . $passkey), 5, 10);
	$md5 = substr($md5, 0, 3) .'.'. substr($md5, 3, -3) .'.'. substr($md5, -3);

	$table = zerofill($table, 3);
	$table = substr($table, 0, -1) .'.'. substr($table, -1);

	$id = zerofill($id, 3);
	$id = substr($id, 0, -1) .'.'. substr($id, -1);

	return $table . $id . $md5;
}

/*EXTRACTKEY
Extract table and ID from the above function
Returns:
  $array['table']
  $array['id']
or false if wrong key
*/
function extractKey($key, $passkey) {
	if(empty($key)) return false;
	else {
		$cut = strpos($key, '.')+2;
		$resp['table'] = str_replace('.', '', substr($key, 0, $cut))*1;
		$key = substr($key, $cut);

		$cut = strpos($key, '.')+2;
		$resp['id'] = str_replace('.', '', substr($key, 0, $cut))*1;
		$key = str_replace('.', '', substr($key, $cut));

		$md5 = substr(md5($resp['table'] . $resp['id'] . $passkey), 5, 10);

		if($md5 != $key) return false;
		else return $resp;
	}
}

?>