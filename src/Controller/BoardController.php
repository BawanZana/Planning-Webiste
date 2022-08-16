<?php
namespace Src\Controller;

use Src\Table\BoardModel;
// use Src\TableGateways\TaskModel;

class BoardController {

    private $db;
    private $requestMethod;
    private $boardId;

    private $BoardModel;

    public function __construct($db, $requestMethod, $boardId)
    {
        $this->db = $db;
        $this->requestMethod = $requestMethod;
        $this->boardId = $boardId;

        $this->BoardModel = new BoardModel($db);
    }

    public function processRequest()
    {
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
            // header("Location:/views/show.php");
        }

        
    }

    private function getAllBoards()
    {
        $result = $this->BoardModel->findAll();
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }

    private function getBoard($id)
    {
        $result = $this->BoardModel->find($id);
        if (! $result) {
            return $this->notFoundResponse();
        }
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);

        if($result[0]['id'])
        {
            $_SESSION['board']=TRUE;
            $_SESSION['board_id']=$result[0]['id'];
            $_SESSION['board_name']=$result[0]['board_name'];
        }
       

        return $response;
    }

    private function createBoardFromRequest()
    {
        
        $input = (array) json_decode(file_get_contents('php://input'), TRUE);
        
        
        if (! $this->validateBoard($input)) {
            return $this->unprocessableEntityResponse();
        }
        $this->BoardModel->insert($input);
        $response['status_code_header'] = 'HTTP/1.1 201 Created';
        $response['body'] = "Board created successfully";
        return $response;
    }

    private function updateBoardFromRequest($id)
    {
        $result = $this->BoardModel->find($id);
        if (! $result) {
            return $this->notFoundResponse();
        }
        $input = (array) json_decode(file_get_contents('php://input'), TRUE);
        if (! $this->validateBoard($input)) {
            return $this->unprocessableEntityResponse();
        }
        $this->BoardModel->update($id, $input);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = null;
        return $response;
    }

    private function deleteBoard($id)
    {
        $result = $this->BoardModel->find($id);
        if (! $result) {
            return $this->notFoundResponse();
        }
        $this->BoardModel->delete($id);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = null;
        return $response;
    }

    private function validateBoard($input)
    {
        
        
        if (! isset($input['name'])) {
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