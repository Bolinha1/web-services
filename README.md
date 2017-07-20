<h2>WEB SERVICES PHP - UMA ABORDAGEM SOBRE RPC, SOAP</h2>


<h3>1 - WEB SERVICES - DEFINIÇÃO E APLICABILIDADE</h3>

<p>
    Tem se tornado cada vez mais comum pensar em aplicações que são criadas consumindo ou expondo algum tipo de funcionalidade através de implementações de serviços. Web services definem como pode acontecer essas interações entre softwares que estão sendo executados em plataformas distintas.
    Essa interação comumente acontece fazendo uso de um meio comun de tráfego como por exemplo, o protocolo HTTP e um formato de arquivo que de formato comum tais como, XML ou JSON. 
</p>
<p>
    Algumas variações de web services seriam, Rpc (Xml-rpc), Soap e Rest, cada um possuindo características próprias de implementação. Serviços web são úteis para que softwares em diferentes máquinas executando com diferentes tecnologias possam se comunicar, por exemplo um sistema legado que só executa suas funções em ambiente local, pode expor esssas funcionalidades utilizando algum dos serviços mencionados acima, tornando possível o consumo dessas funcionalidades por clientes web ou mobile. 
    Será demonstrado e explicado como pode acontecer a implementação de uma Api de Usuários, utilizando serviços XML-RPC, SOAP e REST.
</p>


<h3>2 - SERVIÇOS RPC - O QUE SÃO E COMO FUNCIONAM?</h3>

<p>
    Uma implementação de web service  seriam os serviços RPC que significam Remote Procedure Call, para o português, Chamada para Procedimentos Remotos. Serviços RPC consistem na chamada de funções entre duas máquinas seguindo o conceito de cliente/servidor, onde cada cliente realiza uma requisição HTTP POST normalmente para um único ponto de entrada enviando um documento XML que específica a função a ser chamada e seus respectivos parâmetros, de maneira similar a realização de chamadas de funções locais.
