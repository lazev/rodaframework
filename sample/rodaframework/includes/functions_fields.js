/*CLASS - FILTER BOX*/
function Filters(listid, source, jscommand, fields) {
	//Main form
	var form = document.createElement('form');
	$(form).attr('id', 'filterBox');
	$(form).addClass('ui-corner-bl ui-corner-tl');
	$(form).submit(function(){
		$(':submit', this).trigger('click');
		return false;
	});

	//Fields
	for(k in fields) {
		var div = document.createElement('div');
		fields[k].id = 'filter_'+ fields[k].id;
		$(div).attr('id', fields[k].id);
		$(form).append($(div));
	}

	//Button
	var but = document.createElement('input');
	$(but).attr('type', 'submit');
	$(but).addClass('ui-button ui-state-default ui-corner-all');
	$(but).val('Filtrar');
	$(but).click(function(event) { doFilter(event, source, jscommand) });
	$(form).append($(but));

	//Links
	var linx = document.createElement('a');
	$(linx).attr('href', '');
	$(linx).addClass('clearFilters');
	$(linx).html('Limpar filtros');
	$(form).append($(linx));

	var linx = document.createElement('a');
	$(linx).attr('href', '');
	$(linx).addClass('clearFilters');
	$(linx).html('Ocultar caixa');
//	$(linx).click(closeFilterBox);
	$(form).append($(linx));


	//OpenFilters Div
	var div = document.createElement('div');
	$(div).attr('id', 'openFilters');
	$(div).addClass('ui-corner-bl ui-corner-tl hideOnPrint');
	$(div).html('F<br/>I<br/>L<br/>T<br/>R<br/>O<br/>S');
	var span = document.createElement('span');
	$(span).addClass('ui-icon ui-icon-grip-diagonal-se');
	$(div).prepend($(span));

	if($('#putFilterHere').length) {
		$('#putFilterHere').prepend($(form));
		$('#putFilterHere').prepend($(div));
	} else {
		$('#content').prepend($(form));
		$('#content').prepend($(div));
	}

	Fields(fields);

	$('#openFilters').click(function(event) {
		event.preventDefault();
		$('#'+ listid).animate({ marginRight: '180px' }, 300 );
		$('#filterBox').show('slide', {direction: 'right'}, 'fast');
		$('#openFilters').hide('slide', {direction: 'right'}, 'fast');
	});

	$('.clearFilters').click(function(event) {
		event.preventDefault();
		clearFilter(event, source, jscommand);
		$('#filterBox').hide('slide', {direction: 'right'}, 'fast');
		$('#filterBox')[0].reset();
		$('#'+ listid).animate({ marginRight: '0px' }, 300 );
		$('#openFilters').show('slide', {direction: 'right'}, 'fast');

	});

	if((!empty(FILTER)) && (FILTER['activefilter'])) {
		$('#openFilters').trigger('click');
		for(k in FILTER) {
			if((k != 'registers') && (k != 'nowpage') && (k != 'activefilter') && ($('#filter_'+ k).length)) $('#filter_'+ k).val(FILTER[k]);
		}
	}
}



/*CLASS - GENERAL FIELDS*/
acOnSel    = new Array();
acHidVal   = new Array();
regexRules = new Array();
statusMsgs = new Array();
alertMsgs  = new Array();

