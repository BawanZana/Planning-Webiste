<?php

use Src\Controller\BoardController;
use Src\Controller\LabelController;
use Src\Controller\LogController;
use Src\Controller\LoginController;
use Src\Controller\StatusController;
use Src\Controller\Task_LabelController;
use Src\Controller\TaskController;
use Src\Controller\UserController;
use Src\middlware\Role;


require "../bootstrap.php";
// use Src\Controller\TaskController;
session_start();

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


$role=new Role();

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode( '/', $uri );
$requestMethod = $_SERVER["REQUEST_METHOD"];
$Id = null;
if (isset($uri[2])) {
    $Id = (int) $uri[2];
}



if ($uri[1] == 'login') {
    if($_SESSION['logged_in'])
    {
        
        exit ("you are already logged in");
    }
    if($requestMethod == "POST")
    {
        $_POST['email']=htmlspecialchars(addslashes($_POST['email']) );
        $_POST['password']=htmlspecialchars(addslashes($_POST['password']));
        $controller = new LoginController($dbConnection, $requestMethod);
        $controller->processRequest();
        exit;
    }
    else
    {
        exit("login form");
    }
   
}


if($uri[1] == 'logout')
{
    if ($requestMethod === "POST") {

        
        if($_SESSION)
        {   
            session_destroy();
            $result="logout successfully";
            echo $result;
        }
        
        
        exit;
    }
}


if($uri[1] == 'register')
{   if($_SESSION['logged_in'])
    {
        
        exit("you are already logged in");
    }
    if ($requestMethod === "POST") {

        $controller = new UserController($dbConnection, $requestMethod, $Id);
        $controller->processRequest();
        exit;
    }
}


if($_SESSION['logged_in'])
{
    
    
     
    
    if ($uri[1] == 'boards') {
        
        
        if(isset($uri[2]))
        {
           
            $Id = (int) $uri[2];
            
            if(is_int($Id) && $requestMethod !="POST")
            {
                
                $controller = new BoardController($dbConnection, $requestMethod, $Id);
                $controller->processRequest();
                exit;
            }
            else
            {
                exit("You should select specific board first");
            }
        }
        $controller = new BoardController($dbConnection, $requestMethod, $Id);
        $controller->processRequest();
        exit;
    }

    if ($uri[1] == 'statuses') {
              
     
        if(isset($uri[2]) && $requestMethod!='GET')
        {
            if($requestMethod == ['DELETE'] && !($role->productOwner()))
            {
                echo "you must be product owner to do this action";
                exit;
            }
            $controller = new StatusController($dbConnection, $requestMethod, $Id);
            $controller->processRequest();
            exit;
        }    
        
        
        $controller = new StatusController($dbConnection, $requestMethod, $Id);
        $controller->processRequest();
        exit;
        
    }

    if ($uri[1] == 'tasks') {
        if(isset($uri[2]) && $requestMethod!='GET')
        {
           if($requestMethod == 'DELETE' && !($role->productOwner()))
            {
                echo "you must be product owner to do this action";
                exit;
            }

            $controller = new TaskController($dbConnection, $requestMethod, $Id);
            $controller->processRequest();
            exit;
        }
        
        
        $controller = new TaskController($dbConnection, $requestMethod, $Id);
        $controller->processRequest();
        exit;
       
    }

    if($uri[1] == 'history')
    {
        $controller = new LogController($dbConnection, $requestMethod, $_GET['task_id']);
        $controller->processRequest();
        exit;
    }
    if($uri[1] == 'bind_label_to_task' && ($requestMethod!='PUT'))
    {
        $Task_Label_Id = null;
        if (isset($uri[2])) {
            $Task_Label_Id = (int) $uri[2];
        }
        
        $controller = new Task_LabelController($dbConnection, $requestMethod, $Task_Label_Id);
        $controller->processRequest();
        exit;
    }

    if($uri[1] == 'users')
    {
        if ($requestMethod != "POST") {

            
            if ($role->productOwner() && $requestMethod!='PUT') {
               
                $controller = new UserController($dbConnection, $requestMethod, $Id);
                $controller->processRequest();
                
            }
            else
            {

                $controller = new UserController($dbConnection, $requestMethod, $_SESSION['user_id']);
                $controller->processRequest();
            }
            
        
        }
    }

    if($uri[1] == 'logs')
    {
        if ($role->productOwner()) {
            
                $controller = new LogController($dbConnection, $requestMethod, $Id);
                $controller->processRequest();
            
        }
        else
        {
            $result="you can't access this part";
            echo $result;    
        } 
    }

    if($uri[1] == 'labels')
    {
        
        $controller = new LabelController($dbConnection, $requestMethod, $Id);
        $controller->processRequest();
            
       
    }
 
}
else
{
    echo "please log in first";
}


