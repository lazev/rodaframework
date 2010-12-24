<?php
/*REQUIRE START/STOP FILE*/
require '../config.inc.php';
require RODA .'begin_end.php'; //Do not 'echo' nothing above this command

if($_POST['lognow']) {

	//Clean the session
	$_SESSION = array();

	//Basic verifications
	if(empty($_POST['user'])) die('You must type your username to get in.');
	if(empty($_POST['pass'])) die('You must type your password to get in.');

	$user = sql("select * from `users` where username='". $_POST['user'] ."' and active='1' limit 1");
	$pass = sql("select password from `users` where id='". $user['id'] ."' and active='1' limit 1");
	if($pass['password'] != $_POST['pass']) die('Username or password invalid.');


	/**************************/
	/*******\ LOGIN OK /*******/
	/**************************/

	//Create the sessions
	$_SESSION['user_logged']       = $user['id'];
	$_SESSION['user_logged_name']  = $user['name'];

	echo true; //true = 1
}

/*REQUIRE START/STOP FILE*/
require RODA .'begin_end.php';
?>