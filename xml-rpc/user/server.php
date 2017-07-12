<?php

function create(string $method_name, array $args) : string
{
	return 'user '.$args[0].' created with success';
}

function update(string $method_name, array $args) : string
{
    return 'user '.$args[0].' updated with success';
}

function delete(string $method_name, array $args) : string
{
	return 'user '.$args[0].'  deleted with success';
}

$request = file_get_contents("php://input");

$server = xmlrpc_server_create();

xmlrpc_server_register_method($server, "createUser", "create");

xmlrpc_server_register_method($server, "updateUser", "update");

xmlrpc_server_register_method($server, "deleteUser", "delete");

header('Content-Type: text/xml');

print xmlrpc_server_call_method($server, $request, array());