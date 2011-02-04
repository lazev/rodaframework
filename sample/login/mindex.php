<?php
/*DEFAULT PAGE VARIABLES*/
$style_name         = 'mobile'; //Name of the style file
$page_title         = 'Login screen'; //Title of the page
$disable_connection = true; //Connection is enabled by default
$require_login      = false; //If true, require login to show
$template_name      = 'mindex.htm'; //If wanna use a different template name (default index.htm)

/*REQUIRE START/STOP FILE*/
require '../config.inc.php';
require RODA .'begin_end.php'; //Do not 'echo' nothing above this command
?>


<form id="formlogin">
<input type="hidden" name="lognow" value="true" />

<div data-role="page" data-theme="b" id="jqm-home">

	<div data-role="fieldcontain">
		<label for="user">Username</label>
		<input type="text" id="user" name="user" value="" />

		<label for="pass">Password</label>
		<input type="password" id="pass" name="pass" value="" />

		<button type="button" data-theme="b" class="ui-btn-hidden" tabindex="-1" onclick="doin()">Login</button>
	</div>

</div>

</form>




<script type='text/javascript'>
<!--

/*CLICK ON BUTTON*/
function doin() {
	$.post("ajax.php", $("#formlogin").serialize(), function(data) {

		if(data == 1) window.location = '../main';
		else alert(data);

	});
}

$(document).ready(function(){
	$('#formlogin').submit(function(event) {
		event.preventDefault;
		doin();
	});
});
//-->
</script>


<?php
/*REQUIRE START/STOP FILE*/
require RODA .'begin_end.php'; //Do not 'echo' nothing above this command
?>
