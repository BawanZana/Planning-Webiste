<?php
namespace Src\Table;

class UserModel {

    private $db = null;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function findAll()
    {
        $statement = "
            SELECT 
                id,name, email,user_type
            FROM
                users;
        ";

        try {
            $statement = $this->db->query($statement);
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            return $result;
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }

    public function find($id)
    {
        $statement = "
            SELECT 
            id,name, email,user_type
            FROM
            users
            WHERE id = ?;
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array($id));
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            return $result;
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }    
    }

    public function insert(Array $input)
    {
        $statement = "
            INSERT INTO users 
                (name, email,password,user_type)
            VALUES
                (:name, :email, :password,:user_type);
        ";

       
        try {
            $statement = $this->db->prepare($statement);

            $input['email']=htmlspecialchars(addslashes($input['email']) );
            $input['password']=htmlspecialchars(addslashes($input['password']));
            if (!filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
                exit('Email is not valid!');
            }
            if (strlen($input['password']) > 20 || strlen($input['password']) < 5) {
                exit('Password must be between 5 and 20 characters long!');
            }
    

            $statement->execute(array(
                'name' => $input['name'],
                'email'  => $input['email'],
                'password' => password_hash($input['password'], PASSWORD_DEFAULT),
                'user_type' => $input['user_type'],
                
            ));
            return $statement->rowCount();
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }    
    }

    public function update($id, Array $input)
    {
        date_default_timezone_get();
        $statement = "
            UPDATE users
            SET 
                name = :name,
                email  = :email,
                password = :password,
                user_type = :user_type,
                updated_at=:updated_at
            WHERE id = :id;
        ";

        try {
            $statement = $this->db->prepare($statement);

            $input['email']=htmlspecialchars(addslashes($input['email']) );
            $input['password']=htmlspecialchars(addslashes($input['password']));
            if (!filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
                exit('Email is not valid!');
            }
            if (strlen($input['password']) > 20 || strlen($input['password']) < 5) {
                exit('Password must be between 5 and 20 characters long!');
            }
            date_default_timezone_set("Asia/Baghdad");
            $statement->execute(array(
                'id' => (int) $id,
                'name' => $input['name'],
                'email'  => $input['email'],
                'password' => password_hash($input['password'], PASSWORD_DEFAULT),
                'user_type' => $_SESSION['user_type'],
                'updated_at' => date('Y-m-d h:i:s'),
                
            ));
            return $statement->rowCount();
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }    
    }

    public function delete($id)
    {
        $statement = "
            DELETE FROM users
            WHERE id = :id;
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array('id' => $id));
            return $statement->rowCount();
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }    
    }
}