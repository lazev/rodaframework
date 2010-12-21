if(clicar_pra_abrir) var varmenuaberto = false;
else var varmenuaberto = true;
var lazevMenuAberto;

//document.onmousedown = clicarfora;
document.writeln(""+
	"<span id='lazevMenu'>"+
	"<table class='tabelaPrincipal' onmouseover='lazevMenuNaoFechar()' onmouseout='lazevMenuFechar()'><tr>"+
"");
for(i=0; i<matrizLazevMenu.length; i++) {
	document.write("<td onmouseover='pintamenu(this, "+ i +");' id='lazevSuperMenu"+ i +"'>");
	if(matrizLazevMenu[i][0].indexOf("|") > -1) {
		document.write("<a href='javascript:clicamenu("+ i +", this, true);' class='ui-corner-all'>"+ matrizLazevMenu[i][0].split("|")[0] +"</a></td>");
	} else {
		document.write("<a href='javascript:void(0);' class='ui-corner-all'>"+ matrizLazevMenu[i][0] +"</a></td>");
	}
}
document.writeln(""+
	//"<td class='barraFinal'>&nbsp;</td>"+
	"</tr></table>"+
	"<div id='submenu' onmouseover='lazevMenuNaoFechar()' onmouseout='lazevMenuFechar()'></div>"+
	"<div id='submenu2' onmouseover='lazevMenuNaoFechar()' onmouseout='lazevMenuFechar()'></div>"+
	"</span>"+
"");

function clicamenu(i, x, v) {
	if((matrizLazevMenu[i][0].indexOf("|") > -1) && (v)) {
		nome = matrizLazevMenu[i][0].split("|");
		if(nome[1].indexOf("javascript") > -1) {
			executar = nome[1].split(":");
			eval(executar[1]);
		} else vai(nome[1]);
	} else {
		esquerda = x.offsetLeft;
		strmenu = "<table class='subTabela' style='left: "+ esquerda +"px;'>";
		for(k=1; k<matrizLazevMenu[i].length; k++) {
			nome = matrizLazevMenu[i][k].split("|");
			if(nome[0] == "-") strmenu += "<tr><td colspan='2'><hr/></td></tr>";
			else {
				if(nome[1] == "submenu") {
					if(imagem_submenu !== "") var seta_submenu = "<img class='seta' src='"+ imagem_submenu +"' alt='' title=''>";
					else var seta_submenu = "<b>»</b>";
					while(nome[0].indexOf(" ") > -1) nome[0] = nome[0].replace(" ", "&nbsp;");
					strmenu += "<tr><td onmouseover='pintasubmenu(this, "+ esquerda +", true, 1, \""+ nome[2] +"\");' onmouseout='pintasubmenu(this, 0, false, 2, \""+ nome[2] +"\");'><a href='javascript:void(0);'>"+ nome[0] +"</a></td><td>"+ seta_submenu +"</td></tr>";
				} else {
					strmenu += "<tr><td onmouseover='pintasubmenu(this, 0, true, 2, 0);' onmouseout='pintasubmenu(this, 0, false, 2, 0);' colspan='2'><a href='"+ nome[1] +"'>"+ nome[0] +"</a></td></tr>";
				}
			}
		}
		strmenu += "</table>";
		setTimeout('document.getElementById("submenu").innerHTML = strmenu;', 1);
	}
}
function clicasubmenu(x, e, i) {
	largura = x.offsetWidth;
	esquerda = e + largura;
	topo = parseFloat(x.offsetTop+20);
	strmenu = "<table class='subTabela' style='left: "+ esquerda +"px; top: "+ topo +"px;'>";
	for(k=1; k<matrizLazevMenu[i].length; k++) {
		nome = matrizLazevMenu[i][k].split("|");
		if (nome[0] == "-") strmenu += "<tr><td colspan='2'><hr/></td></tr>";
		else {
			while(nome[0].indexOf(" ") > -1) nome[0] = nome[0].replace(" ", "&nbsp;");
			strmenu += "<tr><td onmouseover='pintasubmenu(this, 0, true, 0);' onmouseout='pintasubmenu(this, 0, false, 0);'><a href='"+ nome[1] +"' colspan='2'>"+ nome[0] +"</a></td></tr>";
		}
	}
	strmenu += "</table>";
	setTimeout('document.getElementById("submenu2").innerHTML = strmenu;', 1);
}
function pintamenu(x, z) {
	sumirsubmenu();
	if(varmenuaberto) clicamenu(z, x, false);
}
function pintasubmenu(x, e, y, z, i) {
	if(y) {
		if (z == 1) clicasubmenu(x, e, i);
		else if (z == 2) sumirsubmenu();
	}
}
function sumirmenu() {
	document.getElementById("submenu").innerHTML = "";
	document.getElementById("submenu2").innerHTML = "";
	if(clicar_pra_abrir) varmenuaberto = false;
}
function sumirsubmenu() { document.getElementById("submenu2").innerHTML = ""; }
function lazevMenuNaoFechar() { if(lazevMenuAberto) clearTimeout(lazevMenuAberto); }
function lazevMenuFechar() { lazevMenuAberto = setTimeout('sumirmenu()', tempo_menu_aberto); }
function vai(x) { window.location=x; }
//**** FIM DO CÓDIGO DO MENU (SIM, É SÓ ISSO) ****
