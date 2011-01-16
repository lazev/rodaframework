<?php
/*DEFAULT PAGE VARIABLES*/
$style_name         = 'default'; //Name of the style file
$page_title         = 'Login screen'; //Title of the page
$disable_connection = true; //Connection is enabled by default
$require_login      = false; //If true, require login to show
$template_name      = ''; //If wanna use a different template name

/*REQUIRE START/STOP FILE*/
require '../config.inc.php';
require RODA .'begin_end.php'; //Do not 'echo' nothing above this command
?>


<script type='text/javascript'>
<!--

$(document).ready(function(){

	Fields([
		{ id: 'user',   type: 'login',    label: 'Username', status: 'Type your username', focus: true },
		{ id: 'pass',   type: 'password', label: 'Password', status: 'Type your password' },
		{ id: 'submit', type: 'button',   label: 'Login',    icon: 'ui-icon-home', click: doin }
	]);


	/*CLICK ON BUTTON*/
	//$('#buttonin').click(doin);
	function doin() {
		$.post("ajax.php", $("#formlogin").serialize(), function(data) {

			if(data == 1) window.location = '../main';
			else warning(data);

		});
	}
});
//-->
</script>


<?php
/*REQUIRE START/STOP FILE*/
require RODA .'begin_end.php'; //Do not 'echo' nothing above this command
?>