/*************************\INIT GLOBAL VARIABLE/*************************/
VARI   = new Array();
FILTER = new Array();
var disableWaitBox = false;


/*************************\ON DOCUMENT READY/*************************/
$(document).ready(function() {
	if(!disableWaitBox) {
		$(this).ajaxStart(function() {
			status('<span>Carregando... por favor, aguarde.</span>', true);
		});
		$(this).ajaxStop(function() {
			if($('.tableList').length) formatTableList();
			if($('#statusBar').html().toLowerCase() == '<div><span>carregando... por favor, aguarde.</span></div>') status('');
		});
		$(window).unload(function() {
			status('<span>Carregando... por favor, aguarde.</span>', true);
		});
	}

	$(this).click(hideWarning);

	$('.dialog').dialog({
		open: function(event, ui) {
			$("input:text:visible:first").focus();
			window.setTimeout(function() {
				jQuery(document).unbind('mousedown.dialog-overlay').unbind('mouseup.dialog-overlay');
			}, 100);
			$('.buttonDisabled').removeAttr('disabled');
			$('.buttonDisabled').removeClass('buttonDisabled');
		},
		bgiframe: true,
		autoOpen: false,
		width: 780,
		modal: true
	});

	if($('.panel').length) formatPanel();
	if($('.tableList').length) formatTableList();

	setTimeout(function() {
		$('.ui-dialog-buttonpane button').click(function() {
			$(this).attr('disabled', 'disabled');
			$(this).addClass('buttonDisabled');
			setTimeout(function() {
				$('.buttonDisabled').removeAttr('disabled');
				$('.buttonDisabled').removeClass('buttonDisabled');
			}, 4000);
		});
	}, 1000);


	/*keep the connection alive - FIX THIS*/
	setInterval(function() {
		$.get('wakeup.php');
	}, 1000 * 60 * 10);


	/*on submit, stop the submit and click the default button*/
	$('form').submit(function(event) {
		if(!$(this).hasClass('allowSubmit')) {
			event.preventDefault();
			$(this).find('.defaultButton').trigger('click');
		}
	});
});



/*************************\FORMAT FUNCTIONS/*************************/

jQuery.fn.limitchars = function(y) {
	this.blur(function(e) {
		cleanchars(this, y);
	});
}

function cleanchars(z, y) {
    x = z.value;
    y = y.toUpperCase();
    y = y.replace('A-Z', 'ABCDEFGHIJKLMNOPQRSTUVWXYZ');
    y = y.replace('0-9', '1234567890');
    var chars_permitidos = y;
    var resposta = '';
    for(ii=0; ii<x.length; ii++) {
        if ((chars_permitidos.indexOf(x.charAt(ii).toUpperCase()) > -1) || (chars_permitidos.indexOf(x.charAt(ii).toLowerCase()) > -1)) {
            resposta += x.charAt(ii);
        }
    }
    z.value = resposta;
}

function dotcomma(x) {
	var conta = 0;
	while(x.value.indexOf(".") != -1) {	x.value = x.value.replace(".", ","); conta++; }
	for(ii=0; ii<conta; ii++) {
		if(x.value.indexOf(",", x.value.indexOf(",")+1) != -1) { x.value = x.value.replace(",", ""); }
	}
}

function completedate(z) {
	x = z.value;
	if ((x.length == 1) || (x.length == 2)) {
		hoje = new Date();
		var mes = (hoje.getMonth()+1);
		if(mes < 10) mes = "0" + mes;
		z.value = x + "/" + mes + "/" + hoje.getFullYear();
	} else if((x.length == 4) && (x.indexOf("/") == -1)) {
		hoje = new Date();
		var dia = x.substring(0, 2);
		var mes = x.substring(2, 4);
		z.value = dia + "/" + mes + "/" + hoje.getFullYear();
	} else if((x.length >= 6) && (x.indexOf("/") == -1)) {
		var dia = x.substring(0, 2);
		var mes = x.substring(2, 4);
		var ano = x.substring(4, x.length);
		z.value = dia + "/" + mes + "/" + ano;
	}
}

function hifenbar(x) {
	while(x.value.indexOf("-") != -1) { x.value = x.value.replace("-", "/"); }
}

function dot(valor) {
	valor = valor.replace(".","");
	valor = parseFloat(valor.replace(",","."));
	if(!isNaN(valor)) return valor;
	else return 0.00;
}

