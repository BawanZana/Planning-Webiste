<?php

namespace Src\Controller;

use Src\Table\LabelModel;



class LabelController
{

    private $db;
    private $requestMethod;
    private $Id;

    private $Label;

    public function __construct($requestMethod, $Id)
    {
        
        $this->requestMethod = $requestMethod;
        $this->Id = $Id;

        $this->Label = new LabelModel();
    }

    public function processRequest()
    {
        //Method determination.
        switch ($this->requestMethod) {
            case 'GET':
                if ($this->Id) {
                    $response = $this->getLabel($this->Id);
                } else {
                    $response = $this->getAllLabels();
                };
                break;
            case 'POST':
                $response = $this->createLabelFromRequest();
                break;
            case 'PUT':
                $response = $this->updateLabelFromRequest($this->Id);
                break;
            case 'DELETE':
                $response = $this->deleteLabel($this->Id);
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

    //Getting all labels.
    private function getAllLabels()
    {
        $result = $this->Label->findAll();
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }

    //Getting specific label.
    private function getLabel($id)
    {
        $result = $this->Label->find($id);
        if (!$result) {
            return $this->notFoundResponse();
        }
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }

    //Creating label.
    private function createLabelFromRequest()
    {

        $input = (array) json_decode(file_get_contents('php://input'), TRUE);


        if (!$this->validateLabel($input)) {
            return $this->unprocessableEntityResponse();
        }
        $this->Label->insert($input);
        $response['status_code_header'] = 'HTTP/1.1 201 Created';
        $response['body'] = "Label Created";
        return $response;
    }

    //Updating specific label.
    private function updateLabelFromRequest($id)
    {
        $result = $this->Label->find($id);
        if (!$result) {
            return $this->notFoundResponse();
        }
        $input = (array) json_decode(file_get_contents('php://input'), TRUE);
        if (!$this->validateLabel($input)) {
            return $this->unprocessableEntityResponse();
        }
        $this->Label->update($id, $input);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = "Label Updated";
        return $response;
    }

    //Deleting specific label.
    private function deleteLabel($id)
    {
        $result = $this->Label->find($id);
        if (!$result) {
            return $this->notFoundResponse();
        }
        $this->Label->delete($id);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = "Label Deleted";
        return $response;
    }

    //Checking that the user filled all required fields or not.
    private function validateLabel($input)
    {
        if (!isset($input['name'])) {
            return false;
        }



        return true;
    }

    //This function returning response if validateLabel function return false.
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
