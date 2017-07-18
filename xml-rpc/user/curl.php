<?php

$request = xmlrpc_encode_request("getUser", []);

//$request = xmlrpc_encode_request("createUser", ['eduardo']);

//$request = xmlrpc_encode_request("updateUser", [1, 'eduardo']);

//$request = xmlrpc_encode_request("deleteUser", [1]);

$server = 'http://localhost/web-services/xml-rpc/user/server.php';

$curl = curl_init($server);

curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, $request);

$file = curl_exec($curl);

curl_close($curl);

$response = xmlrpc_decode($file);

var_dump($response);