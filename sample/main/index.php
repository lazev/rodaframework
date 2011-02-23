<?php
/*DEFAULT PAGE VARIABLES*/
$style_name          = 'default'; //Name of the style file
$page_title          = 'Contacts'; //Title of the page
$disable_connection  = false; //Connection is enabled by default
$require_login       = true; //If true, require login to open
$template_name       = ''; //If wanna use a different template name (default index.htm)
$mobile_file        = 'mindex.php'; //To redirect if is a mobile browser

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
	$('#city').val('');
	$('#state').val('');
	$('#tags').val('');
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
			$('#city').val(      $(data).find('city').text() );
			$('#state').val(     $(data).find('state').text() );
			$('#tags').val(      $(data).find('tags').text() );

			$('#detalhes').html( $(data).find('detalhes').text() );

			$('#formcontact').dialog('open');
		});
	}
}

function list() {
	var lister = new gridList(); //Create the gridList object
	lister.list_function = 'list'; //Js function to list and (or not) command to do ajax list
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

function alertar(x) {
	alert(x);
}

$(document).ready(function() {

	list();

	/*ACTION BUTTONS*/
	actionButtons([
		{ name: 'Insert new contact', icon: 'ui-icon-plusthick',   click: insert },
		{ name: 'Remove contact',     icon: 'ui-icon-trash',       click: remove }
	]);

	Filters('listbox', 'ajax.php', 'list', [
		{ id:'find', type:'string', label:'Id, name, phone, email' },
		{ id:'date', type:'date',   label:'Born date' }
	]);

	//Create and define fields options
	Fields([
		{ id: 'id',       type: 'hidden' },
		{ id: 'name',     type: 'readonly',  maxsize: 100,  require: true },
		{ id: 'phone',    type: 'integer',  maxsize: 100 },
		{ id: 'email',    type: 'email',  maxsize: 100 },
		{ id: 'born_date',type: 'date' },
		{ id: 'obs',      type: 'textarea' },
		{ id: 'address',  type: 'string',  maxsize: 100 },
		{ id: 'city',     type: 'autocomplete', action:'ajax.php?com=autocity', hidden:'codcity' },
		{ id: 'state',    type: 'string' },
		{ id: 'tags',     type: 'tags',         action:'ajax.php?com=autocity'  },
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