 <?php
	ini_set('soap.wsdl_cache_enabled', 0);

	$api = new SoapClient("api.wsdl", ['trace' => 1]);

	$users = $api->get();
	foreach($users as $user)
		echo $user;

	echo $api->create('luiz');
	echo $api->update(1, 'cesar');
	echo $api->delete(1);