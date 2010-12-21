/*************************\INIT GLOBAL VARIABLE/*************************/
vari   = new Array();
FILTER = new Array();
var disableWaitBox = false;

listaestados = {'':'', 'AC':'AC', 'AL':'AL', 'AM':'AM', 'AP':'AP', 'BA':'BA', 'CE':'CE', 'DF':'DF', 'ES':'ES', 'GO':'GO', 'MA':'MA', 'MG':'MG', 'MS':'MS', 'MT':'MT', 'PA':'PA', 'PB':'PB', 'PE':'PE', 'PI':'PI', 'PR':'PR', 'RJ':'RJ', 'RN':'RN', 'RO':'RO', 'RR':'RR', 'RS':'RS', 'SC':'SC', 'SE':'SE', 'SP':'SP', 'TO':'TO'};


/*************************\ON DOCUMENT READY/*************************/
$(document).ready(function() {
	if(!disableWaitBox) {
		$(this).ajaxStart(function() {
			status('<span>Carregando... por favor, aguarde.</span>', true);
		});
		$(this).ajaxStop(function() {
			if($('.tableList').length) doTableList();
			if($('#statusBar').html().toLowerCase() == '<div><span>carregando... por favor, aguarde.</span></div>') hideStatus();
		});
	}
	$(window).unload(function() {
		status('<span>Carregando... por favor, aguarde.</span>', true);
	});

	$(this).click(hideWarning);

	if(!empty(vari.listName)) {
		refreshDefaultList();
	}

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

	if($('.panel').length) doPanel();
	if($('.tableList').length) doTableList();

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


	/*keep the connection alive*/
	setInterval(function() {
		$.get('../includes/wakeup.php');
	}, 1000 * 60 * 10);

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
		//var key = [e.keyCode||e.which];
		//var keychar = String.fromCharCode(key).toUpperCase();

		//y = y.toUpperCase();
		//y = y.replace('A-Z', 'ABCDEFGHIJKLMNOPQRSTUVWXYZ');
		//y = y.replace('0-9', '1234567890');
		//var allowed = y;

		////Default allow keys - let space at begin and comma at end
		//var defallow = ' 8, 9, 13, 16, 17, 18, 20, 27, 33, 34, 35, 36, 37, 38, 39, 40, 45, 46,';

		//if((allowed.indexOf(keychar) > -1) || (defallow.indexOf(' '+key+',') > -1)) return true;
		//else return false;
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
	var erro = new Array();

	statusMsgDisabled = true;
	x.find(':input').trigger('blur');
	statusMsgDisabled = false;

	var conta = x.find('.dataFieldError:visible').length;

	if(conta) {
		if(!empty(debug)) x.find('.dataFieldError:visible').each(function() { warning($(this).attr('id')); } );
		if(conta == 1) erro.push('Há '+ conta +' campo que você precisa preencher corretamente antes de salvar.');
		else erro.push('Há '+ conta +' campos que você precisa preencher corretamente antes de salvar.');
	}

	if(!empty(erro)) {
		warning(erro.join('<br/>'));
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


/*CHECKMAIL*/
function checkmail(x) {
	if ((x.indexOf("@") < 1) ||
		(x.indexOf(".") < 1) ||
		(x.indexOf("@.") > -1) ||
		(x.indexOf(".@") > -1) ||
		(x.indexOf(" ") > -1) ||
		(x.indexOf(",") > -1)) {
		return false;
	} else {
		return true;
	}
}

/*CHECKDATE*/
function checkdate(x) {
	if(x != "") {
		if (x.indexOf('/') == -1) {
			alert('A data não se encontra no formato correto: dd/mm/aaaa');
			return false;
		}
		else if (x.indexOf('/') == x.lastIndexOf('/')) {
			if (confirm('A data não possui ano, deseja prosseguir?') == true) {
				dia = x.substring(0,x.indexOf('/'));
				mes = x.substring(x.indexOf('/')+1, x.length);
				if ((mes>12) || (mes < 1) || (((mes==4) || (mes==6) || (mes==9) || (mes==11)) && (dia > 30)) || ((mes==2) && (dia>29)) || ((mes==1) || (mes==3) || (mes==5) || (mes==7) || (mes==8) || (mes==10) || (mes==12)) && (dia > 31)) {
					aviso('Data inválida!');
					return false;
				} else return true;
			}
			else return false;
		} else {
			dia = x.substring(0,x.indexOf('/'));
			mes = x.substring(x.indexOf('/')+1, x.lastIndexOf('/'));
			ano = x.substring(x.lastIndexOf('/')+1, x.length);
			if (((ano%4!=0) && (mes==2) && (dia==29)) || (mes>12) || (mes < 1) || (((mes==4) || (mes==6) || (mes==9) || (mes==11)) && (dia > 30)) || ((mes==2) && (dia>29)) || ((mes==1) || (mes==3) || (mes==5) || (mes==7) || (mes==8) || (mes==10) || (mes==12)) && (dia > 31)) {
				aviso('Data inválida!');
				return false;
			} else return true;
		}
	} else return true;
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
	var resp = $('#'+ list).find('tbody tr.selected:first').children('td:first').children('input:checkbox').attr('value');
	if(empty(resp)) {
		warning('Selecione um registro antes de clicar no botão');
		return false;
	} else {
		var temp = $('#'+ list).find('tbody tr.selected:first');
		$('#'+ list).find('tbody tr.selected').trigger('click');
		temp.trigger('click');
	}

	return resp;
}

function allsell(list) {
	resp = new Array();
	$('#'+ list).find('tbody tr.selected').each(function() {
		if(!empty($(this).children('td:first').children('input:checkbox').attr('value'))) {
			resp.push($(this).children('td:first').children('input:checkbox').attr('value'));
		}
	});

	if(empty(resp)) {
		warning('Selecione um registro antes de clicar no botão');
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



/*************************\FILTER AND PAGINATION/*************************/
function goToPage(x) {
	doGridList(vari.listName, 'com='+ vari.listCommand +'&nowpage='+ x);
}
function changeOrder(x) {
	doGridList(vari.listName, 'com='+ vari.listCommand +'&orderby='+ x);
}
function changeRegisters(x) {
	doGridList(vari.listName, 'com='+ vari.listCommand +'&registers='+ x);
}
function doFilter(event) {
	event.preventDefault();
	doGridList(vari.listName, 'com='+ vari.listCommand +'&changefilter=true&'+ $('#filterBox').serialize());
}
function clearFilter(event) {
	event.preventDefault();
	doGridList(vari.listName, 'com='+ vari.listCommand +'&clearfilter=true');
}



/*************************\DESIGN FUNCTIONS/*************************/

function doPanel(x) {
	if(!empty(x)) {
		var obj = x;
	} else {
		var obj = $('.panel')
	}
	obj.each(function(index) {
		$(this).addClass('ui-widget-content ui-corner-all');

		if(!empty($(this).attr('title'))) {
			$(this).prepend('<h3 class="ui-widget-header ui-corner-all">'+ $(this).attr('title') +'</h3>');
		}
	});
}

function doTableList(x) {
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

function defaultCloseButton(form) {
	form.dialog('close');
}

function refreshDefaultList() {
	doGridList(vari.listName, 'com='+ vari.listCommand);
}

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

function defaultDelRegister(com) {
	x = allsell(vari.listName);
	if(!empty(x)) {
		if(confirm('Tem certeza que deseja apagar os cadastros selecionados?')) {
			$.post('ajax.php', 'com='+ com +'&cods='+ x , function(data) {
				if(data == 1) {
					refreshDefaultList();
					msg('Registros apagados com sucesso');
				}
				else {
					warning(data);
				}
			});
		}
	}
}

/*************************\TUTORIAL FUNCTIONS/*************************/
function initTutorial(x) {
	$.getScript('../includes/tutoriais/'+ x);
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


$.fn.tagName = function() {
    return this.get(0).tagName;
}
