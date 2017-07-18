<?php
ini_set('soap.wsdl_cache_enabled', 0);

class UserService
{
    private $users = [];

    public function __construct()
    {
        $this->users = ['eduardo', 'paula', 'pedro'];
    }

    public function get() : array
    {
        return $this->users;
    }

    public function create(string $nome) : string
    {
        return "user ".$nome." created with success  \n";
    }

    public function update(int $id, string $nome) : string
    {
        return "user updated with success \n";
    }
    
    public function delete(int $id) : string
    {
        return "user ".$id." deleted with success \n";
    }
}

$server = new SoapServer("api.wsdl");

$server->setClass('UserService');

$server->handle();