function Fields(x) {

	//Type of field define kind of input text
	kindof = new Array();
	kindof['string'] = 'text';
	kindof['readonly'] = 'text';
	kindof['integer'] = 'text';
	kindof['integer-'] = 'text';
	kindof['money'] = 'text';
	kindof['money-'] = 'text';
	kindof['date'] = 'text';
	kindof['login'] = 'text';
	kindof['autocomplete'] = 'text';
	kindof['tags'] = 'text';
	kindof['hidden'] = 'hidden';
	kindof['password'] = 'password';
	kindof['check'] = 'checkbox';
	kindof['checkbox'] = 'checkbox';
	kindof['radio'] = 'radio';
	kindof['upload'] = 'file';
	kindof['file'] = 'file';
	kindof['cpf'] = 'text';
	kindof['cnpj'] = 'text';
	kindof['cpfcnpj'] = 'text';
	kindof['cnpjcpf'] = 'text';
	kindof['email'] = 'text';
	kindof['suggest'] = 'text';

	for(k in x) {

		var prop = x[k]; //All the field properties
		var autocomplete = 'off'; //Browser autocomplete disabled by default
		prop.classes += ' texto ui-widget-content ui-corner-all'; //Field default classes


		//Create a select element and set basic attributes
		if(prop.type == 'select') {
			var elem = document.createElement('select');
			var opt = new Array();
			var optsel = '';
			var so = 0;

			for(v in prop.options) {
				if((v == prop.selected) || (v == prop.value)) optsel = 'selected="selected"';
				else optsel = '';

				if(typeof(prop.options[v]) == 'string') {
					opt[so++] = '<option value="'+ v +'" '+ optsel +'>'+ prop.options[v] +'</option>';
				} else {
					opt[so++] = '<option value="'+ prop.options[v]['key'] +'" '+ optsel +'>'+ prop.options[v]['value'] +'</option>';
				}
			}

			$(elem).append(opt.join(''));
		}

		//Create a textarea element and set basic attributes
		else if(prop.type == 'textarea') var elem = document.createElement('textarea');

		//Create a textarea element and set basic attributes
		else if((prop.type == 'submit') || (prop.type == 'button')) {
			var elem = document.createElement('button');
			//$(elem).attr('type', prop.type);
			$(elem).button();
			if(!empty(prop.label)) $(elem).button("option", "label", prop.label);
			if(!empty(prop.icon))  $(elem).button("option", "icons", { primary: prop.icon } );
			if(!empty(prop.click)) $(elem).click( prop.click );
			prop.label = null;

		}

		//Create a textbox element (and others) and set basic attributes
		else {
			var elem = document.createElement('input');
			$(elem).attr('type', kindof[prop.type]);
		}

		if(prop.type == 'suggest') {
			var sugbox = '<br/><span id="'+ prop.id +'SuggestionBox" class="suggestionBox">';
			if(!empty(prop.sugtitle)) sugbox += '<strong>'+ prop.sugtitle +'</strong>';
			for(sug in prop.list) sugbox += '<div onclick="$(\'#'+ prop.id +'\').val(\''+ sug +'\')">'+ prop.list[sug] +'</div>';
			sugbox += '</span>';
		}

		//If is a checkbox...
		if((prop.type == 'check') || (prop.type == 'checkbox') || (prop.type == 'radio')) {
			if(prop.checked) $(elem).attr('checked', 'checked');
		}


		//Set element id
		$(elem).attr('id', prop.id);

		//If the field is readonly
		if(prop.type == 'readonly') {
			$(elem).attr('readonly', 'readonly');
			$(elem).attr('tabindex', -1);
			$(elem).addClass('readonlyField');
		}

		//MaxLength attribute
		if(!empty(prop.size)) $(elem).attr('maxlength', prop.size);

		//If is set class, add it
		if(prop.classes != '') $(elem).addClass(prop.classes);

		//If hidden don't have class
		if(prop.type == 'hidden') $(elem).removeClass();

		//Set others attributes
		var alreadyNamed = false;
		for(n in prop.atts) {
			$(elem).attr(n, prop.atts[n]);
			if(n == 'name') alreadyNamed = true;
		}
		if(!empty(prop.name)) $(elem).attr('name', prop.name);

		//If name not defined in others attribs, name = id;
		if((empty(prop.name)) && (!alreadyNamed)) $(elem).attr('name', prop.id);

		//Browser autocomplete disabled by default, or user configuration
		if(!empty(prop.autocomplete)) autocomplete = prop.autocomplete; //Browser autocomplete tool
		$(elem).attr('autocomplete', autocomplete);

		//Field value if no empty
		if(prop.value) $(elem).val(prop.value);

		//Put hint into field
		if(!empty(prop.hint)) {
			$(elem).attr('alt', prop.hint);
			if(empty(prop.value)) {
				$(elem).addClass('hintActive');
				setTimeout(function() { $(elem).val(prop.hint); }, 100);
			}
			$(elem).focus(function() {
				if($(this).val() == $(this).attr('alt')) {
					$(this).removeClass('hintActive');
					$(this).val('');
				}
			});
			$(elem).blur(function() {
				if(empty($(this).val())) {
					$(this).addClass('hintActive');
					$(this).val($(this).attr('alt'));
				}
			});
		}

		//Validate functions
		if(prop.require)   $(elem).addClass('requiredField');
		if(prop.minsize)   $(elem).addClass('minSize='+ prop.minsize);
		if(prop.maxsize)   $(elem).addClass('maxSize='+ prop.maxsize);
		if(prop.exactsize) $(elem).addClass('exactSize='+ prop.exactsize);
		if(prop.regex)     regexRules[prop.id] = prop.regex;

		$(elem).blur(function() {
			$(this).removeClass('dataFieldError');

			if($(this).is(':visible')) {

				var allow = true;
				var allclass = $(this).attr('class');
				var temp = '';

				//require
				if(allclass.indexOf('requiredField') > -1) {
					if(empty(trim(this.value))) $(this).addClass('dataFieldError');
				}
				//minsize
				if((allclass.indexOf('minSize') > -1) && (allow)) {
					temp = /minSize=([0-9]+)/.exec(allclass)[1];
					if((this.value.length < temp) && (this.value.length != 0)) allow = false;
				}
				//maxsize
				if((allclass.indexOf('maxSize') > -1) && (allow)) {
					temp = /maxSize=([0-9]+)/.exec(allclass)[1];
					if(this.value.length > temp) allow = false;
				}
				//exactsize
				if((allclass.indexOf('exactSize') > -1) && (allow)) {
					if(this.value.length != 0) {
						temp = /exactSize=([0-9,]+)/.exec(allclass)[1].split(',');

						allow = false;
						for(s in temp) {
							if(this.value.length == temp[s]) {
								allow = true;
								break;
							}
						}
					}
				}
				//regex
				if((!empty(regexRules[$(this).attr('id')])) && (allow)) {
					temp = regexRules[$(this).attr('id')];
					var re = new RegExp(temp, 'gi');
					allow = re.test(this.value);
				}


				if(!allow) $(this).fieldErrorAlert(true);
			}
		});
		/**/

		//Set especial attributes
		$(elem).setEspecialAttributes(prop.type);

		//Status message
		if(!empty(prop.status)) statusMsgs[prop.id] = prop.status;
		$(elem).focus(function() {
			if(!empty(statusMsgs[$(this).attr('id')])) status(statusMsgs[$(this).attr('id')]);
			if((!empty(alertMsgs[$(this).attr('id')])) && ($(this).hasClass('dataFieldError'))) {
				$(this).hint(alertMsgs[$(this).attr('id')]);
			}
		});
		$(elem).blur(function () {
			hideStatus();
			$(this).hideHint();
		});

		//Autocomplete/Tags field properties
		if((prop.type == 'autocomplete') || (prop.type == 'tags')) {
			var acWidth = (!empty(prop.width)) ? prop.width : 300;

			acOnSel[prop.id] = prop.onselect;

			//If elem has hidden field, create and append it
			if(!empty(prop.hidden)) {
				var hid = document.createElement('input');
				$(hid).attr('type', 'hidden');
				$(hid).attr('name', prop.hidden);
				$(hid).attr('id', prop.hidden);
				$(hid).appendTo($('#'+ prop.id));
				acHidVal[prop.id] = prop.hidden;
			}

			//Autocomplete properties
			if(prop.type == 'autocomplete') {
				$(elem).autocomplete({
					source: prop.action,
					delay: 300,
					minLength: 2,
					open: function() {
						if(acHidVal[$(this).attr('id')]) $('#'+ acHidVal[$(this).attr('id')]).attr('value', 0);
					},
					select: function(event, ui) {
						if(acHidVal[$(this).attr('id')]) $('#'+ acHidVal[$(this).attr('id')]).attr('value', (ui.item) ? ui.item.id : 0);
						if((ui.item) && (acOnSel[$(this).attr('id')])) setTimeout(acOnSel[$(this).attr('id')] +'('+ ui.item.id +')', 50);
						$(elem).trigger('blur');
					}
				});
			}

			//Tags properties
			else if(prop.type == 'tags') {
				$(elem).autocomplete(prop.action, {
					minChars: 2,
					delay: 200,
					width: acWidth,
					matchContains: 'word',
					multiple: true,
					multipleSeparator: ', ',
					autoFill: false
				});
			}
		}

		if((!empty($('#'+ prop.id).html())) && (empty(prop.label))) prop.label = $('#'+ prop.id).html();

		//Change the recipient ID
		$('#'+ prop.id).attr('id', $('#'+ prop.id).attr('id') +'_rcpt');

		//Creating the label element and put into recipient element
		if(!empty(prop.label)) {
			var labelem = document.createElement('label');
			$(labelem).attr('for', prop.id);
			$(labelem).html(' '+ prop.label);
			$('#'+ prop.id +'_rcpt').html(labelem);
		}

		//Put created element into td, div..., with the id
		if((!empty(prop.label)) && ((prop.type == 'check') || (prop.type == 'checkbox') || (prop.type == 'radio'))) {
			$(labelem).prepend(elem);
		} else {
			$('#'+ prop.id +'_rcpt').append(elem);
			if(!empty(sugbox)) {
				$('#'+ prop.id +'_rcpt').append(sugbox);
				sugbox = '';
				$('#'+ prop.id).focus(function() { $('#'+ $(this).attr('id') +'SuggestionBox').fadeIn() });
				$('#'+ prop.id).blur(function() { $('#'+ $(this).attr('id') +'SuggestionBox').fadeOut() });
			}
		}

		//If set focus attribute, set focus to field on show
		if(prop.focus) $('#'+ prop.id).focus();
	}
}



