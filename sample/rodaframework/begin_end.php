<?php
/*BEGIN*/
if(!$already_call_begin) {
	$already_call_begin = true; //do not change

	/*MOBILE BROWSER IF AVALIABLE*/
	if(!empty($mobile_file)) if(file_exists($mobile_file)) require_once RODA .'includes/mobile_browser.php';

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
		if(!$disable_connection) {
			if(file_exists(ACCESS_FILE)) require ACCESS_FILE;
		}
	}

	if($enable_nusoap) require_once RODA .'includes/nusoap/nusoap.php';
	if($enable_fpdf) require_once RODA .'includes/fpdf/fpdf.php';

	if(!empty($style_name)) include STYLES_FOLDER . $style_name .'.php';

	defineFilters($_REQUEST['changefilter'], $_REQUEST['clearfilter'], $_REQUEST['orderby'], $_REQUEST['nowpage'], $_REQUEST['registers']);
	if(!empty($style_name)) {
		iffilter(); //If filter is set, put on javascript
	}

	if(!empty($_REQUEST['startTutorial'])) $_SESSION['tutorialActive'] = $_REQUEST['startTutorial'];
	if(!empty($_REQUEST['stopTutorial']))  $_SESSION['tutorialActive'] = array();
}


/*END*/
else {
	if(empty($template_name)) $tpl = basename($_SERVER['PHP_SELF'], '.php');
	else $tpl = basename($template_name, '.htm');

	if(file_exists($tpl .'.htm')) {
		include $tpl .'.htm';
		if(!empty($_SESSION['tutorialActive'])) {
			echo '<script src="'. TUTORIALS_FOLDER . $_SESSION['tutorialActive'] .'" type="text/javascript"></script>';
		}
	}

	if(!empty($style_name)) include STYLES_FOLDER . $style_name .'.php';

	if(!$disable_connection) require RODA .'includes/connection.php';
}
?>
