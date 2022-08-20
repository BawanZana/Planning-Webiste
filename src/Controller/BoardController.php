<?php

namespace Src\Controller;

use Src\Table\BoardModel;


class BoardController
{

    private $db;
    private $requestMethod;
    private $boardId;

    private $BoardModel;

    public function __construct($requestMethod, $boardId)
    {
        
        $this->requestMethod = $requestMethod;
        $this->boardId = $boardId;

        $this->BoardModel = new BoardModel();
    }

    public function processRequest()
    {
        //Method determination.
        switch ($this->requestMethod) {
            case 'GET':
                if ($this->boardId) {
                    $response = $this->getBoard($this->boardId);
                } else {
                    $response = $this->getAllBoards();
                };
                break;
            case 'POST':
                $response = $this->createBoardFromRequest();
                break;
            case 'PUT':
                $response = $this->updateBoardFromRequest($this->boardId);
                break;
            case 'DELETE':
                $response = $this->deleteBoard($this->boardId);
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

    //Getting all boards.
    private function getAllBoards()
    {
        $result = $this->BoardModel->findAll();
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }

    //Getting one board.
    private function getBoard($id)
    {
        $result = $this->BoardModel->find($id);
        if (!$result) {
            return $this->notFoundResponse();
        }
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);

        if ($result[0]['id']) {
            $_SESSION['board'] = TRUE;
            $_SESSION['board_id'] = $result[0]['id'];
            $_SESSION['board_name'] = $result[0]['board_name'];
        }


        return $response;
    }

    //Creating board.
    private function createBoardFromRequest()
    {

        $input = (array) json_decode(file_get_contents('php://input'), TRUE);


        if (!$this->validateBoard($input)) {
            return $this->unprocessableEntityResponse();
        }
        $this->BoardModel->insert($input);
        $response['status_code_header'] = 'HTTP/1.1 201 Created';
        $response['body'] = "Board created successfully";
        return $response;
    }

    //Updating specific board.
    private function updateBoardFromRequest($id)
    {
        $result = $this->BoardModel->find($id);
        if (!$result) {
            return $this->notFoundResponse();
        }
        $input = (array) json_decode(file_get_contents('php://input'), TRUE);
        if (!$this->validateBoard($input)) {
            return $this->unprocessableEntityResponse();
        }
        $this->BoardModel->update($id, $input);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = "Board updated successfully";
        return $response;
    }

    //Deleting specific board.
    private function deleteBoard($id)
    {
        $result = $this->BoardModel->find($id);
        if (!$result) {
            return $this->notFoundResponse();
        }
        $this->BoardModel->delete($id);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = "Board deleted successfully";
        return $response;
    }

    //Checking that the user filled all required fields or not.
    private function validateBoard($input)
    {


        if (!isset($input['name'])) {
            return false;
        }

        return true;
    }

    //This function returning response if validationBoard function return false.
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