function comma(num) {
	negativo = false;
	if (num < 0) {
		num = num * (-1);
		negativo = true;
	}
	num = num.toString().replace(/\$|\,/g,'');

	if(isNaN(num)) num = "0";
	cents = Math.floor((num*100+0.5)%100);
	num = Math.floor((num*100+0.5)/100).toString();
	if(cents < 10) cents = "0" + cents;
	for (var i = 0; i < Math.floor((num.length-(1+i))/3); i++)
		num = num.substring(0,num.length-(4*i+3))+num.substring(num.length-(4*i+3));

	if (!negativo) return ('' + num + ',' + cents);
	else return ('-' + num + ',' + cents);
}

function trim(str){
	return str.replace(/^\s+|\s+$/g,"");
}




/*************************\CHECK FUNCTIONS/*************************/

function validateFormFields(x, debug) {
	var error = new Array();
	onefielderror = 'Há %d campo que você precisa preencher corretamente antes de clicar no botão.';
	morefieldserror = 'Há %d campos que você precisa preencher corretamente antes de clicar no botão.';

	statusMsgDisabled = true;
	x.find(':input').trigger('blur');
	statusMsgDisabled = false;

	var count = x.find('.dataFieldError:visible').length;

	if(count) {
		if(!empty(debug)) x.find('.dataFieldError:visible').each(function() { warning($(this).attr('id')); } );
		if(count == 1) error.push(sprintf(onefielderror, count));
		else error.push(sprintf(morefieldserror, count));
		$('.dataFieldError:visible:first').focus();
		$('html, body').animate({scrollTop: $(window).scrollTop()-20}, 'slow');
	}

	if(!empty(error)) {
		warning(error.join('<br/>'));
		return false;
	}

	return true;
}


/*IS EMPTY?*/
function empty(mixed_var) {
	var key;
	if(mixed_var === '' || mixed_var === 0 || mixed_var === '0' || mixed_var === null || mixed_var === false || typeof mixed_var === 'undefined' || mixed_var === '0,00' || mixed_var === '0,000' || mixed_var === '0,0000' || mixed_var === '0,00000') return true;

	if(typeof mixed_var == 'object') {
		for(key in mixed_var) return false;
		return true;
	}
 	return false;
}


function checkcpfcnpj(x) {
	if (x.length < 13) return checkcpf(x);
	else return checkcnpj(x);
}

function checkcpf(x) {
	var CPF = x;
	if(CPF=='') var faiado = true;
	else {
		if(CPF == "00000000000" || CPF == "11111111111" ||
			CPF == "22222222222" ||	CPF == "33333333333" || CPF == "44444444444" ||
			CPF == "55555555555" || CPF == "66666666666" || CPF == "77777777777" ||
			CPF == "88888888888" || CPF == "99999999999" || CPF == "00000000000") {
			var faiado = true;
		}
		soma = 0;
		for(i=0; i<9; i++) soma += parseInt(CPF.charAt(i)) * (10-i);
		resto = 11 - (soma % 11);
		if (resto == 10 || resto == 11) resto = 0;
		if (resto != parseInt(CPF.charAt(9))) var faiado = true;
		soma = 0;
		for(i=0; i<10; i++) soma += parseInt(CPF.charAt(i)) * (11-i);
		resto = 11-(soma % 11);
		if (resto == 10 || resto == 11) resto = 0;
		if (resto != parseInt(CPF.charAt(10))) var faiado = true;
	}
	if(faiado) return false;
	else return true;
}

function checkcnpj(x) {
	var s = x;
	var i;
	if(s.length == "15") s = s.substr(1,14);
	if(s != '00000000000000') {
		var c = s.substr(0,12);
		var dv = s.substr(12,2);
		var d1 = 0;
		for (i = 0; i < 12; i++) d1 += c.charAt(11-i)*(2+(i % 8));
		if (d1 == 0) var faiado = true;
		d1 = 11 - (d1 % 11);
		if (d1 > 9) d1 = 0;
		if(dv.charAt(0) != d1) var faiado = true;
		d1 *= 2;
		for (i = 0; i < 12; i++) d1 += c.charAt(11-i)*(2+((i+1) % 8));
		d1 = 11 - (d1 % 11);
		if (d1 > 9) d1 = 0;
		if (dv.charAt(1) != d1) var faiado = true;
	}
	if(faiado) return false;
	else return true;
}