//Especial attributes to Fields class
jQuery.fn.setEspecialAttributes = function(x) {
	if((x == 'integer') || (x == 'integer-')) {
		if(x == 'integer') var limits = '0-9'; //Only positive numbers
		else var limits = '0-9-'; //Positive and negative numbers

		this.limitchars(limits);
	}
	else if((x == 'money') || (x == 'money-')) {
		if(x == 'money') var limits = '0-9,'; //Only positive numbers
		else var limits = '0-9-,'; //Positive and negative numbers

		this.keyup(function() {
			dotcomma(this);
		});
		this.limitchars(limits);
	}
	else if(x == 'date') {
		this.attr('maxlength', '10');
		this.blur(function() {
			hifenbar(this);
			cleanchars(this, '0-9/');
			completedate(this);
			if(!is_date(this.value)) $(this).fieldErrorAlert(true);
		});


		this.datepicker({
			changeMonth: true,
			changeYear: true,
			dateFormat: 'dd/mm/yy',
			dayNamesMin: ['Do', 'Se', 'Te', 'Qa', 'Qi', 'Se', 'Sa'],
			monthNamesShort: ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'],
			onSelect: function() { $(this).removeClass('hintActive'); }
		});
	}
	else if(x == 'login') {
		this.limitchars('A-Z0-9');
	}
	else if((x == 'cpf') || (x == 'cnpj') || (x == 'cpfcnpj') || (x == 'cnpjcpf')) {
		this.limitchars('0-9');
		this.blur(function() {
			if(x == 'cpf') {
				if(!checkcpf(this.value)) {
					if(window.onError) onError($(this).attr('id'));
					$(this).fieldErrorAlert(true);
				}
				else {
					if(window.onOk) onOk($(this).attr('id'));
				}
			} else if(x == 'cnpj') {
				if(!checkcnpj(this.value)) {
					if(window.onError) onError($(this).attr('id'));
					$(this).fieldErrorAlert(true);
				}
				else {
					if(window.onOk) onOk($(this).attr('id'));
				}
			} else {
				if(!checkcpfcnpj(this.value)) {
					if(window.onError) onError($(this).attr('id'));
					$(this).fieldErrorAlert(true);
				}
				else {
					if(window.onOk) onOk($(this).attr('id'));
				}
			}
		});
	}
	else if(x == 'email') {
		this.limitchars('0-9A-Z_.@-');
		this.blur(function() {
			if(!is_mail(this.value)) {
				if(window.onError) onError($(this).attr('id'));
				$(this).fieldErrorAlert(true);
			} else {
				if(window.onOk) onOk($(this).attr('id'));
			}
		});
	}
}



