<?php
namespace Src\Controller;

use Src\Table\LogModel;


class LogController {

    private $db;
    private $requestMethod;
    private $Id;

    private $Log;

    public function __construct($db, $requestMethod, $Id)
    {
        $this->db = $db;
        $this->requestMethod = $requestMethod;
        $this->Id = $Id;

        $this->Log = new LogModel($db);
    }

    public function processRequest()
    {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $uri = explode( '/', $uri );
        switch ($this->requestMethod) {
            case 'GET':
                if($uri[1] =='history')
                {
                    if ($this->Id) {
                        $response = $this->getLog($this->Id);
                    }
                    exit;
                }
                else{
                    $response = $this->getAllLogs();
                };
                break;
            
            default:
                $response = $this->notFoundResponse();
                break;
        }
        
        header($response['status_code_header']);
        if ($response['body']) {
            echo $response['body'];
           
        }

        
    }

    private function getAllLogs()
    {
        $result = $this->Log->findAll();
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }

    private function getLog($id)
    {
        $result = $this->Log->find($id);
        if (! $result) {
            return $this->notFoundResponse();
        }
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        echo $response['body'];
        return $response;
    }

    

   

    private function notFoundResponse()
    {
        $response['status_code_header'] = 'HTTP/1.1 404 Not Found';
        $response['body'] = null;
        return $response;
    }
}