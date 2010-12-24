var url = window.location.pathname;
var temp = url.split("/");
var page = temp[(temp.length-2)];
var tuto = 'Cadastrando um cliente';

$(document).ready( function() { setTimeout(function() { switch(page) {

	/*PÁGINA INICIO*/
	case "inicio":

		$('#lazevSuperMenu1').bt('Tutorial "'+tuto+'". Clique em <b>Clientes</b>.', { trigger: 'none' });
		$('#lazevSuperMenu1').btOn();

		status('Tutorial: '+tuto+'. <a href="javascript:stopTutorial();">Clique aqui para sair do tutorial</a>');

	break;


	
	
	/*PÁGINA CLIENTES*/
	case "clientes":

		status('Tutorial: '+tuto+'. <a href="javascript:stopTutorial();">Clique aqui para sair do tutorial</a>');

		$('#lista').bt('Nesta lista você acessa seus clientes já cadastrados.', { trigger: 'none' });
		$('#lista').btOn();

		$('#openFilters').bt('Neste canto fica a janela de filtros. Utilize os filtros para localizar mais rápido os clientes desejados.', { trigger: 'none' });
		$('#openFilters').btOn();
		$('#openFilters').click( function() { $('#openFilters').btOff(); });

		$('#navigationBar div:first').bt('Clique em Inserir para adicionar um novo cliente.');
		$('#navigationBar div:first').btOn();
		$('#navigationBar div:first').click( function() {
			$('#lista').btOff();
			$('.ui-dialog button:first').btOn();

			status('Tutorial: '+tuto+'. <a href="javascript:stopTutorial();">Clique aqui para sair do tutorial</a>');
		});

		$('#cpfcnpj').bt('Preencha com o CPF ou CNPJ do seu cliente. O sistema avisará caso o CPF (ou CNPJ) esteja incorreto, mas não impedirá que você cadastre mesmo assim.', { trigger: ['focus', 'blur'] });
		$('#nome').bt('É o campo mais importante do cadastro do cliente. É pelo nome (ou Razão Social) que você identificará seu cliente em todo o sistema.', { trigger: ['focus', 'blur'] });
		$('#cep').bt('O sistema tem uma base de dados de CEP. Na maioria dos casos, basta preencher corretamente o CEP do cliente e o sistema preencherá outros dados do endereço. É possível, porém, que o CEP não esteja cadastrado na base de dados, bastando, assim, preencher manualmente o restante do endereço.', { trigger: ['focus', 'blur'] });

		$('.ui-dialog button:first').bt('Após preencher os dados do cliente. Clique em Gravar.', { positions: 'top', width: 80 } );
		$('.ui-dialog button:first').click( function() {
			$('#lista').bt('Seu cliente adicionado ficará nesta lista. Para detalhar, clique sobre o nome dele ou selecione na lista e clique no botão <b>Detalhar</b>.', { trigger: 'none', width: 400 });
			$('#lista').btOn();
		});
		

	break;

	

	
	/*PÁGINA DETALHES_CLIENTE*/
	case "detalhes":

		$('#dados_cliente').bt('Pronto, você concluiu o tutorial de cadastro de um novo cliente. Esta é a tela que centraliza todas as informações sobre um cliente. Nela você poderá alterar os dados do cliente, anotar qualquer assunto referente ao cliente, adicionar, editar ou remover serviços assinados por este cliente e verificar as faturas abertas e já pagas dele. <a href="javascript:initTutorial(\'02_detalhar_cliente.js\');">Clique aqui para iniciar o tutorial sobre a Tela de Detalhes</a>.', { trigger: 'none', width: 500 } );
		$('#dados_cliente').btOn();
		$('#dados_cliente a').click(function() { $('#dados_cliente').btOff(); });

		stopTutorial();

	break;


} }, 1000); });