jQuery.fn.fieldErrorAlert = function(x) {
	if(x) {
		if(empty(this.val())) {
			if(this.attr('class').indexOf('requiredField') > -1) this.addClass('dataFieldError');
			else this.removeClass('dataFieldError');
		} else {
			this.addClass('dataFieldError');
		}
	}
	else this.removeClass('dataFieldError');
}



/*UPLOAD FILE*/
function doUploadForm(x, ajaxFile, callback) {
	if(empty(ajaxFile)) ajaxFile = 'ajax.php';
	var iframe = $('<iframe name="uploadFormIframe'+ x.attr('id') +'" id="uploadFormIframe'+ x.attr('id') +'" src="about:blank" style="display: none;"></iframe>').appendTo(document.body);
	$("#uploadFormIframe"+ x.attr('id')).load(function() {
		var resp = $('#uploadFormIframe'+ x.attr('id')).contents().find("body").html();
		if((!empty(callback)) && (!empty(resp))) setTimeout(callback +"('"+ resp +"')", 0);
	});

	x.attr('method', 'post');
	x.attr('action', ajaxFile);
	x.attr('target', 'uploadFormIframe'+ x.attr('id'));
	x.addClass('allowSubmit');
}





/*TABLE LIST*/
function gridList(destiny) {
	this.header       = new Array();
	this.body         = new Array();
	this.destiny      = destiny;
	this.actual_page  = 1;
	this.total_page   = 1;
	this.reg_per_page = 15;
	this.total_reg    = 0;
	this.use_checkbox = true;
	this.sortable     = false;
	this.sortable_function = '';
	this.filter_descr = '';
	this.list_function= 'list';
	this.source_file  = 'ajax.php';
	this.reg_ppage_list = { 10:'10 registros por página', 15:'15 registros por página', 25:'25 registros por página', 50:'50 registros por página', 100:'100 registros por página', 500:'500 registros por página' };
	this.title_head_check    = 'Inverter seleção';
	this.title_head_sort     = 'Clique para ordenar';
	this.total_reg_label     = '%d registros encontrados';
	this.first_page_label    = 'Primeira';
	this.previous_page_label = 'Anterior';
	this.next_page_label     = 'Próxima';
	this.last_page_label     = 'Última';
	this.none_records_found  = 'Nenhum registro encontrado';

	this.getListInfo = function(root) {
		this.actual_page  = root.find('actual_page').text();
		this.reg_per_page = root.find('reg_per_page').text();
		this.total_reg    = root.find('total_reg').text();
		this.filter_descr = root.find('filter_descr').text();
	}

	this.write = function(destiny) {
		if(!empty(destiny)) this.destiny = destiny;

		var countcols = 0;
		var resp = new Array();
		var r = 0;
		this.total_page = Math.ceil( this.total_reg / this.reg_per_page );
		if(this.total_page == 0) this.total_page = 1;

		resp[r++] = '<table>';

		if(!empty(this.header)) {
			resp[r++] = '<thead><tr>';

			if(this.use_checkbox) {
				countcols++;
				resp[r++] = '<td><input type="checkbox" title="'+ this.title_head_check +'" class="idselall"></td>';
			}

			for(k in this.header) {
				countcols++;
				this.header[k].replace('\=', '{temporaryequalsignal}');
				if(this.header[k].indexOf('=') > -1) {
					var temp = this.header[k].split('=');
					resp[r++] = '<td><span onclick="changeOrder(\''+ temp[1] +'\', \''+ this.source_file +'\', \''+ this.list_function +'\')" title="'+ this.title_head_sort +'" style="float: left;" class="ui-icon ui-icon-triangle-2-n-s"></span>'+ temp[0] +'</td>';
				} else {
					resp[r++] = '<td>'+ this.header[k] +'</td>';
				}
//				this.header[k].replace('{temporaryequalsignal}', '=');
			}
			resp[r++] = '</tr></thead>';
		}

		var sortid = (this.sortable) ? 'id="sortable"' : '';

		resp[r++] = '<tbody '+ sortid +'>';
		if(this.body.length == 0) {
			resp[r++] = '<tr><td colspan="'+ countcols +'"><i>'+ this.none_records_found +'</i></td></tr>';
		} else {
			for(k in this.body) {
				var line = this.body[k];
				resp[r++] = '<tr>';

				if(this.use_checkbox) {
					resp[r++] = '<td><input type="checkbox" value="'+ line[0] +'" name="cods[]"></td>';
				}

				for(j in line) {
					if((this.use_checkbox == false) || (j > 0)) {
						resp[r++] = '<td>'+ line[j] +'</td>';
					}
				}

				resp[r++] = '</tr>';
			}
		}
		resp[r++] = '</tbody><tfoot class="paginateFooter ui-widget-footer"><tr><td colspan="'+ countcols +'">';

		resp[r++] = '<span class="onRight"><select class="texto ui-widget-content ui-corner-all" style="width: 140px;" name="selectNumberRegisters" onchange="changeNumberRegisters(this, \''+ this.source_file +'\', \''+ this.list_function +'\')">';

		for(k in this.reg_ppage_list) {
			if(k == this.reg_per_page) var selit = 'selected="selected"'; else selit = '';
			resp[r++] = '<option value="'+ k +'" '+ selit +'>'+ this.reg_ppage_list[k] +'</option>';
		}

		resp[r++] = '</select></span>';

		resp[r++] = '<div class="descrList">'+ this.filter_descr +' '+ sprintf(this.total_reg_label, parseInt(this.total_reg)) +'.</div>';

		resp[r++] = '<div class="onLeft">';

		if(this.actual_page > 1) {
			resp[r++] = '<a href="javascript:goToPage(1, \''+ this.source_file +'\', \''+ this.list_function +'\')">'+ this.first_page_label +'</a> | ';
			resp[r++] = '<a href="javascript:goToPage('+ (parseInt(this.actual_page)-1) +', \''+ this.source_file +'\', \''+ this.list_function +'\')">'+ this.previous_page_label +'</a> | ';
		} else {
			resp[r++] = this.first_page_label +' | ';
			resp[r++] = this.previous_page_label +' | ';
		}

		resp[r++] = this.actual_page +'/'+ this.total_page;

		if(this.actual_page < this.total_page) {
			resp[r++] = ' | <a href="javascript:goToPage('+ (parseInt(this.actual_page)+1) +', \''+ this.source_file +'\', \''+ this.list_function +'\')">'+ this.next_page_label +'</a>';
			resp[r++] = ' | <a href="javascript:goToPage('+ this.total_page +', \''+ this.source_file +'\', \''+ this.list_function +'\')">'+ this.last_page_label +'</a>';
		} else {
			resp[r++] = ' | '+ this.next_page_label;
			resp[r++] = ' | '+ this.last_page_label;
		}

		resp[r++] = '</div>';

		resp[r++] = '</td></tr></tfoot></table>';
		$('#'+ this.destiny).html(resp.join(''));
//		$('#'+ this.destiny).addClass('tableList');

//		formatTableList($('#'+ this.destiny));
		formatTableList($('.tableList'));

		var listobj = $('#'+ this.destiny +' table');

		//if click at a link inside the list, select only that line
		$(listobj).find('a').click(function(event) { $(listobj).find('tbody tr.selected').trigger('click') });

		/*if click idselall, (de)select all body lines*/
		$(listobj).find('.idselall').click(function(event) { $(listobj).find('tbody tr').trigger('click') });

		/*on click, select all the line (tr)*/
		$('#'+ this.destiny +' table tbody tr').click(function(event) {
			if($(this).hasClass('selected')) {
				$(this).removeClass('selected');
				$(this).children('td:first').children('input:checkbox').removeAttr('checked');
			} else {
				if($(this).children('td:first').children('input:checkbox').attr('name') != undefined) {
					$(this).addClass('selected');
					$(this).children('td:first').children('input:checkbox').attr('checked', 'checked');
				}
			}
		});

		/*allow sort elements*/
		if(this.sortable) {
			if(empty(this.sortable_function)) $("#sortable").sortable();
			else $("#sortable").sortable({ update:function(event, ui) { setTimeout(this.sortable_function, 50); } });
			$("#sortable").disableSelection();
		}

	}
}



