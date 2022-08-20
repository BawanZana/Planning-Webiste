<?php

namespace Src\Controller;

use Src\Table\LogModel;


class LogController
{

    private $db;
    private $requestMethod;
    private $Id;

    private $Log;

    public function __construct($requestMethod, $Id)
    {
        
        $this->requestMethod = $requestMethod;
        $this->Id = $Id;

        $this->Log = new LogModel();
    }

    public function processRequest()
    {
        //Method determination.
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $uri = explode('/', $uri);
        switch ($this->requestMethod) {
            case 'GET':
                if ($uri[1] == 'history') {
                    if ($this->Id) {
                        $response = $this->getLog($this->Id);
                    }
                    exit;
                } else {
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

    //Getting all logs.
    private function getAllLogs()
    {
        $result = $this->Log->findAll();
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }

    //Getting specific log.
    private function getLog($id)
    {
        $result = $this->Log->find($id);
        if (!$result) {
            return $this->notFoundResponse();
        }
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        echo $response['body'];
        return $response;
    }

    //This function returning response if specific data that required not excited or if the requested method not matching any methods in the above switch case.
    private function notFoundResponse()
    {
        $response['status_code_header'] = 'HTTP/1.1 404 Not Found';
        $response['body'] = "404 Not Found";
        return $response;
    }
}
