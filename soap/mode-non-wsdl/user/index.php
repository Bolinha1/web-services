 <?php
	$api = new SoapClient(null, [
									'uri' => 'http://localhost/web-services/soap/mode-non-wsdl/user',
									'location' => 'http://localhost/web-services/soap/mode-non-wsdl/user/server.php'
								]);

	$users = $api->get();

	foreach($user as $user)
		echo $user. "<br />";

	echo "Request for method create user.: " .$api->create('eduardo', 'eduardo@teste.com.br'). "<br />";
	
	echo "Request for method update user.: " .$api->update(1, 'cesar', 'cesar@teste.com.br'). "<br />";

	echo "Request for method delete user.: " .$api->delete(1). "<br />";