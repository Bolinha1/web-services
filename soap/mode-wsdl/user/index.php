 <?php
	ini_set('soap.wsdl_cache_enabled', 0);

	$api = new SoapClient("api.wsdl", ['trace' => 1]);

	echo "Request for method create user.: " .$api->create('eduardo', 'eduardo@teste.com.br'). "<br />";
	
	echo "Request for method update user.: " .$api->update(1, 'cesar', 'cesar@teste.com.br'). "<br />";

	echo "Request for method delete user.: " .$api->delete(1). "<br />";