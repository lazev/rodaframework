<?php
$filters[] = 'find';
$filters[] = 'date';

/*REQUIRE START/STOP FILE*/
require '../config.inc.php';
require RODA .'begin_end.php'; //Do not 'echo' nothing above this command

if($_REQUEST['com'] == 'list') {
	//Filters
	$temp = getFilter('find');
	if(!empty($temp)) {
		$filterbyfind = "and (id='$temp' or name like '%$temp%' or phone like '%$temp%' or email like '%$temp%')";
		$filterdescr['Search for'] = $temp;
	}
	$temp = getFilter('date');
	if(!empty($temp)) {
		$filterbydate = "and born_date='". invertdate($temp) ."'";
		$filterdescr['Born date'] = $temp;
	}

	//Main SQL
	$sql = sql("select * from `contacts` where active='1' $filterbyfind $filterbydate". orderby('name') .  limit());
	if($sql) {
		foreach($sql as $reg) {
			$resp['contacts']['contact id="'. $reg['id'] .'"']['name']    = $reg['name'];
			$resp['contacts']['contact id="'. $reg['id'] .'"']['phone']   = $reg['phone'];
			$resp['contacts']['contact id="'. $reg['id'] .'"']['email']   = $reg['email'];
			$resp['contacts']['contact id="'. $reg['id'] .'"']['address'] = $reg['address'];
		}
	}

	//Extra info(total count sql, filterdescr)
	$resp['listinfo'] = setListInfo("select count(id) as total_reg from `contacts` where active='1' $filterbyfind $filterbydate", $filterdescr);

	array2xml($resp);
}


if($_REQUEST['com'] == 'edit') {
	$resp = sql("select * from `contacts` where id='". $_REQUEST['cod'] ."' limit 1");
	$resp['born_date'] = datemask($resp['born_date']);
	array2xml($resp);
}


if($_REQUEST['com'] == 'save') {
	$reg = array();
	$reg['name']      = $_REQUEST['name'];
	$reg['phone']     = $_REQUEST['phone'];
	$reg['email']     = $_REQUEST['email'];
	$reg['born_date'] = invertdate($_REQUEST['born_date']);
	$reg['obs']       = $_REQUEST['obs'];
	$reg['address']   = $_REQUEST['address'];
	$reg['zipcode']   = $_REQUEST['zipcode'];
	$reg['city']      = $_REQUEST['city'];
	$reg['state']     = $_REQUEST['state'];

	if(empty($_REQUEST['id'])) sql("insert into `contacts`", $reg);
	else sql("update `contacts` set [fields] where id='". $_REQUEST['id'] ."'", $reg);
	echo true;
}


if($_REQUEST['com'] == 'remove') {
	sql("update `contacts` set active='0' where id in (". $_REQUEST['cods'] .")");
	echo true;
}

/*REQUIRE START/STOP FILE*/
require RODA .'begin_end.php';
?>