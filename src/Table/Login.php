<?php
namespace Src\Table;

class Login {
    private $db = null;

    public function __construct($db)
    {
        $this->db = $db;
    }

public function find($email)
    {
        
        $statement = "
            SELECT 
                id,name, email, user_type, password
            FROM
            users
            WHERE (email = ?);
        ";

        try {
            $statement = $this->db->prepare($statement);

           
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                exit('Email is not valid!');
            }
          
    
            $statement->execute(array($email));
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            return $result;
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }    
    }


}