<p>
<p>
    Para se trabalhar com serviços RPC uma abordagem comum seria seguir uma implementação XML-RPC (http://xmlrpc.scripting.com/spec.html), no caso o PHP possui uma série de funções que permitem a criação de clientes e servidores XML-RPC. 
</p>

<h3>2.2 - FUNÇÕES PHP PARA XML-RPC</h3>

<p>
    O PHP fornece um conjuntos de funções úteis para que seja possível trabalhar com serviços XML-RPC. Utilizando as funções nativas da linguagem conseguimos de forma relativamente simples criar um cliente e um servidor XML-RPC. 
</p>
<p>
    Através do uso de recursos oferecidos pela própria linguagem PHP será criado um cliente XML-RPC que irá consumir uma Api de usuários que estará disponível através de um servidor XML-RPC.
</p>

```php
    <?php

    $request = xmlrpc_encode_request("getUser", []);

    //$request = xmlrpc_encode_request("createUser", ['eduardo']);

    //$request = xmlrpc_encode_request("updateUser", [1, 'eduardo']);

    //$request = xmlrpc_encode_request("deleteUser", [1]);

    $context = stream_context_create(['http' =>[
                            'method' => "POST",
                            'header' => "Content-Type: text/xml",
                            ‘content' => $request]]);

    $server = 'http://localhost/web-services/xml-rpc/user/server.php';

    $file = file_get_contents($server, false, $context);

    $response = xmlrpc_decode($file);

    var_dump($response);
```

<p>
    O código acima ilustra como pode ser feita uma chamada a um procedimento remoto utilizando recursos do PHP. 
    A primeira linha do script é responsável por codificar o arquivo XML a ser enviado no corpo de uma uma requisição POST. Abaixo exemplo do arquivo enviado na chamada de função getUser():
</p>

~~~xml
    <?xml version="1.0" encoding="iso-8859-1"?>
    <methodCall>
       <methodName>getUser</methodName>
       <params/>
    </methodCall>
~~~
<p>  
    O exemplo do arquivo de envio para uma chamada á função createUser() seria:
</p>

~~~xml
    <?xml version="1.0" encoding="iso-8859-1"?>
    <methodCall>
        <methodName>createUser</methodName>
        <params>
            <param>
                <value>
                    <string>eduardo</string>
                </value>
            </param>
        </params>
    </methodCall>
~~~

<p>
    A chamada a qualquer método descrito no código é válida, após codificar o arquivo a ser enviado na requisição é criado um stream de rede utilizando o protocolo HTTP possuindo as seguintes características o método POST, o content-type  para text/xml, o content possuindo o arquivo gerado por xmlrpc_encode_request(), a seguir é utilizado a função file_get_contents()  para ler o conteúdo retornado desse stream para uma string, que então através da função xmlrpc_decode() é decodificada e seu resultado exibido.
</p>

<p>
    Este mesmo processamento poderia ser feito utilizando curl, como pode ser visto no código abaixo:
</p>

```php
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
```

<p>
    Variando a forma como pode ser feita a requisição.
    O servidor que irá responder por cada requisição pode ser representado pelo seguinte código:
</p>    

```php
    <?php
    function get(string $method_name) : array
    {
        return ['eduardo', 'paula', 'pedro'];
    }

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
```

<p>
    Acima estão descritas as funções que podem ser chamadas remotamente, elas recebem em seu primeiro parâmetros uma string $method_name, que é o nome pelo qual essa função será referenciada, e chamada externamete, já o segundo parâmetro trata-se de um array contendo os dados a serem processados pelas funções.
</p>

```php
    <?php

    $request = file_get_contents("php://input");

    $server = xmlrpc_server_create();

    xmlrpc_server_register_method($server, "getUser", "get");

    xmlrpc_server_register_method($server, "createUser", "create");

    xmlrpc_server_register_method($server, "updateUser", "update");

    xmlrpc_server_register_method($server, "deleteUser", "delete");

    header('Content-Type: text/xml');

    print xmlrpc_server_call_method($server, $request, array());
```

<p>
    O código acima inicia recuperando os valores enviados no corpo de uma requisição HTTP utilizando file_get_contents("php://input"), em seguida através da função xmlrpc_server_create() é iniciado um servidor XML-RPC, 
    o que permite registrar funções que possam ser chamadas por um client XML-RPC. Com o uso da função xmlrpc_server_register_method(), é possível registrarmos funções no servidor que foi iniciado, ela recebe 3 parâmetros de entrada, o primeiro é o server que foi iniciado a partir de xmlrpc_server_create(), o segundo parâmetro se refere ao $method_name, 
    valor este, pelo qual o client XML-RPC chama a função, o terceiro parâmetro é a função própriamente dita que foi referenciada por method_name. 
    Logo ao registrar xmlrpc_server_register_method($server, "getUser", "get"), o client irá chamar getUser e não get, podendo ser usado para a criação de uma Api mais 
    condizente com a realização do serviço. 
    Em seguida é definido o content-type para text/xml para ser enviado no retorno e, então, é realizada uma chamada para 
    xmlrpc_server_call_method(), recebendo 3 parâmetros, o primeiro é o servidor a ser executado, o segundo o 
    request enviado por POST, e o terceiro um array, essa função é encarregada por executar a função informada no request 
    no servidor que ela estiver registrada. 
</p>    

<h3>3 - SOAP - O QUE É SOAP E SEU FUNCIONAMENTO?</h3>

<p>
    SOAP provê um mecanismo para troca de informação em ambientes descentralizados, é especificado pela w3c 
    contendo duas versões SOAP 1.1 e SOAP 1.2. 
    Baseado em XML consiste na troca de informações utilizando HTTP ou outro protocolo 
    para o trafégo de dados. 
    SOAP é um tipo de serviço que pode ou não trabalhar em conjunto com 
    WSDL que seriam WEB SERVICES DESCRIPTION LANGUAGE, consiste em um documento xml 
    que contém informações relevantes sobre o serviço, como por exemplo, 
    métodos que podem ser chamados, parâmetros de entrada e saída, meios de acesso. 
</p>

<p>
    Abaixo segue um exemplo de WSDL utilizado para a Api de usuários:
</p>

~~~xml
    <?xml version='1.0' encoding='UTF-8'?>
    <definitions name="Api" targetNamespace="urn:Api" xmlns:typens="urn:Api" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/wsdl/soap/" xmlns:soapenc="http://schemas.xmlsoap.org/soap/encoding/" xmlns:wsdl="http://schemas.xmlsoap.org/wsdl/" xmlns="http://schemas.xmlsoap.org/wsdl/">
        <message name="get" />
        <message name="getResponse">
            <part name="getReturn" type="xsd:anyType"/>
        </message>
        <message name="create">
            <part name="nome" type="xsd:string"/>
        </message>
        <message name="createResponse">
            <part name="createReturn" type="xsd:string"/>
        </message>
        <message name="update">
            <part name="id" type="xsd:int"/>
            <part name="nome" type="xsd:string"/>
        </message>
        <message name="updateResponse">
            <part name="updateReturn" type="xsd:string"/>
        </message>
        <message name="delete">
            <part name="id" type="xsd:int"/>
        </message>
        <message name="deleteResponse">
            <part name="deleteReturn" type="xsd:string"/>
        </message>
        <portType name="UserServicePortType">
            <operation name="get">
                <input message="typens:get"/>
                <output message="typens:getResponse"/>
            </operation>
            <operation name="create">
                <input message="typens:create"/>
                <output message="typens:createResponse"/>
            </operation>
            <operation name="update">
                <input message="typens:update"/>
                <output message="typens:updateResponse"/>
            </operation>
            <operation name="delete">
                <input message="typens:delete"/>
                <output message="typens:deleteResponse"/>
            </operation>
        </portType>
        <binding name="UserServiceBinding" type="typens:UserServicePortType">
            <soap:binding style="rpc" transport="http://schemas.xmlsoap.org/soap/http"/>
            <operation name="get">
                <soap:operation soapAction="urn:UserServiceAction"/>
                <input>
                    <soap:body namespace="urn:Api" use="encoded" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/>
                </input>
                <output>
                    <soap:body namespace="urn:Api" use="encoded" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/>
                </output>
            </operation>
            <operation name="create">
                <soap:operation soapAction="urn:UserServiceAction"/>
                <input>
                    <soap:body namespace="urn:Api" use="encoded"encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/>
                </input>
                <output>
                    <soap:body namespace="urn:Api" use="encoded" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/>
                </output>
            </operation>
            <operation name="update">
                <soap:operation soapAction="urn:UserServiceAction"/>
                <input>
                    <soap:body namespace="urn:Api" use="encoded" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/>
                </input>
                <output>
                    <soap:body namespace="urn:Api" use="encoded" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/>
                </output>
            </operation>
            <operation name="delete">
                <soap:operation soapAction="urn:UserServiceAction"/>
                <input>
                    <soap:body namespace="urn:Api" use="encoded" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/>
                </input>
                <output>
                    <soap:body namespace="urn:Api" use="encoded" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/>
                </output>
            </operation>
        </binding>
        <service name="ApiService">
            <port name="UserServicePort" binding="typens:UserServiceBinding">
                <soap:address location="http://localhost/web-services/soap/mode-wsdl/user/server.php"/>
            </port>
        </service>
    </definitions>
~~~

<p>
    Acima está o código de wsdl utilizando durante o exemplo. Nele estão descritos os métodos 
    e seus parâmetros e entrada, retorno e formas de acesso.  
    Para mais detalhes sobre consulte (https://www.w3.org/TR/wsdl).
</p>

<p>
    Possui um formato de requisição bem específicado, os documentos XML que são trocados nas requisições são conhecidos como Envelopes Soap, sendo estes determinados pelas marcações presentes nestes documentos, por exemplo:
</p>

~~~xml
    <?xml version="1.0" encoding="UTF-8"?>
    <SOAP-ENV:Envelope 
            xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" 
            xmlns:ns1="urn:Api" 
            xmlns:xsd="http://www.w3.org/2001/XMLSchema" 
            xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" 
            xmlns:SOAP-ENC="http://schemas.xmlsoap.org/soap/encoding/" 
            SOAP-ENV:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/">
        <SOAP-ENV:Body>
            <ns1:create>
                <nome xsi:type="xsd:string">eduardo</nome>
            </ns1:create>
        </SOAP-ENV:Body>
    </SOAP-ENV:Envelope>
~~~

<p>
    O código acima demonstra o formato existente em um envelope soap. As características presentes, 
    a definição de envelope SOAP-ENV:Envelope, elementos estes 
    obrigatórios para que esse documento possa ser compreendido por um servidor Soap, 
    outra característica obrigatória são os elementos body, descritos SOAP-ENV:Body, 
    entre o elemento body estão descritos o método de acesso e os parâmetros a serem 
    processados,  elemento que pode estar presente mas não é obrigatório, são os elementos  header. 
</p>
<p>
    Da mesma maneira será enviado na resposta um documento XML possuindo marcação semelhante ao de requisição. 
    Abaixo é apresentado o documento XML enviado na resposta:
</p>

~~~xml
    <?xml version="1.0" encoding="UTF-8"?>
    <SOAP-ENV:Envelope 
        xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" 
        xmlns:ns1="urn:Api" 
        xmlns:xsd="http://www.w3.org/2001/XMLSchema" 
        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" 
        xmlns:SOAP-ENC="http://schemas.xmlsoap.org/soap/encoding/" 
        SOAP-ENV:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/">
        <SOAP-ENV:Body>
            <ns1:createResponse>
                <createReturn xsi:type="xsd:string">user eduardo created with success</createReturn>
            </ns1:createResponse>
        </SOAP-ENV:Body>
    </SOAP-ENV:Envelope>
~~~
<p>
    As marcações e definições de envelope SOAP continuam sendo respeitadas. Para se obter mais detalhes sobre (https://www.w3.org/TR/2000/NOTE-SOAP-20000508/#_Toc478383494).
</p>

<h3>3.1 - INTERAGINDO COM SOAP EM PHP</h3>

<p>
    O fato de SOAP ser um protocolo muito bem específicado e padronizado junto a w3c 
    faz com que implementações para este padrão existam nativamente na maioria das linguagens de programação 
    e, com PHP não é diferente. 
    Para se trabalhar com SOAP em PHP existem duas bibliotecas nativas SoapClient e SoapServer, 
    ambas possuem um uso bem direto, por exemplo o código abaixo ilustra como poderia 
    ser realizadas as chamadas a um serviço soap exemplificando uma Api de usuários:
</p>

```php
    <?php
        ini_set('soap.wsdl_cache_enabled', 0);

        $options =  [
                'uri' => 'http://localhost/web-services/soap/mode-non-wsdl/user',
                'location' => 'http://localhost/web-services/soap/mode-non-wsdl/user/server.php'];  
        $api = new SoapClient(null, $options);  
        //$api = new SoapClient("api.wsdl", ['trace' => 1]);

        $users = $api->get();
        
        foreach($users as $user)
            echo $user;

        echo $api->create('luiz');
        echo $api->update(1, 'cesar');
        echo $api->delete(1);
```

<p>
    A primeira linha altera a opção de cache que está configurada no php.ini, 
    isso se deve ao fato de estar configurada para 1, mantendo um cache de wsdl ativo. 
    O primeiro caso trata-se de iniciar um SoapCLient sem o uso de wsdl, 
    para isso é necessário informar um array que é criado na segunda linha do script que contém uri, 
    que representa o namespace onde este serviço está alocado 
    e o segundo índice se trata da localidade do serviço, estes valores só são obrigatórios 
    para o uso sem wsdl, e estão representados por este trecho de 
    código SoapClient(null, $options), iniciando assim um soap client sem wsdl. 
    Da mesma forma é possível estar criando client soap informando um caminho válido para 
    um wsdl. 
</p>
<p>
    Feito isso, é possível então interagir com a Api de serviços que for oferecida,
    como por exemplo para criar um novo usuário $api->create('luiz'),
    acessando o método create do serviço soap, ou qualquer outro método que esteja 
    disponível.
    Para criar um servidor que será capaz de responder a essas chamadas é utilizada a classe SoapClient, a partir dela é possível registrarmos classes ou funções como sendo serviços Soap, que podem ser acessados externamente, abaixo segue um exemplo de como poderia ser implementado os serviços de uma Api de usuários:
</p>

```php
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
    $server = new SoapServer(null,['uri' => 'http://localhost/web-services/soap/mode-non-wsdl/user']);
    //$server = new SoapServer("api.wsdl");

    $server->setClass('UserService');

    $server->handle();
```

<p>
    Da mesma forma que SoapClient, SoapServer pode ser iniciada com ou sem wsdl, seguindo a mesma regra, porém desta vez o unico elemento necessário no modo sem wsdl, 
    é a uri do serviço, onde ele está alocado. 
</p>

<p>
    Com isso feito, é possível registrar uma classe ou um conjunto de funções como serviço, no caso é definido a 
    class UserService, e qualquer método existente nele é disparado a partir de $server->handle(). 
</p>
