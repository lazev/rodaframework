<?php
/*DEFAULT PAGE VARIABLES*/
$style_name          = 'default'; //Name of the style file
$page_title          = 'Contacts'; //Title of the page
$disable_connection  = false; //Connection is enabled by default
$require_login       = true; //If true, require login to open
$template_name       = ''; //If wanna use a different template name

/*REQUIRE START/STOP FILE*/
require '../config.inc.php';
require RODA .'begin_end.php'; //Do not 'echo' nothing above this command
?>

<script type='text/javascript'>
<!--
function insert() {
	$('#id').val('');
	$('#name').val('');
	$('#phone').val('');
	$('#email').val('');
	$('#born_date').val('');
	$('#obs').val('');
	$('#address').val('');
	$('#zipcode').val('');
	$('#city').val('');
	$('#state').val('');
	$('#formcontact').dialog('open');
}

function edit(x) {
	if(empty(x)) x = firstSel('listbox');
	if(x) {
		$.post('ajax.php', { com:'edit', cod:x }, function(data) {
			$('#id').val(        $(data).find('id').text() );
			$('#name').val(      $(data).find('name').text() );
			$('#phone').val(     $(data).find('phone').text() );
			$('#email').val(     $(data).find('email').text() );
			$('#born_date').val( $(data).find('born_date').text() );
			$('#obs').val(       $(data).find('obs').text() );
			$('#address').val(   $(data).find('address').text() );
			$('#zipcode').val(   $(data).find('zipcode').text() );
			$('#city').val(      $(data).find('city').text() );
			$('#state').val(     $(data).find('state').text() );

			$('#formcontact').dialog('open');
		});
	}
}

function list() {
	var lister = new gridList(); //Create the gridList object
	lister.list_function = 'list'; //Js function to list and command to do ajax list
	lister.source_file   = 'ajax.php';
	lister.header        = ['Id=id', 'Name=name', 'Phone=phone', 'E-mail=email', 'Address=address']; //Define the header (Name=orderby)

	$.post(lister.source_file, { com:lister.list_function }, function(data) {

		var contacts = $(data).find('contacts');
		contacts.find('contact').each(function() { //Looping to create the body
			var check   = $(this).attr('id');
			var id      = check;
			var name    = '<a href="javascript:edit('+ id +');">'+ $(this).find('name').text() +'</a>';
			var phone   = $(this).find('phone').text();
			var email   = $(this).find('email').text();
			var address = $(this).find('address').text();
			lister.body.push([check, id, name, phone, email, address]);
		});

		lister.getListInfo($(data).find('listinfo')); //Get extra info about the list
		lister.write('listbox'); //Write the result at the destiny
	});
}

function remove() {
	x = allsell('listbox');
	if((!empty(x)) && (confirm('Are you sure?'))) {
		$.post('ajax.php', 'com=remove&cods='+ x , function(data) {
			if(data == 1) {
				list();
				msg('Deleted records!');
			}
			else warning(data);
		});
	}
}

$(document).ready(function() {

	list();

	/*ACTION BUTTONS*/
	actionButtons([
		{ name: 'Insert new contact', icon: 'ui-icon-plusthick',   click: insert },
		{ name: 'Remove contact',     icon: 'ui-icon-trash',       click: remove }
	]);

	//Create and define fields options
	Fields([
		{ id: 'id',       type: 'hidden' },
		{ id: 'name',     type: 'string',  maxsize: 100,  require: true },
		{ id: 'phone',    type: 'string',  maxsize: 100 },
		{ id: 'email',    type: 'string',  maxsize: 100 },
		{ id: 'born_date',type: 'date' },
		{ id: 'obs',      type: 'textarea' },
		{ id: 'address',  type: 'string',  maxsize: 100 },
		{ id: 'zipcode',  type: 'integer' },
		{ id: 'city',     type: 'string',  maxsize: 100 },
		{ id: 'state',    type: 'string' }
	]);


	$('#formcontact').dialog('option', 'buttons', {
		'Save': function() { defaultSaveButton($(this), 'save', 'list()'); }, //defaultSaveButton($(this), command, callback);
		'Close': function() { $(this).dialog('close'); }
	});
});

//-->
</script>

<?php
/*REQUIRE START/STOP FILE*/
require RODA .'begin_end.php'; //Do not 'echo' nothing above this command
?>