/*IS_MAIL*/
function is_mail(x) {
	if ((x.indexOf('@') < 1) ||
		(x.indexOf('.') < 1) ||
		(x.indexOf('@.') > -1) ||
		(x.indexOf('.@') > -1) ||
		(x.indexOf(' ') > -1) ||
		(x.indexOf(',') > -1)) {
		return false;
	} else {
		return true;
	}
}

/*IS_DATE*/
function is_date(x) {
	if(x != '') {
		if(x.indexOf('/') == -1) return false;
		else if (x.indexOf('/') == x.lastIndexOf('/')) return false;
		else {
			day = x.substring(0,x.indexOf('/'));
			month = x.substring(x.indexOf('/')+1, x.lastIndexOf('/'));
			year = x.substring(x.lastIndexOf('/')+1, x.length);
			if (((year%4!=0) && (month==2) && (day==29)) || (month>12) || (month < 1) || (year.length == 3) || (((month==4) || (month==6) || (month==9) || (month==11)) && (day > 30)) || ((month==2) && (day>29)) || (((month==1) || (month==3) || (month==5) || (month==7) || (month==8) || (month==10) || (month==12)) && (day > 31))) return false;
		}
	}
	return true;
}





/***********************\CHECKBOX FUNCTIONS/***********************/

function countsel(form) {
	var selected = 0;
	var x = $('#'+ form);
	for (var ii=0; ii<x.elements.length; ii++) {
		if((x.elements[ii].type == 'checkbox') && (x.elements[ii].id != 'idselall')) {

			if(x.elements[ii].checked) selected++;
		}
	}
	return selected;
}

function firstsel(list) {
	var selectbeforeclick = 'Selecione um registro antes de clicar no botão';

	var resp = $('#'+ list).find('tbody tr.selected:first').children('td:first').children('input:checkbox').attr('value');
	if(empty(resp)) {
		warning(selectbeforeclick);
		return false;
	} else {
		var temp = $('#'+ list).find('tbody tr.selected:first');
		$('#'+ list).find('tbody tr.selected').trigger('click');
		temp.trigger('click');
	}

	return resp;
}

function allsell(list) {
	var selectbeforeclick = 'Selecione um ou mais registros antes de clicar no botão';

	resp = new Array();
	$('#'+ list).find('tbody tr.selected').each(function() {
		if(!empty($(this).children('td:first').children('input:checkbox').attr('value'))) {
			resp.push($(this).children('td:first').children('input:checkbox').attr('value'));
		}
	});

	if(empty(resp)) {
		warning(selectbeforeclick);
		return '';
	} else {
		return resp;
	}
}



/*************************\POPUP FUNCTIONS/*************************/
function popup(url, w, h) {
	if(isNaN(w)) var w=750;
	if(isNaN(h)) var h=600;
	var y = screen.availHeight/2-h/2;
	var x = screen.availWidth/2-w/2;
	var aleatorio = Math.random();
	aleatorio = "a" + aleatorio;
	var ultimo = aleatorio.charAt(aleatorio.length-1);
	ultimo = ultimo + aleatorio.charAt(aleatorio.length-2);
	telapopup = window.open(url, 'tela'+ ultimo, 'width='+ w +', height='+ h +', left='+ x +', top='+ y +', scrollbars=1, resizable=1');
	if(!telapopup) alert('Atenção, o seu navegador está bloqueando as janelas popups, é necessário que você habilite a visualização de Popups.');
}

function popupform(formulario, w, h) {
	if(!window.focus) return true;

	if(isNaN(w)) var w=750;
	if(isNaN(h)) var h=600;
	var y = screen.availHeight/2-h/2;
	var x = screen.availWidth/2-w/2;
	var aleatorio = Math.random();
	aleatorio = "a" + aleatorio;
	var ultimo = aleatorio.charAt(aleatorio.length-1);
	ultimo = ultimo + aleatorio.charAt(aleatorio.length-2);
	window.open('', 'tela'+ ultimo, 'width='+ w +', height='+ h +', left='+ x +', top='+ y +', scrollbars=1, resizable=1');

	formulario.target = 'tela'+ ultimo;
	return true;
}



/*************************\ACTIONS FUNCTIONS/*************************/

function enabled(field, y) {
	if(y) {
		$('#'+ field).removeAttr('disabled');
		$('#'+ field).removeClass('disabledField');
	} else {
		$('#'+ field).attr('disabled', 'disabled');
		$('#'+ field).addClass('disabledField');
	}
}


