<?php /*HEADER*/
if(!$already_show_header) { $already_show_header = true; //Do not change
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html><head>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8">
	<meta name="robots" content="noarchive">
	<link rel="shortcut icon" href="<?=ROOT_URL?>_includes/styles/img/favicon.ico">
	<title><?=$page_title?></title>

	<?=CSS_RODA?>
	<link rel="stylesheet" type="text/css" href="<?=ROOT_URL?>_includes/styles/default.css"/>
	<link rel="stylesheet" type="text/css" href="<?=ROOT_URL?>_includes/styles/print.css" media="print"/>

	<?=JS_RODA?>
	<script type="text/javascript" src="<?=ROOT_URL?>_includes/functions.js"></script>
</head>
<body>
<div id="header" class="hideOnPrint">

	<div id="auxmenu">
		<?if(!empty($_SESSION['user_logged'])) {?>
			<span><a href='../logout'>Logoff</a></span>

			<?if(!empty($_SESSION['user_logged'])) {?>
				<span><?=$_SESSION['user_logged_name']?></span>
			<?}?>
		<?}?>
	</div>

	<div id="logo">
		<img src="<?=ROOT_URL?>_includes/styles/img/roda_black_logo.png" class="logo">
	</div>

	<?if(!empty($_SESSION['user_logged'])) {?>
	<div id="headermenu">
		<span><a href='../main'>Home</a></span>
	</div>
	<?}?>
</div>

<div id="navigationBar" class="hideOnPrint">
	<h1><?=$page_title?></h1>
</div>

<div id="content">




<?php /*FOOTER*/
} else {?>

	<div class='clearer'></div>
	</div>
	<div id="footer" class="hideOnPrint">RodaFramework give me a hand, thanks.</div>
</body>
</html>

<?}?>