/*CLASS - ACTION BUTTONS*/
var actionButtonSubOpen;
function actionButtons(x) {
	for(k in x) {

		var elem = document.createElement('div');

		$(elem).addClass('actionButton ui-corner-all');
		if(!empty(x[k].name))    $(elem).append(x[k].name);
		if(!empty(x[k].id))      $(elem).attr('id', x[k].id);
		if(!empty(x[k].classes)) $(elem).addClass(x[k].classes);
		if((!empty(x[k].icon)) && (empty(x[k].submenu))) $(elem).prepend('<span class="ui-icon '+ x[k].icon +'"></span>');
		if(!empty(x[k].click))   $(elem).click(x[k].click);
		$('#navigationBar').append(elem);

		if(!empty(x[k].submenu)) {
			var y = x[k].submenu;

			$(elem).prepend('<span class="ui-icon ui-icon-triangle-1-s"></span>');
			if(empty(x[k].id)) $(elem).attr('id', 'subid'+k);

			var subm = document.createElement('span');
			$(subm).addClass('actionButtonSubBox actionButtonSub'+ $(elem).attr('id'));

			$(elem).mouseover(function() {
				actionButtonSubOpen = true;
				$('.actionButtonSub'+ $(elem).attr('id')).fadeIn();
			});
			$(elem).mouseout(function() {
				actionButtonSubOpen = false;
				setTimeout(function() { if(!actionButtonSubOpen) $('.actionButtonSub'+ $(elem).attr('id')).fadeOut(); }, 500);
			});
			$(subm).mouseover(function() {
				actionButtonSubOpen = true;
				$(subm).fadeIn();
			});
			$(subm).mouseout(function() {
				actionButtonSubOpen = false;
				setTimeout(function() { if(!actionButtonSubOpen) $(subm).fadeOut(); }, 500);
			});


			for(s in y) {
				var selem = document.createElement('div');

				$(selem).addClass('actionButton');

				if(!empty(y[s].name))    $(selem).append(y[s].name);
				if(!empty(y[s].id))      $(selem).attr('id', y[s].id);
				if(!empty(y[s].classes)) $(selem).addClass(y[s].classes);
				if(!empty(y[s].icon))    $(selem).prepend('<span class="ui-icon '+ y[s].icon +'"></span>');
				if(!empty(y[s].click))   $(selem).click(y[s].click);

				$(subm).append($(selem));
			}

			$('#navigationBar').append(subm);

			nowleft = $(elem).offset();
			nowleft.left = $(elem).offset().left;
			$(subm).offset(nowleft);

		}
	}
}



