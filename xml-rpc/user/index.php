<?php

$request = xmlrpc_encode_request("getUser", []);

//$request = xmlrpc_encode_request("createUser", ['eduardo']);

//$request = xmlrpc_encode_request("updateUser", [1, 'eduardo']);

//$request = xmlrpc_encode_request("deleteUser", [1]);


$context = stream_context_create(['http' => 	[
													'method' => "POST",
													'header' => "Content-Type: text/xml",
													'content' => $request
												]
								]);

$server = 'http://localhost/web-services/xml-rpc/user/server.php';

$file = file_get_contents($server, false, $context);

$response = xmlrpc_decode($file);

var_dump($response);