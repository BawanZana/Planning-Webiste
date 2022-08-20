<?php

namespace Src\Controller;

use Src\Guard\Role;
use Src\TableGateways\LogModel;
use Src\Table\TaskModel;



class TaskController
{

    private $db;
    private $requestMethod;
    private $Id;
    private $search;
    private $order;
    private $Task;
    private $filter;
    private $status;
    

    public function __construct($requestMethod, $Id)
    {
        
        $this->requestMethod = $requestMethod;
        $this->Id = $Id;

        if (isset($_GET['order'])) {
            $this->order = $_GET['order'];
        } else {
            $this->order = 'DESC';
        }

        if (isset($_GET['filter'])) {
            $this->filter = $_GET['filter'];
        } else {
            $this->filter = null;
        }

        if (isset($_GET['status'])) {
            $this->status = $_GET['status'];
        } else {
            $this->status = null;
        }

        if (isset($_GET['search'])) {
            $this->search = $_GET['search'];
        } else {
            $this->search = null;
        }

        $this->Task = new TaskModel();
       
    }

    public function processRequest()
    {
        //Method determination.
        switch ($this->requestMethod) {
            case 'GET':
                if ($this->Id) {
                    $response = $this->getTask($this->Id);
                } elseif ($this->search != null) {
                    $response = $this->searchTasks($this->search, $this->order, $this->filter);
                } else {
                    $response = $this->getAllTasks($this->order, $this->filter,$this->status);
                };
                break;
            case 'POST':
                if (!(Role::productOwner())) {
                    echo "you must be product owner to do this action";
                    exit;
                }
                $response = $this->createTaskFromRequest();
                break;
            case 'PUT':
                $response = $this->updateTaskFromRequest($this->Id);
                break;
            case 'DELETE':
                $response = $this->deleteTask($this->Id);
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

    //Searching Tasks in specific order and filter.
    private function searchTasks($search, $order, $filter)
    {
        if ($order === 'DESC' || $order === 'ASC') {
            $result = $this->Task->search($search, $order, $filter);
            $response['status_code_header'] = 'HTTP/1.1 200 OK';
            $response['body'] = json_encode($result);
        } else {
            $response['status_code_header'] = 'HTTP/1.1 200 OK';
            $response['body'] = "you cant order tasks in " . $order . " order";
        }

        return $response;
    }

    //Getting all tasks in specific order and filter.
    private function getAllTasks($order, $filter,$status)
    {
        if ($order === 'DESC' || $order === 'ASC') {
            $result = $this->Task->findAll($order, $filter,$status);
            $response['status_code_header'] = 'HTTP/1.1 200 OK';
            $response['body'] = json_encode($result);
        } else {
            $response['status_code_header'] = 'HTTP/1.1 200 OK';
            $response['body'] = "you cant order tasks in " . $order . " order";
        }
        return $response;
    }

    //Getting specific task.
    private function getTask($id)
    {
        $result = $this->Task->find($id);
        if (!$result) {
            return $this->notFoundResponse();
        }

        if ($result[0]['id']) {
            $_SESSION['task'] = TRUE;
            $_SESSION['task_id'] = $result[0]['id'];
            $_SESSION['task_name'] = $result[0]['task_name'];
            $_SESSION['task_description'] = $result[0]['description'];
            $_SESSION['task_image'] = $result[0]['image'];
            $_SESSION['task_due_date'] = $result[0]['due_date'];
           
        }

        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }

    //Creating task.
    private function createTaskFromRequest()
    {

        if (!$this->validateTask()) {
            return $this->unprocessableEntityResponse();
        }

        //Checking that the image file is uploaded or not and also checking for errors. 
        if (isset($_FILES['task_image']) && $_FILES['task_image']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['task_image']['tmp_name'];
            $fileName = $_FILES['task_image']['name'];
            $fileSize = $_FILES['task_image']['size'];
            $fileType = $_FILES['task_image']['type'];
            $fileNameCmps = explode(".", $fileName);
            $fileExtension = strtolower(end($fileNameCmps));

            $newFileName = md5(time() . $fileName) . '.' . $fileExtension;

            // check if file has one of the following extensions
            $allowedfileExtensions = array('jpg', 'gif', 'png');

            if (in_array($fileExtension, $allowedfileExtensions)) {
                // directory in which the uploaded file will be moved
                $uploadFileDir = '../uploads/';
                $dest_path = $uploadFileDir . $newFileName;
                
                if (move_uploaded_file($fileTmpPath, $dest_path)) {
                    $_SESSION['task_image'] = $newFileName;
                    $message = 'File is successfully uploaded.';
                } else {
                    $message = 'There was some error moving the file to upload directory. Please make sure the upload directory is writable by web server.';
                }
            } else {
                $message = 'Upload failed. Allowed file types: ' . implode(',', $allowedfileExtensions);
            }
        } else {
            $message = 'There is some error in the image file  Please check the following error.<br>';
            $message .= 'Error:' . $_FILES['task_image']['error'];
            exit($message);
        }


        $this->Task->insert();
        $response['status_code_header'] = 'HTTP/1.1 201 Created';
        $response['body'] = "Task created successfully";



        return $response;
    }

    //Updating specific task.
    private function updateTaskFromRequest($id)
    {

        $result = $this->Task->find($id);
        if (!$result) {
            return $this->notFoundResponse();
        }

        
        if (Role::developer() && !($result[0]['status_name'] === 'to-do' || $result[0]['status_name'] === 'in-progress')) {
            exit("you don't have access to do this action !!");
        } elseif (Role::tester() && $result[0]['status_name'] != 'testing') {
            exit("you don't have access to do this action");
        }



        if (Role::developer() && ($_POST['task_status_name'] != 'in-progress' && $_POST['task_status_name'] != 'testing')) {
            exit("you can't perform this action");
        }
        if (Role::tester() && $_POST['task_status_name'] != 'dev-review') {
            exit("you can't perform this action");
        }



        if (!$this->validateTask()) {
            return $this->unprocessableEntityResponse();
        }

        //Checking that the image file is uploaded or not and also checking for errors. 
        if (isset($_FILES['task_image']) && $_FILES['task_image']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['task_image']['tmp_name'];
            $fileName = $_FILES['task_image']['name'];
            $fileSize = $_FILES['task_image']['size'];
            $fileType = $_FILES['task_image']['type'];
            $fileNameCmps = explode(".", $fileName);
            $fileExtension = strtolower(end($fileNameCmps));

            $newFileName = md5(time() . $fileName) . '.' . $fileExtension;

            // check if file has one of the following extensions
            $allowedfileExtensions = array('jpg', 'gif', 'png');

            if (in_array($fileExtension, $allowedfileExtensions)) {
                // directory in which the uploaded file will be moved
                $uploadFileDir = '../uploads/';
                $dest_path = $uploadFileDir . $newFileName;

                if (move_uploaded_file($fileTmpPath, $dest_path)) {
                    $_SESSION['task_image'] = $newFileName;
                    $message = 'File is successfully uploaded.';
                } else {
                    $message = 'There was some error moving the file to upload directory. Please make sure the upload directory is writable by web server.';
                }
            } else {
                $message = 'Upload failed. Allowed file types: ' . implode(',', $allowedfileExtensions);
            }
        } else {
            $message = 'There is some error in the image file  Please check the following error.<br>';
            $message .= 'Error:' . $_FILES['task_image']['error'];
            exit($message);
        }

        $this->Task->update($id);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = "update successfully";
        return $response;
    }

    //Deleting specific task.
    private function deleteTask($id)
    {
        $result = $this->Task->find($id);
        if (!$result) {
            return $this->notFoundResponse();
        }
        $this->Task->delete($id);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = "Task Deleted";
        return $response;
    }

    //Checking that the user filled all required fields or not.
    private function validateTask()
    {

        if (!isset($_POST['task_name'])) {
            return false;
        }

        if (!isset($_POST['task_due_date'])) {
            return false;
        }
      
        return true;
    }

    //This function returning response if validateTask function return false.
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