/*WARNING MSG POPUP*/
var timeoutKeepWarning;
function msg(x) { warning(x); }
function hideWarning(x) {
	if(timeoutKeepWarning == 0) $('.warningMsgBox').remove();
}
function warning(x) {
	timeoutKeepWarning = 1;
	setTimeout(function() { timeoutKeepWarning = 0; }, 300);

	var vartop = ($('.warningMsgBox').length+1)*60;

	var warningcss = {
		position:	'fixed',
		width:  	300,
		top:		vartop,
		border: 	'none',
		padding:	15,
		textAlign:	'center',
		opacity:	0.6,
		cursor: 	'pointer',
		zIndex: 	'9999',
		color:		'#fff',
		backgroundColor: '#000',
		'-webkit-border-radius': '10px',
		'-moz-border-radius':	 '10px'
	}

	var elem = document.createElement('div');

	for(k in warningcss) $(elem).css(k, warningcss[k]);
	$(elem).html(x)
				.attr('id', 'warningMsgBox')
				.addClass('warningMsgBox')
				.click(hideWarning)
				.mouseover(function() { $(elem).animate({ opacity: 1 }, 'slow') })
				.mouseleave(function() { $(elem).animate({ opacity: 0.6 }, 'slow') });
	$('body').prepend($(elem));
	$(elem).center(true, false, true);
	$('#warningMsgBox').show('bounce', null, 150);
	setTimeout(function() { $(elem).animate({ opacity: 0.6 }, 'slow') }, 300);
}





