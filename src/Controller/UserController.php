<?php
namespace Src\Controller;

use Src\middlware\Role;
use Src\Table\UserModel;

// use Src\TableGateways\TaskModel;

class UserController {

    private $db;
    private $requestMethod;
    private $Id;
    private $role;
    private $User;

    public function __construct($db, $requestMethod, $Id)
    {
        $this->db = $db;
        $this->requestMethod = $requestMethod;
        $this->Id = $Id;
        $this->role=new Role();
        $this->User = new UserModel($db);
    }

    public function processRequest()
    {
        switch ($this->requestMethod) {
            case 'GET':
                if ($this->Id) {
                    $response = $this->getUser($this->Id);
                } elseif($this->role->productOwner()) {
                    $response = $this->getAllUsers();
                }
                else{
                    exit("you can't access users profile");
                };
                break;
            case 'POST':
                $response = $this->createUserFromRequest();
                break;
            case 'PUT':
                $response = $this->updateUserFromRequest($this->Id);
                break;
            case 'DELETE':
                $response = $this->deleteUser($this->Id);
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

    private function getAllUsers()
    {
        $result = $this->User->findAll();
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }

    private function getUser($id)
    {
        $result = $this->User->find($id);
        if (! $result) {
            return $this->notFoundResponse();
        }
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }

    private function createUserFromRequest()
    {
        // var_dump($_POST['name']);
        // exit;
        $input = (array) json_decode(file_get_contents('php://input'), TRUE);
        // var_dump($input);
        // exit;
        if (! $this->validateUser($input)) {
            return $this->unprocessableEntityResponse();
        }
        $this->User->insert($input);
        $response['status_code_header'] = 'HTTP/1.1 201 Created';
        $response['body'] = "account created successfully";
        return $response;
    }

    private function updateUserFromRequest($id)
    {
        $result = $this->User->find($id);
        if (! $result) {
            return $this->notFoundResponse();
        }
        $input = (array) json_decode(file_get_contents('php://input'), TRUE);
        if (! $this->validateUser($input)) {
            return $this->unprocessableEntityResponse();
        }
        $this->User->update($id, $input);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = null;
        return $response;
    }

    private function deleteUser($id)
    {
        $result = $this->User->find($id);
        if (! $result) {
            return $this->notFoundResponse();
        }
        $this->User->delete($id);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        session_destroy();
        $response['body'] = "account deleted successfully";
        return $response;
    }

    private function validateUser($input)
    {
        // var_dump($input['name']);
        if (! isset($input['name'])) {
            return false;
        }
        if (! isset($input['email'])) {
            return false;
        }
        if (! isset($input['password'])) {
            return false;
        }
        // if (! isset($input['user_type'])) {
        //     return false;
        // }
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