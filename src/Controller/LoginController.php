<?php

namespace Src\Controller;

use Src\Table\UserModel;


class LoginController
{
    private $db;
    private $requestMethod;
    private $email;
    private $password;
    
    private $user;

    public function __construct($requestMethod)
    {
       
        $this->requestMethod = $requestMethod;

        $this->user = new UserModel();
    }

    public function processRequest()
    {
        //Method determination.
        switch ($this->requestMethod) {

            case 'POST':
                $this->email = $_POST['email'];
                $this->password = $_POST['password'];
                $response = $this->LoginFromRequest($this->email);
                break;
            default:
                $response = $this->notFoundResponse();
                break;
        }
        header($response['status_code_header']);
        if ($response['body']) {
            echo "login";
        }
    }

    //Checking email and password for login. 
    private function LoginFromRequest($email)
    {

        if (!isset($_POST['email'], $_POST['password'])) {

            exit('Please fill both the email and password fields!');
        }
        $result = $this->user->findEmail($email);

        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);


        if (password_verify($this->password, $result[0]['password'])) {

            session_regenerate_id();
            $_SESSION['logged_in'] = TRUE;
            $_SESSION['user_name'] = $result[0]['name'];
            $_SESSION['email'] = $result[0]['email'];
            $_SESSION['user_id'] = $result[0]['id'];
            $_SESSION['user_type'] = $result[0]['user_type'];
            
            return $response;
        } else {
            // Incorrect password
            echo 'Incorrect username and/or password!';
        }
    }

    //This function returning response if the requested method not matching any methods in the above switch case.
    private function notFoundResponse()
    {
        $response['status_code_header'] = 'HTTP/1.1 404 Not Found';
        $response['body'] = "404 Not Found";
        return $response;
    }
}
