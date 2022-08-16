<?php
namespace Src\Controller;

use Src\Table\StatusModel;


class StatusController {

    private $db;
    private $requestMethod;
    private $Id;

    private $Status;

    public function __construct($db, $requestMethod, $Id)
    {
        $this->db = $db;
        $this->requestMethod = $requestMethod;
        $this->Id = $Id;

        $this->Status = new StatusModel($db);
    }

    public function processRequest()
    {
        switch ($this->requestMethod) {
            case 'GET':
                if ($this->Id) {
                    $response = $this->getStatus($this->Id);
                } else {
                    $response = $this->getAllStatus();
                };
                break;
            case 'POST':
                $response = $this->createStatusFromRequest();
                break;
            case 'PUT':
                $response = $this->updateStatusFromRequest($this->Id);
                break;
            case 'DELETE':
                $response = $this->deleteStatus($this->Id);
                break;
            default:
                $response = $this->notFoundResponse();
                break;
        }
        
        header($response['status_code_header']);
        if ($response['body']) {
            echo $response['body'];
            // header("Location:/views/show.php");
        }

        
    }

    private function getAllStatus()
    {
        $result = $this->Status->findAll();
        // var_dump($result);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }

    private function getStatus($id)
    {
        $result = $this->Status->find($id);
        if (! $result) {
            return $this->notFoundResponse();
        }

        if($result[0]['id'])
        {
            $_SESSION['status']=TRUE;
            $_SESSION['status_id']=$result[0]['id'];
            $_SESSION['status_name']=$result[0]['status_name'];
            $_SESSION['status_description']=$result[0]['description'];
            $_SESSION['status_flag']=$result[0]['flag'];
            
        }

        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }

    private function createStatusFromRequest()
    {
        
        $input = (array) json_decode(file_get_contents('php://input'), TRUE);
        
        
        if (! $this->validateStatus($input)) {
            return $this->unprocessableEntityResponse();
        }
        $this->Status->insert($input);
        $response['status_code_header'] = 'HTTP/1.1 201 Created';
        $response['body'] = "status created successfully";
        return $response;
    }

    private function updateStatusFromRequest($id)
    {
        $result = $this->Status->find($id);
        if (! $result) {
            return $this->notFoundResponse();
        }
        $input = (array) json_decode(file_get_contents('php://input'), TRUE);
        if (! $this->validateStatus($input)) {
            return $this->unprocessableEntityResponse();
        }
        $this->Status->update($id, $input);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = null;
        return $response;
    }

    private function deleteStatus($id)
    {
        $result = $this->Status->find($id);
        if (! $result) {
            return $this->notFoundResponse();
        }
        $this->Status->delete($id);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = null;
        return $response;
    }

    private function validateStatus($input)
    {
        if (! isset($input['name'])) {
            return false;
        }
      
        if (! isset($input['board_id'])) {
            return false;
        }
        return true;
    }

    private function unprocessableEntityResponse()
    {
        $response['status_code_header'] = 'HTTP/1.1 422 Unprocessable Entity';
        $response['body'] = json_encode([
            'error' => 'Invalid input !'
        ]);
        return $response;
    }

    private function notFoundResponse()
    {
        $response['status_code_header'] = 'HTTP/1.1 404 Not Found';
        $response['body'] = null;
        return $response;
    }
}