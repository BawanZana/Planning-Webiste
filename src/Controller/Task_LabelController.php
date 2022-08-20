<?php

namespace Src\Controller;

use Src\TableGateways\LogModel;
use Src\Table\Task_LabelModel;

class Task_LabelController
{

    private $db;
    private $requestMethod;
    private $Id;

    private $Task_Label;

    public function __construct($requestMethod, $Id)
    {
        
        $this->requestMethod = $requestMethod;
        $this->Id = $Id;

        $this->Task_Label = new Task_LabelModel();
    }

    public function processRequest()
    {
        //Method determination.
        switch ($this->requestMethod) {
            case 'GET':
                if ($this->Id) {
                    $response = $this->getTask_Label($this->Id);
                } else {
                    $response = $this->getAllTask_Label();
                };
                break;
            case 'POST':
                $response = $this->createTask_LabelFromRequest();
                break;

            case 'DELETE':

                $response = $this->deleteTask_Label($this->Id);
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

    //Getting all association between labels and tasks.
    private function getAllTask_Label()
    {
        $result = $this->Task_Label->findAll();
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }

    //Getting all associated labels with specific task. 
    private function getTask_Label($id)
    {
        $result = $this->Task_Label->find($id);
        if (!$result) {
            return $this->notFoundResponse();
        }
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }

    //Creating association between specific task and label.
    private function createTask_LabelFromRequest()
    {
        $input = (array) json_decode(file_get_contents('php://input'), TRUE);

        $result = $this->Task_Label->FindBeforeInsert($input['task_id'],$input['label_id']);
        if ($result) {
            
            exit("This association already exit");
        }

        if (!$this->validateTask_Label($input)) {
            return $this->unprocessableEntityResponse();
        }
        $this->Task_Label->insert($input);
        $response['status_code_header'] = 'HTTP/1.1 201 Created';
        $response['body'] = "Task Label Added";
        return $response;
    }

    //Deleting specific association between task and label.
    private function deleteTask_Label($id)
    {

        $result = $this->Task_Label->find($id);
        if (!$result) {
            return $this->notFoundResponse();
        }

        $this->Task_Label->delete($id);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = "Task Label Deleted";
        return $response;
    }

    //Checking that the user filled all required fields or not.
    private function validateTask_Label($input)
    {

        if (!isset($input['label_id'])) {
            return false;
        }
        if (!isset($input['task_id'])) {
            return false;
        }
        
        return true;
    }

    //This function returning response if validateTask_Label function return false.
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
