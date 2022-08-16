<?php
namespace Src\Table;

class LabelModel {

    private $db = null;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function findAll()
    {
        $statement = "
            SELECT 
                labels.id,labels.name as label_name, labels.flag, users.name as user_name
            FROM
                labels
            inner join users on labels.user_id=users.id    ;
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
            labels.id,labels.name as label_name, labels.flag, users.name as user_name
        FROM
            labels
        inner join users on labels.user_id=users.id
            WHERE labels.id = ?;
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
            INSERT INTO labels 
                (name, flag, user_id)
            VALUES
                (:name, :flag, :user_id);
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array(
                'name' => $input['name'],
                'flag'  => $input['flag'] ?? 1,
                'user_id' => $_SESSION['user_id'],
                
            ));
            return $statement->rowCount();
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }    
    }

    public function update($id, Array $input)
    {
        
        $statement = "
            UPDATE labels
            SET 
            name = :name,
            flag  = :flag,
            user_id = :user_id,
            updated_at=:updated_at
                
            WHERE id = :id;
        ";
        date_default_timezone_set("Asia/Baghdad");
        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array(
                'id' => (int) $id,
                'name' => $input['name'],
                'flag'  => $input['flag'] ?? 1,
                'user_id' => $_SESSION['user_id'],
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
            DELETE FROM labels
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