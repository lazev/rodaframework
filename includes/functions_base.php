<?php
/*
BASE FUNCTIONS
File used by all the pages
*/
include 'class_base.php';

function cript($text, $passkey) {
	$resp = base64_encode($text);
	$md5 = onlynumbers(md5($passkey));
	echo "<br>$resp<br>";
	echo "<br>$md5<br>";
	for($i=1;$i<strlen($md5);$i++) if((is_numeric($md5[$i])) && ($resp[$md5[$i]] !== null) && ($resp[$md5[$i+1]] !== null))  {
		$temp = $resp[$md5[$i]];
		$resp[$md5[$i]*3] = $resp[$md5[$i+2]];
		$resp[$md5[$i+2]*3] = $temp;
	}
	return $resp;
}

function decript($text, $passkey) {
//	$resp = base64_decode($text);
//	return $resp;
	$resp = base64_encode($text);
	$md5 = onlynumbers(md5($passkey));

	for($i=strlen($md5);$i>1;$i--) if((is_numeric($md5[$i])) && ($resp[$md5[$i]] !== null) && ($resp[$md5[$i+1]] !== null))  {
		$temp = $resp[$md5[$i]];
		$resp[$md5[$i]*3] = $resp[$md5[$i+2]];
		$resp[$md5[$i+2]*3] = $temp;
	}

	return $resp;
}

function geraChave($tab, $fat) {
	$tab = $tab*1;
	$cod = $fat*1;

	$md5 = substr(md5($tab . $cod . 'vla2010nfe+'), 5, 10);
	$md5 = substr($md5, 0, 3) .'.'. substr($md5, 3, -3) .'.'. substr($md5, -3);

	$tab = zerofill($tab, 3);
	$tab = substr($tab, 0, -1) .'.'. substr($tab, -1);

	$cod = zerofill($cod, 3);
	$cod = substr($cod, 0, -1) .'.'. substr($cod, -1);

	return $tab . $cod . $md5;
}

function trataChave($key) {
	if(empty($key)) return false;
	else {
		$corte = strpos($key, '.')+2;
		$resp['tab'] = str_replace('.', '', substr($key, 0, $corte))*1;
		$key = substr($key, $corte);

		$corte = strpos($key, '.')+2;
		$resp['cod'] = str_replace('.', '', substr($key, 0, $corte))*1;
		$key = str_replace('.', '', substr($key, $corte));

		$md5 = substr(md5($resp['tab'] . $resp['cod'] . 'vla2010nfe+'), 5, 10);

		if($md5 != $key) return false;
		else return $resp;
	}
}

function getSubdomain() {
	return substr($_SERVER['HTTP_HOST'], 0, strpos($_SERVER['HTTP_HOST'], '.'));
}


/**********************************************\HTML FUNCTIONS/**********************************************/

function hintBox($text) {
	return '<span class="ui-icon ui-icon-comment" style="float: left;" title="'. $text .'"></span>';
}



/**********************************************\FILTERS FUNCTIONS/**********************************************/

