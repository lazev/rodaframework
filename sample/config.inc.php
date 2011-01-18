<?php
//Good to development time
error_reporting(E_ERROR | E_WARNING | E_PARSE);

/*LOCALE AND TIMEZONE*/
setlocale(LC_ALL, 'pt_BR');
date_default_timezone_set('America/Sao_Paulo');

/*CONNECTION VARIABLES*/
$CONNECTION_DATA['server'] = 'localhost';
$CONNECTION_DATA['login']  = 'your_db_login';
$CONNECTION_DATA['pass']   = 'your_db_pass';
$CONNECTION_DATA['base']   = 'rodaframework'; //database name

/*STYLES*/
$jquery_css_ui_theme = 'redmond';
//$jquery_css_ui_theme = 'ui-lightness';
//$jquery_css_ui_theme = 'smoothness';
//$jquery_css_ui_theme = 'custom-gray';

//Default number of registers on a list
$register_per_page = 15;

/*MONITORING E-MAIL*/
//System send an email when an error occurs
$ALERT_EVENT_EMAIL[] = 'your@email.com';

/*DEFINE PATHS*/
//Always with / at the end
$HTTP = ($_SERVER['HTTPS'] == 'on') ? 'https://' : 'http://';

define(RODA,          '/var/www/rodaframework/rodaframework/'); //Logical framework path
define(RODA_URL,      $HTTP .'zipline.homelinux.com:8080/rodaframework/rodaframework/'); //Framework URL
define(RODA_STYLES,   RODA_URL .'styles/');
define(RODA_INCLUDES, RODA_URL .'includes/');

define(ROOT,             dirname(__FILE__) .'/'); //Logical software path
define(ROOT_URL,         $HTTP .'zipline.homelinux.com:8080/rodaframework/sample/'); //Software URL
define(STYLES_FOLDER,    ROOT .'_includes/styles/');
define(INCLUDES_FOLDER,  ROOT .'_includes/');
define(TUTORIALS_FOLDER, ROOT_URL .'includes/tutorials/');

//Access path & file
define(LOGIN_FOLDER, 'login/');
define(ACCESS_FILE,  ROOT . LOGIN_FOLDER .'access.php'); //Script call this file to garantee access
define(TABLEPREFIX,  'rod_'); //Used with the ACCESS_FILE if you wanna use different table after the login

//KEEP SESSIONS ALIVE
//Used to never lost session refreshing each 10 minutes.
//If you don't wanna use, left empty.
define(WAKEUP_FILE, RODA  .'includes/wakeup.php');

/*EMAIL DEFINITIONS*/
//To use with the smtp send mail framework function
define(SMTP_HOST,     'smtp...com');
define(SMTP_USERNAME, 'your@email.com');
define(SMTP_PASSWORD, 'pass');
define(SMTP_PORT,     '587');

//Your own functions file (you can include others files here too)
include INCLUDES_FOLDER .'functions.php';
?>