/*STATUS BAR*/
var timeoutHideStatus;
var statusMsgDisabled = false;
function hideStatus() {
	if(!statusMsgDisabled) timeoutHideStatus = setTimeout(function() { $('#statusBar:visible').hide('slide', { direction: "down" } ); }, 300);
}
function status(x, fast) {

	clearTimeout(timeoutHideStatus);

	if(empty(x)) hideStatus();
	else {

		if($('#statusBar').length) { //If warning box already exists
			if($("#statusBar").is(":hidden")) {
				if(fast) $('#statusBar').html('<div>'+ x +'</div>').show();
				else $('#statusBar').html('<div>'+ x +'</div>').show('slide', { direction: "down" } );
			} else {
				$('#statusBar div').html(x);
			}

		} else { //Else, create it

			var cssstyle = {
				zIndex: 9999
			};

			var st = document.createElement('div');
			$(st).css(cssstyle)
				.attr('id', 'statusBar')
				.html('<div>'+ x +'</div>');
			$(document.body).append($(st));
			if(fast) $('#statusBar').show();
			else $('#statusBar').show('slide', { direction: "down" } );
		}

	}
}


/*HINT BALLOON*/
jQuery.fn.hint = function(x) {
	$(this).bt(x, { trigger: ['none', 'blur'] });
	$(this).btOn();
}

jQuery.fn.hideHint = function() {
	$(this).btOff();
}
