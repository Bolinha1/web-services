<?php
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

$server = new SoapServer(null, ['uri' => 'http://localhost/web-services/soap/mode-non-wsdl/user']);

$server->setClass('User');

$server->handle();