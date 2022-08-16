<?php
namespace Src\Controller;

use Src\Table\Login;

// session_start();
class LoginController {
    private $db;
    private $requestMethod;
    private $email;
    private $password;
//     $name=$_POST['name'];
// $password=$_POST['password'];

    private $Login;

    public function __construct($db, $requestMethod)
    {
        $this->db = $db;
        $this->requestMethod = $requestMethod;
        
        $this->Login = new Login($db);

        
    }
    
    public function processRequest()
    {
        switch ($this->requestMethod) {
            
            case 'POST':
                $this->email=$_POST['email'];
                $this->password=$_POST['password'];
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

    private function LoginFromRequest($email)
    {

        if ( !isset($_POST['email'], $_POST['password']) ) {
           
            exit('Please fill both the email and password fields!');
        }
        $result = $this->Login->find($email); 
        
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);


       
    
        
            
               
                if (password_verify($this->password,$result[0]['password']))  {

                    session_regenerate_id();
                    $_SESSION['logged_in'] = TRUE;
                    $_SESSION['user_name'] = $result[0]['name'];
                    $_SESSION['email'] = $result[0]['email'];
                    $_SESSION['user_id'] = $result[0]['id'];
                    $_SESSION['user_type'] = $result[0]['user_type'];
                    // $response= 'Welcome ' . $_SESSION['name'] . 'you are'.$_SESSION['user_type'].'!';
                    return $response;
                } else {
                    // Incorrect password
                    echo 'Incorrect username and/or password!';
                }
            
    
            
        

      
    }

    private function notFoundResponse()
    {
        $response['status_code_header'] = 'HTTP/1.1 404 Not Found';
        $response['body'] = null;
        return $response;
    }
   
}


