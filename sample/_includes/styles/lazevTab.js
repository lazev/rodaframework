/*var lazevTabMenuOpen;
jQuery.fn.lazevTab = function(tabarray) {
	var ii = 0;
	menu = new Array();
	menu[ii++] = '<div class="lazevTabMenu" id="'+ this.attr('id') +'Menu">';
	for(k in tabarray) {
		menu[ii++] = '<a href="'+ tabarray[k]['link'] +'">'+ tabarray[k]['label'] +'</a>';
	}
	menu[ii++] = '</div>';

	elem = menu.join('');

	$(elem).mouseover(function() {
		lazevTabMenuOpen = $(this).attr('id');
		$('#'+ $(this).attr('id')).fadeIn();
	});
	$(elem).mouseout(function() {
		lazevTabMenuOpen = $(this).attr('id');
		setTimeout(function() { if(!lazevTabMenuOpen) $('#'+ $(this).attr('id')).fadeOut(); }, 500);
	});
	this.mouseover(function() {
		lazevTabMenuOpen = $(this).attr('id') +'Menu';
		$('#'+ $(this).attr('id') +'Menu').fadeIn();
	});
	this.mouseout(function() {
		lazevTabMenuOpen = $(this).attr('id') +'Menu';
		setTimeout(function() { if(!lazevTabMenuOpen) $('#'+ $(this).attr('id') +'Menu').fadeOut(); }, 500);
	});


	nowleft = this.offset();
	nowleft.left = this.offset().left;
	$(elem).offset(nowleft);

	$('body').append(elem);
}
*/

var lazevTabMenuOpen;
lazevTabMenuArray = new Array();
jQuery.fn.lazevTab = function(tabarray) {

	this.parent().addClass('withLazevMenu');

	var temp = '<div class="hideLazevBorder"></div>';
	for(k in tabarray) temp += '<a href="'+ tabarray[k]['link'] +'">'+ tabarray[k]['label'] +'</a>';
	lazevTabMenuArray[this.attr('id')] = temp;

	this.click(function(event) {
		event.preventDefault();
	});

	this.mouseover(function() {
		lazevTabMenuOpen = true;

		$('#lazevTabMenu').fadeIn(100);

		nowleft = $(this).offset();
		nowleft.left = $(this).offset().left+7;
		nowleft.top  = $(this).offset().top+28;

		$('#lazevTabMenu').offset(nowleft);
		$('#lazevTabMenu').html(lazevTabMenuArray[$(this).attr('id')]);
		$('.withLazevMenuHover').removeClass('withLazevMenuHover');
		$(this).parent().addClass('withLazevMenuHover');
	});

	this.mouseout(function() {
		lazevTabMenuOpen = false;
		setTimeout(function() {
			if(!lazevTabMenuOpen) {
				$('.withLazevMenuHover').removeClass('withLazevMenuHover');
				$('#lazevTabMenu').fadeOut(100);
			}
		}, 300);
	});

	$('#lazevTabMenu').mouseover(function() { lazevTabMenuOpen = true; });
	$('#lazevTabMenu').mouseout(function() {
		lazevTabMenuOpen = false;
		setTimeout(function() {
			if(!lazevTabMenuOpen) {
				$('.withLazevMenuHover').removeClass('withLazevMenuHover');
				$('#lazevTabMenu').fadeOut(100);
			}
		}, 300);
	});
}


$(document).ready(function() {

	$('#lazevTabCadastros').lazevTab([
		{ label:'Clientes',     link:'../clientes' },
		{ label:'Fornecedores', link:'../fornecedores' },
		{ label:'Usuários',     link:'../usuarios' }
	]);

	$('#lazevTabVendas').lazevTab([
		{ label:'Vender produto', link:'../vender' },
		{ label:'Listar vendas',  link:'../vendas' }
	]);

});
