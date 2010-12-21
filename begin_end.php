<?
/*BEGIN*/
if(!$already_call_begin) {
	$already_call_begin = true; //do not change

	/*START SESSION*/
	session_start();

	/*DEFAULT INCLUDES*/
	require_once RODA .'includes/config.php';
	require_once RODA .'includes/functions_base.php';

	if(!$disable_connection) require RODA .'includes/connection.php';

	if(!$_SESSION['user_logged']) {
		if($require_login) {
			header('Location: '. ROOT_URL . LOGIN_FOLDER);
			exit();
		}
	} else {
		if(!$disable_connection) require ACCESS_FILE;
	}

	if($enable_nusoap) require_once RODA .'includes/nusoap/nusoap.php';
	if($enable_fpdf) require_once RODA .'includes/fpdf/fpdf.php';

	if(!empty($style_name)) include STYLES_FOLDER . $style_name .'.php';

	defineFilters($_REQUEST['changefilter'], $_REQUEST['clearfilter'], $_REQUEST['orderby'], $_REQUEST['nowpage'], $_REQUEST['registers']);
//	if((!empty($style_name)) || (strpos(basename($_SERVER['REQUEST_URI']), 'ajax.php') === false)) { //Double check to do filters
	if(!empty($style_name)) {
		iffilter(); //If filter is set, put on javascript
	}

	if(!empty($_REQUEST['startTutorial'])) $_SESSION['tutorialStarted'] = $_REQUEST['startTutorial'];
	if(!empty($_REQUEST['stopTutorial'])) $_SESSION['tutorialStarted'] = array();
}


/*END*/
else {
	if(empty($template_name)) $tpl = basename($_SERVER['PHP_SELF'], '.php');
	else $tpl = basename($template_name, '.htm');

	if(file_exists($tpl .'.htm')) {
		include $tpl .'.htm';
		if(!empty($_SESSION['tutorialStarted'])) echo '<script src="'. TUTORIALS_FOLDER . $_SESSION['tutorialStarted'] .'" type="text/javascript"></script>';
	}

	if(!empty($style_name)) include STYLES_FOLDER . $style_name .'.php';

	if(!$disable_connection) require RODA .'includes/connection.php';
}
?>
