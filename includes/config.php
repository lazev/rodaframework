<?
/*BASIC HTML STRUCTURE*/

define(MENU_RODA, '
	<script type="text/javascript" src="'. RODA_INCLUDES .'lazevmenu.js"></script>
');

define(JS_RODA, '
	<script type="text/javascript" src="'. RODA_INCLUDES .'jquery/jquery-1.4.2.min.js"></script>
	<script type="text/javascript" src="'. RODA_INCLUDES .'jquery/jquery-ui-1.8.custom.min.js"></script>
	<script type="text/javascript" src="'. RODA_INCLUDES .'jquery/jquery.autocomplete.min.js"></script>
	<script type="text/javascript" src="'. RODA_INCLUDES .'jquery/jquery.bgiframe.min.js"></script>
	<!--[if IE]>
	<script type="text/javascript" src="'. RODA_INCLUDES .'jquery/excanvas.compiled.js"></script>
	<![endif]-->
	<script type="text/javascript" src="'. RODA_INCLUDES .'jquery/jquery.bt.min.js"></script>
	<script type="text/javascript" src="'. RODA_INCLUDES .'jquery/jquery.validate.min.js"></script>
	<script type="text/javascript" src="'. RODA_INCLUDES .'jquery/jquery.jqplot.min.js"></script>
	<script type="text/javascript" src="'. RODA_INCLUDES .'functions_base.js"></script>
	<script type="text/javascript" src="'. RODA_INCLUDES .'functions_fields.js?v=2"></script>
');
//tirei fora o <script type="text/javascript" src="'. RODA_INCLUDES .'jquery/jquery.validate.min.js"></script>


define(CSS_RODA, '
	<link rel="stylesheet" type="text/css" href="'. RODA_STYLES .'general.css?v=2"/>
	<link rel="stylesheet" type="text/css" href="'. RODA_STYLES .'jquery/'. $jquery_css_ui_theme .'/jquery-ui-1.8.custom.css"/>
	<link rel="stylesheet" type="text/css" href="'. RODA_STYLES .'jquery/jquery.jqplot.min.css"/>
');

?>
