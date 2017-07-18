<?php
class UserService
{
    public function get() : array
    {
        return ['eduardo', 'paula', 'pedro'];
    }
    public function create(string $name) : string
    {
    	return 'user '.$name.' created with success';
    }

    public function update(int $id, string $name) : string
    {
        return 'user '.$id.' updated with success';
    }
    
    public function delete(int $id) : string
    {
    	return 'user '.$id.'  deleted with success';
    }
}

$server = new SoapServer(null, ['uri' => 'http://localhost/web-services/soap/mode-non-wsdl/user']);

$server->setClass('UserService');

$server->handle();