function hide(x, callback, fast) {
	var y = x.split(',');
	for(k in y) {
		if(empty(fast)) $('#'+ trim(y[k])).fadeOut();
		else $('#'+ trim(y[k])).hide();
	}
	if(!empty(callback)) setTimeout(callback, 50);
}

function show(x, callback, fast) {
	var y = x.split(',');
	for(k in y) {
		if(empty(fast)) $('#'+ trim(y[k])).fadeIn();
		else $('#'+ trim(y[k])).show();
	}
	if(!empty(callback)) setTimeout(callback, 50);
}

function desactivate(x, callback) {
	var y = x.split(',');
	for(k in y) {
		$('#'+ trim(y[k])).attr('disabled', 'disabled');
		if(!empty(callback)) setTimeout(callback, 50);
	}
}

function activate(x, callback) {
	var y = x.split(',');
	for(k in y) {
		$('#'+ trim(y[k])).removeAttr('disabled');
		if(!empty(callback)) setTimeout(callback, 50);
	}
}


/*************************\FILTER AND PAGINATION/*************************/
function goToPage(x, source, command) {
	$.post(source, { nowpage:x }, function() {
		setTimeout(command +'()', 1);
	});
}
function changeOrder(x, source, command) {
	$.post(source, { orderby:x }, function() {
		setTimeout(command +'()', 1);
	});
}
function changeNumberRegisters(elem, source, command) {
	$.post(source, { registers:elem.value }, function() {
		setTimeout(command +'()', 1);
	});
}
function doFilter(event, source, command) {
	event.preventDefault();
	$.post(source, 'changefilter=true&'+ $('#filterBox').serialize(), function() {
		setTimeout(command +'()', 1);
	});
}
function clearFilter(event, source, command) {
	event.preventDefault();
	$.post(source, 'clearfilter=true', function() {
		setTimeout(command +'()', 1);
	});
}



/*************************\DESIGN FUNCTIONS/*************************/

function formatPanel(x) {
	if(!empty(x)) var obj = x;
	else var obj = $('.panel');

	obj.each(function(index) {
		$(this).addClass('ui-widget-content ui-corner-all');

		if(!empty($(this).attr('title'))) {
			$(this).prepend('<h3 class="ui-widget-header ui-corner-all">'+ $(this).attr('title') +'</h3>');
		}
	});
}

function formatTableList(x) {
	if(empty(x)) {
		$('.tableList').each(function(index) {
			$(this).find('table').addClass('ui-widget ui-widget-content ui-corner-all fullBox');
			$(this).find('thead').addClass('ui-widget-header');
		});
	} else {
		x.find('table').addClass('ui-widget ui-widget-content ui-corner-all fullBox');
		x.find('thead').addClass('ui-widget-header');
	}
}


/*************************\"DEFAULT" FUNCTIONS/*************************/

function defaultSaveButton(form, com, callback) {
	if(validateFormFields(form)) {
		setTimeout(function() {
			$.post('ajax.php', 'com='+ com +'&'+ form.serialize() , function(data) {
				if(data == 1) {
					msg('Dados gravados com sucesso.');
					setTimeout(callback, 0);
					form.dialog('close');
				}
				else {
					warning(data);
				}
			});
		}, 300);
	}
}

/*************************\TUTORIAL FUNCTIONS/*************************/
function initTutorial(x) {
	$.getScript('../_includes/tutorials/'+ x);
	$.post('ajax.php', { startTutorial: x });
}

function stopTutorial() {
	$.post('ajax.php', { stopTutorial: true });
	hideStatus();
}



/*************************\OTHER FUNCTIONS/*************************/
jQuery.fn.center = function(x, y, z) {
	if(x) this.css('left', ( $(window).width() - this.width() ) / 2+$(window).scrollLeft() + 'px');
	if(y) this.css('top', ( $(window).height() - this.height() ) / 2+$(window).scrollTop() + 'px');
	return this;
}


jQuery.fn.tagName = function() {
    return this.get(0).tagName;
}

/*************************\INTERNACIONALIZATION/*************************/



function _(x) {
//	return getLang(x);
}

function getLang(x) {
	alert(x);
	if(empty(i18n[x])) return x;
	return i18n[x];
}

function translate(s) {
	return s.replace(/{\#([^}]+)\}/g, function(a, b) { return getLang(String(b)) });
}