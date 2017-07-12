<?php
ini_set('soap.wsdl_cache_enabled', 0);


class User
{
    public function create(string $nome, string $email) : string
    {
    	return 'user '.$nome.' created with success';
    }

    public function update(int $id, string $nome, string $email) : string
    {
        return 'user '.$id.' updated with success';
    }
    
    public function delete(int $id) : string
    {
    	return 'user '.$id.'  deleted with success';
    }
}

$server = new SoapServer("api.wsdl");

$server->setClass('User');

$server->handle();