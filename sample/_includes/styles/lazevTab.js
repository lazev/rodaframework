$(document).ready(function() {


	//PARTE DO MENU

	$('#lazevTabCadastros').lazevTab([
		{ label:'Clientes',     link:'../clientes' },
		{ label:'Fornecedores', link:'../fornecedores' },
		{ label:'Usuários',     link:'../usuarios' }
	]);


	$('#lazevTabVendas').lazevTab([
		{ label:'Vender produto', link:'../vender' },
		{ label:'Listar vendas',  link:'../vendas' }
	]);

	/*FIM DA PARTE DO MENU*/




});


/*SCRIPT DE CRIAÇÃO DOS MENUS*/
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