<?php
namespace Src\Controller;

use Src\Guard\Role;
use Src\Table\UserModel;



class UserController {

    private $db;
    private $requestMethod;
    private $Id;
    
    private $User;

    public function __construct($requestMethod, $Id)
    {
        
        $this->requestMethod = $requestMethod;
        $this->Id = $Id;
        
        $this->User = new UserModel();
    }

    public function processRequest()
    {
        switch ($this->requestMethod) {
            //Method determination.
            case 'GET':
                if ($this->Id) {
                    $response = $this->getUser($this->Id);
                } elseif(Role::productOwner()) {
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

    //Getting all users.
    private function getAllUsers()
    {
        $result = $this->User->findAll();
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }

    //Getting specific user.
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

    //Creating User.
    private function createUserFromRequest()
    {
        
        $input = (array) json_decode(file_get_contents('php://input'), TRUE);
        
        if (! $this->validateUser($input)) {
            return $this->unprocessableEntityResponse();
        }
        $this->User->insert($input);
        $response['status_code_header'] = 'HTTP/1.1 201 Created';
        $response['body'] = "account created successfully";
        return $response;
    }

    //Updating specific user.
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
        $response['body'] = "User Updated";
        return $response;
    }

    //Deleting specific user.
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

    //Checking that the user filled all required fields or not.
    private function validateUser($input)
    {
        
        if (! isset($input['name'])) {
            return false;
        }
        if (! isset($input['email'])) {
            return false;
        }
        if (! isset($input['password'])) {
            return false;
        }
        
        return true;
    }

    //This function returning response if validateUser function return false.
    private function unprocessableEntityResponse()
    {
        $response['status_code_header'] = 'HTTP/1.1 422 Unprocessable Entity';
        $response['body'] = json_encode([
            'error' => 'Invalid input !'
        ]);
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