function filterword() {
	$filterword = $_SERVER['REQUEST_URI'] .'a';
	$filterword = dirname($filterword);
	return substr($filterword, strrpos($filterword, "/")+1);
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
		echo '<script>' ."\n";
		foreach($_SESSION['FILTER'][$f] as $chave => $valor) echo "FILTER['". $chave ."'] = '". $valor ."';\n";
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

	if($debug) $enterchar = "\n";

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
			echo "<$field>$enterchar";
			array2xml($value, $debug, false);
			echo "</$field2>$enterchar";
		}
		else {

			if(!is_numeric($field)) {
				if((strpos($value, '<') !== false) || (strpos($value, '>') !== false) || (strpos($value, '&') !== false)) echo "<$field><![CDATA[". $value ."]]></$field2>$enterchar";
				else echo "<$field>$value</$field2>$enterchar";
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
*/
function utf8($x) {
	if(is_numeric($x)) return $x;
	elseif(is_array($x)) {
		foreach($x as $key => $value) {
			if(is_array($value)) $resp[$key] = utf8($value);
			else {
				if(mb_detect_encoding($value .' ', 'UTF-8,ISO-8859-1') != 'UTF-8') $resp[$key] = utf8_encode($value);
				else $resp[$key] = $value;
			}
		}
		return $resp;
	}
	else {
		if(mb_detect_encoding($x .' ', 'UTF-8,ISO-8859-1') != 'UTF-8') return utf8_encode($x);
		else return $x;
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
	return eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $email);
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
		if(preg_match("/^(\d{4})-(\d{2})-(\d{2}) ([01][0-9]|2[0-3]):([0-5][0-9]):([0-5][0-9])$/", $date, $matches)) {
			if(checkdate($matches[2], $matches[3], $matches[1])) return true;
		}
	} else { //Only date
		if(preg_match("/^(\d{4})-(\d{2})-(\d{2})$/", $date, $matches)) {
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
function clientfolder($subfolder='') {
	if(!empty($subfolder)) {
		$folder = ROOT .'/'. CLIENTS .'/'. clientkey($_SESSION['user_logged']);
		if(!file_exists($folder)) mkdir($folder);
		$sub = '/'. $subfolder;
	}

	$folder = ROOT .'/'. CLIENTS .'/'. clientkey($_SESSION['user_logged']) . $sub;
	if(!file_exists($folder)) {
		if(mkdir($folder)) return $folder;
		else return false;
	} else return $folder;
}

/*
CLIENTKEY
Subfunction, return the name of the client's folder.
*/
function clientkey($x) {
	return $x . substr(md5($x . "lazevroda"), 0, 3);
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
				if(($file != ".") && ($file != "..")) {
					if(is_dir("$dir/$file")) $counter += deltree("$dir/$file");
					else if(unlink("$dir/$file")) $counter++;
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
			$field = implode(", ", $fields);
			$value = implode("', '", $values);
			$com = $com ." ($field) values ('$value')";
		}
		//If it is an update command
		elseif(strtolower(substr($com, 0, 6)) == 'update') { //UPDATE
			foreach($fields as $key => $value) $texts[] = "$value='". $values[$key] ."'";
			$text = implode(", ", $texts);
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



function showTagList($table, $field, $where='') {
	//Get all tags
	$sql = sql("select $field from $table $where");
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
			foreach($combo as $key => $value) echo $key ."\n";
		}
	}
}

/*MYSQL_UPDATE_CHAGES
Used with sql() function, look for changes before update a table
*/
function mysql_update_changes($sql, $fields, $exclude=null) {
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
function setListInfo($sql) {
	global $register_per_page;

	if(substr(strtolower($sql), -7) != 'limit 1') $sql .= ' limit 1';
	$temp = sql($sql);

	if($temp['total_reg'] !== null) $resp['total_reg'] = $temp['total_reg'];
	elseif($temp['total'] !== null) $resp['total_reg'] = $temp['total'];

	$resp['reg_per_page'] = getFilter('registers');;
	if(empty($resp['reg_per_page'])) $resp['reg_per_page'] = $register_per_page;

	$resp['actual_page'] = getFilter('nowpage');
	if(empty($resp['actual_page'])) $resp['actual_page'] = 1;

	return $resp;
}

/**********************************************\EMAIL FUNCTIONS/**********************************************/

/*
SENDMAIL
Send an email using smtp
*/
function sendmail($para, $assunto, $mensagem, $denome='', $demail='', $html=false) {
	require_once('smtp/class.phpmailer.php');

	$mail = new PHPMailer();
	$mail->SMTP_PORT = SMTP_PORT; //porta de smt a ser utilizada. Neste caso, a 587 que o GMail utiliza
	$mail->Host = SMTP_HOST; //endereço do servidor smtp do GMail
	$mail->Username = SMTP_USERNAME; //usuário SMTP do GMail
	$mail->Password = SMTP_PASSWORD; //senha do usuário SMTP do GMail

	$mail->SetLanguage('br', ''); //língua a ser utilizadda
	$mail->SMTPSecure = 'tls'; //tipo de comunicação a ser utilizada, no caso, a TLS do GMail
	$mail->IsSMTP(); //email para utilizar protocolo SMTP
	$mail->SMTPAuth = true; //autenticação SMTP, no caso do GMail, é necessário
	$mail->WordWrap = 75; // quebra linha sempre que uma linha atingir os caracteres (inicial: 50)

	$mail->IsHTML($html);
	$mail->From = $demail;
	$mail->FromName = $denome;
	$mail->AddAddress($para);
	$mail->AddReplyTo($demail, $de);
	$mail->Subject = $assunto;
	$mail->Body = $mensagem;

	/*
	Opcionais:
	$mail->AddAttachment("/var/tmp/file.tar.gz");         // adc arquivo anexo.
	$mail->AddAttachment("/tmp/image.jpg", "new.jpg");    // adc outro arquivo anexo com nome (opcional).
	$mail->AltBody = "Este é o corpo da mensagem para usuários que possuem a opção de ver o html do email desativada em seu cliente de email";
	*/

	if(!$mail->Send()) $resposta = false; //"Erro no envio: ". $mail->ErrorInfo;
	else $resposta = true; //"Mensagem enviada";

	return $resposta;
}

/*
EMAIL
Send an email with basic mail PHP function
*/
function email($to, $subject, $msg, $from="", $frommail="", $html=false) {
	global $egestornome, $egestorurl, $egestoremail;

	if(empty($de)) $de = $egestornome;
	if(empty($demail)) $demail = $egestoremail;
	if($html) {
		$headers  = "MIME-Version: 1.0\n";
		$headers .= "Content-type: text/html; charset=iso-8859-1\n";
	}
	$headers .= "From: $de <$demail>\n";
	$headers .= "X-Sender: $egestornome ($egestorurl)\n";
	$headers .= "X-Mailer: PHP\n";
	$headers .= "X-Priority: 3\n";
	$headers .= "Return-Path: <$demail>\n";

	if(checkmail($para)) return mail($para, $assunto, $mensagem, $headers);
	else return false;
}


/*
ERRORMONITOR
Send warnings and errors to an email
*/
function errormonitor($msg, $avisar=true) {
	global $PHP_SELF, $HTTP_HOST, $egestornome;
	$texto = date('d/m/Y H:i') ."\t". $PHP_SELF ."\t". $_SESSION['empresa_logada'] ."\t". $msg ."\n";
	$arquivo = 'includes/erros.log';
//		if((filectime($arquivo) < time()-60*60) && ($HTTP_HOST != "zipline.homelinux.com:8080") && ($avisar)) {
	$texto = str_replace("\t", "\n", $texto);
	sendmail('vinilazev@gmail.com', "Erro no $egestornome", $texto);
	sendmail('deivison@gmail.com', "Erro no $egestornome", $texto);
//		}
	if((file_exists($arquivo)) && (is_writable($arquivo))) {
		if($handle = fopen($arquivo, 'a')) {
			fwrite($handle, $texto);
			fclose($handle);
		}
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
function createpass($size=6, $initpass='') {
	$base = 'abcdefghijklmnopqrstuvwxyz123456789';
	for($ii=0; $ii<$size; $ii++) $initpass .= $base{rand(1, 36)};
	return $initpass;
}

/*
CHANGEDATE
Increase/decrease year, month and/or day of some date
*/
function changedate($date, $year=0, $month=0, $day=0) {
	$split = explode('-', $date);
	return date('Y-m-d', mktime (0, 0, 0, $split[1]+$month, $split[2]+$day, $split[0]+$year));
}

/*
DIFFDATE
Return the difference between two dates, in days
*/
function diffdate($date1, $date2) {
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
function plural($text, $number) {
	if($number == 1) return str_replace('#', '', $text);
	else return str_replace('#', 's', $text);
}

/*
MOD11 (Modulo 11)
Create a mod 11 check digit
*/
function mod11($base_val) {
   $result = "";
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



?>
