<?php

use Src\Controller\BoardController;
use Src\Controller\LabelController;
use Src\Controller\LogController;
use Src\Controller\LoginController;
use Src\Controller\StatusController;
use Src\Controller\Task_LabelController;
use Src\Controller\TaskController;
use Src\Controller\UserController;
use Src\Guard\Role;

//loading bootstrap file.
require "../bootstrap.php";

//starting session.
session_start();

//header values.
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");




$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode('/', $uri);

$requestMethod = $_REQUEST["_method"] ?? $_SERVER["REQUEST_METHOD"];

$Id = null;
if (isset($uri[2])) {
    $Id = (int) $uri[2];
}


//Login API. 
if ($uri[1] == 'login') {
    if (authenticated()) {
        exit("you are already logged in");
    }
    if ($requestMethod == "POST") {
        $_POST['email'] = htmlspecialchars(addslashes($_POST['email']));
        $_POST['password'] = htmlspecialchars(addslashes($_POST['password']));
        $controller = new LoginController($requestMethod);
        $controller->processRequest();
        exit;
    } else {
        exit("login form");
    }
}

//Register API.
if ($uri[1] == 'register') {
    if (authenticated()) {

        exit("you are already logged in");
    }
    if ($requestMethod === "POST") {

        $controller = new UserController($requestMethod, $Id);
        $controller->processRequest();
        exit;
    }
}

//Checking if the user logged in or not.
if (authenticated()) {

    //Boards API.
    if ($uri[1] == 'boards') {
        if (isset($uri[2])) {
            $Id = (int) $uri[2];

            if (is_int($Id) && $requestMethod != "POST") {

                $controller = new BoardController($requestMethod, $Id);
                $controller->processRequest();
                exit;
            } else {
                exit("You should select specific board first");
            }
        }
        $controller = new BoardController($requestMethod, $Id);
        $controller->processRequest();
        exit;
    }

    //Statuses API.
    if ($uri[1] == 'statuses') {
        if (isset($uri[2]) && $requestMethod != 'GET') {
            if ($requestMethod == ['DELETE'] && !(Role::productOwner())) {
                echo "you must be product owner to do this action";
                exit;
            }
            $controller = new StatusController($requestMethod, $Id);
            $controller->processRequest();
            exit;
        }

        $controller = new StatusController($requestMethod, $Id);
        $controller->processRequest();
        exit;
    }

    //Tasks API.
    if ($uri[1] == 'tasks') {
        if (isset($uri[2]) && $requestMethod != 'GET') {
            if ($requestMethod == 'DELETE' && !(Role::productOwner())) {
                echo "you must be product owner to do this action";
                exit;
            }

            $controller = new TaskController($requestMethod, $Id);
            $controller->processRequest();
            exit;
        }


        $controller = new TaskController($requestMethod, $Id);
        $controller->processRequest();
        exit;
    }

    //Task history API.
    if ($uri[1] == 'history') {
        $controller = new LogController($requestMethod, $_GET['task_id']);
        $controller->processRequest();
        exit;
    }

    //Binding labels to tasks.
    if ($uri[1] == 'bind_label_to_task' && ($requestMethod != 'PUT')) {
        $Task_Label_Id = null;
        if (isset($uri[2])) {
            $Task_Label_Id = (int) $uri[2];
        }

        $controller = new Task_LabelController($requestMethod, $Task_Label_Id);
        $controller->processRequest();
        exit;
    }

    //Users API.
    if ($uri[1] == 'users') {
        if ($requestMethod != "POST") {


            if (Role::productOwner() && $requestMethod != 'PUT') {

                $controller = new UserController($requestMethod, $Id);
                $controller->processRequest();
            } else {

                $controller = new UserController($requestMethod, $_SESSION['user_id']);
                $controller->processRequest();
            }
        }
    }

    //Logs API.
    if ($uri[1] == 'logs') {
        if (Role::productOwner()) {

            $controller = new LogController($requestMethod, $Id);
            $controller->processRequest();
        } else {
            $result = "you can't access this part";
            echo $result;
        }
    }

    //Labels API.
    if ($uri[1] == 'labels') {

        $controller = new LabelController($requestMethod, $Id);
        $controller->processRequest();
    }

    //Logout API.
    if ($uri[1] == 'logout') {
        if ($requestMethod === "POST") {


            if ($_SESSION) {
                session_destroy();
                $result = "logout successfully";
                echo $result;
            }


            exit;
        }
    }
} else {
    echo "please log in first";
}
