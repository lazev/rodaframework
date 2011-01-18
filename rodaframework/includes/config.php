<?PHP
/* BASIC HTML STRUCTURE
 * This file is used to call all the default scripts and css framework's files
 * */

define(JS_RODA, '
	<script type="text/javascript" src="'. RODA_INCLUDES .'jquery/jquery-1.4.2.min.js"></script>
	<script type="text/javascript" src="'. RODA_INCLUDES .'jquery/jquery-ui-1.8.custom.min.js"></script>
	<script type="text/javascript" src="'. RODA_INCLUDES .'jquery/jquery.autocomplete.min.js"></script>
	<script type="text/javascript" src="'. RODA_INCLUDES .'jquery/jquery.bgiframe.min.js"></script>
	<!--[if IE]>
	<script type="text/javascript" src="'. RODA_INCLUDES .'jquery/excanvas.compiled.js"></script>
	<![endif]-->
	<script type="text/javascript" src="'. RODA_INCLUDES .'jquery/jquery.bt.min.js"></script>
	<script type="text/javascript" src="'. RODA_INCLUDES .'jquery/jquery.jqplot.min.js"></script>
	<script type="text/javascript" src="'. RODA_INCLUDES .'sprintf.js"></script>
	<script type="text/javascript" src="'. RODA_INCLUDES .'functions_base.js"></script>
	<script type="text/javascript" src="'. RODA_INCLUDES .'functions_fields.js?v=2"></script>
');


//$jquery_css_ui_theme - from config.inc.php file
define(CSS_RODA, '
	<link rel="stylesheet" type="text/css" href="'. RODA_STYLES .'general.css"/>
	<link rel="stylesheet" type="text/css" href="'. RODA_STYLES .'jquery/'. $jquery_css_ui_theme .'/jquery-ui-1.8.custom.css"/>
	<link rel="stylesheet" type="text/css" href="'. RODA_STYLES .'jquery/jquery.jqplot.min.css"/>
');
?>
