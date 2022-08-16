<?php
namespace Src\Controller;

use Src\middlware\Role;
use Src\TableGateways\LogModel;
use Src\Table\TaskModel;

// use Src\TableGateways\TaskModel;

class TaskController {

    private $db;
    private $requestMethod;
    private $Id;
    private $search;
    private $order;
    private $Task;
    private $filter;
    private $role;
    
    public function __construct($db, $requestMethod, $Id)
    {
        $this->db = $db;
        $this->requestMethod = $requestMethod;
        $this->Id = $Id;
        if(isset($_GET['order']))
        {
            $this->order=$_GET['order'];
        }
        else
        {
            $this->order='DESC';
        }

        if(isset($_GET['filter']))
        {
            $this->filter=$_GET['filter'];
        }
        else
        {
            $this->filter='';
        }
        if(isset($_GET['search']))
        {
            $this->search=$_GET['search'];
        }
        else
        {
            $this->search=null;
        }
        
        $this->Task = new TaskModel($db);
        $this->role= new Role();

        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $uri = explode( '/', $uri );
        if(isset($uri[2]))
        { 
            
            if($this->requestMethod === 'POST')
            {
               
               $this->requestMethod="PUT";
            }
        }
    }

    public function processRequest()
    {
        switch ($this->requestMethod) {
            case 'GET':
                if ($this->Id) {
                    $response = $this->getTask($this->Id);
                }elseif($this->search !=null){
                    $response = $this->searchTasks($this->search,$this->order,$this->filter);
                } 
                else {
                    $response = $this->getAllTasks($this->order,$this->filter);
                };
                break;
            case 'POST':
                if(!($this->role->productOwner()))
                {
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
            // header("Location:/views/show.php");
        }

        
    }

    private function searchTasks($search,$order,$filter)
    {
        if($order=== 'DESC' || $order==='ASC')
        {
            $result = $this->Task->search($search,$order,$filter);
            $response['status_code_header'] = 'HTTP/1.1 200 OK';
            $response['body'] = json_encode($result);   
        }
        else
        {
            $response['status_code_header'] = 'HTTP/1.1 200 OK';
            $response['body']="you cant order tasks in ".$order." order";
        }
        
        return $response;
    }

    private function getAllTasks($order,$filter)
    {
        if($order=== 'DESC' || $order==='ASC')
        {
            $result = $this->Task->findAll($order,$filter);
            $response['status_code_header'] = 'HTTP/1.1 200 OK';
            $response['body'] = json_encode($result);
        }
        else
        {
            $response['status_code_header'] = 'HTTP/1.1 200 OK';
            $response['body']="you cant order tasks in ".$order." order";
        }
        return $response;
    }

    private function getTask($id)
    {
        $result = $this->Task->find($id);
        if (! $result) {
            return $this->notFoundResponse();
        }

        if($result[0]['id'])
        {
            $_SESSION['task']=TRUE;
            $_SESSION['task_id']=$result[0]['id'];
            $_SESSION['task_name']=$result[0]['task_name'];
            $_SESSION['task_description']=$result[0]['description'];
            $_SESSION['task_image']=$result[0]['image'];
            $_SESSION['task_due_date']=$result[0]['due_date'];
            $_SESSION['task_flag']=$result[0]['flag'];
            
        }

        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }

    private function createTaskFromRequest()
    {
        // var_dump($_POST['name']);
        // exit;
        
        // var_dump($input);
        // exit;

        if (! $this->validateTask()) {
            return $this->unprocessableEntityResponse();
        }

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
        
            if (in_array($fileExtension, $allowedfileExtensions))
            {
                // directory in which the uploaded file will be moved
                $uploadFileDir = '../uploads/';
                $dest_path = $uploadFileDir. $newFileName;
                // var_dump($dest_path);
                if(move_uploaded_file($fileTmpPath, $dest_path)) 
                {
                    $_SESSION['task_image']=$newFileName;
                    $message ='File is successfully uploaded.';
                }
                else
                {
                    $message = 'There was some error moving the file to upload directory. Please make sure the upload directory is writable by web server.';
                }
                }
                else
                {
                    $message = 'Upload failed. Allowed file types: ' . implode(',', $allowedfileExtensions);
                }
            }
            else
            {
                $message = 'There is some error in the image file  Please check the following error.<br>';
                $message .= 'Error:' . $_FILES['task_image']['error'];
                exit($message);
            }
            
        
        $this->Task->insert();
        $response['status_code_header'] = 'HTTP/1.1 201 Created';
        $response['body'] = "Task created successfully";

        

        return $response;
    }

    private function updateTaskFromRequest($id)
    {
        
        $result = $this->Task->find($id);
        if (! $result) {
            return $this->notFoundResponse();
        }
        
        // var_dump($result[0]['status_name']);
        if ($this->role->developer() && !($result[0]['status_name'] ==='to-do' || $result[0]['status_name'] ==='in-progress')) {
            exit("you don't have access to do this action !!");
        }elseif ($this->role->tester() && $result[0]['status_name'] !='testing') {
            exit("you don't have access to do this action");
        }
        
        
        
        if($this->role->developer() && ($_POST['task_status_name'] !='in-progress' && $_POST['task_status_name'] !='testing'))
        {
            exit("you can't perform this action");
        }
        if($this->role->tester() && $_POST['task_status_name'] !='dev-review')
        {
            exit("you can't perform this action");
        }

        

        if (! $this->validateTask()) {
            return $this->unprocessableEntityResponse();
        }

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
        
            if (in_array($fileExtension, $allowedfileExtensions))
            {
                // directory in which the uploaded file will be moved
                $uploadFileDir = '../uploads/';
                $dest_path = $uploadFileDir. $newFileName;
               
                if(move_uploaded_file($fileTmpPath, $dest_path)) 
                {
                    $_SESSION['task_image']=$newFileName;
                    $message ='File is successfully uploaded.';
                }
                else
                {
                    $message = 'There was some error moving the file to upload directory. Please make sure the upload directory is writable by web server.';
                }
                }
                else
                {
                    $message = 'Upload failed. Allowed file types: ' . implode(',', $allowedfileExtensions);
                }
            }
            else
            {
                $message = 'There is some error in the image file  Please check the following error.<br>';
                $message .= 'Error:' . $_FILES['task_image']['error'];
                exit($message);
            }
            
        $this->Task->update($id);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = "update successfully";
        return $response;
    }

    private function deleteTask($id)
    {
        $result = $this->Task->find($id);
        if (! $result) {
            return $this->notFoundResponse();
        }
        $this->Task->delete($id);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = null;
        return $response;
    }

    private function validateTask()
    { 
        
        // var_dump($GLOBALS);
        if (! isset($_POST['task_name'])) {
            return false;
        }
        

        if (! isset($_POST['task_due_date'])) {
            return false;
        }
        // if (! isset($input['flag'])) {
        //     return false;
        // }
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