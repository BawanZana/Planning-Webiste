<?php
namespace Src\Table;



class BoardModel {

    private $db = null;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function findAll()
    {
        $statement = "
            SELECT 
            boards.id,boards.name as board_name,boards.flag, users.name as user_name
            FROM
                boards
            inner join users on boards.user_id = users.id;    
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
            boards.id,boards.name as board_name,boards.flag, users.name as user_name
            FROM
            boards
            inner join users on boards.user_id = users.id
            WHERE boards.id = ?;
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
            INSERT INTO boards 
                (name,user_id, flag)
            VALUES
                (:name, :user_id, :flag);
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array(
                'name' => $input['name'],
                'user_id'  => $_SESSION['user_id'],
                'flag' => $input['flag'] ?? 1,
                
            ));
            return $statement->rowCount();
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }    
    }

    public function update($id, Array $input)
    {
        
        $statement = "
            UPDATE boards
            SET 
                name = :name,
                user_id  = :user_id,
                flag = :flag,
                updated_at=:updated_at
            WHERE id = :id;
        ";
        date_default_timezone_set("Asia/Baghdad");
        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array(
                'id' => (int) $id,
                'name' => $input['name'],
                'user_id'  => $_SESSION['user_id'],
                'flag' => $input['flag'] ?? 1,
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
            DELETE FROM boards
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