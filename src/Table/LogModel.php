<?php
namespace Src\Table;

class LogModel {

    private $db = null;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function findAll()
    {
        $statement = "
            SELECT 
                id,task, user, action
            FROM
                logs;
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
        // var_dump("hi");
        $statement = "
            SELECT 
            logs.id,logs.task,logs.user,logs.action
            FROM
            logs
            inner join tasks on logs.task_id=tasks.id
            WHERE tasks.id = ?;
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

    public function insert($task_id,$task_name)
    {
        $statement = "
            INSERT INTO logs 
                (task_id, task,user,action)
            VALUES
                (:task_id, :task_name, :user_name,:action);
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array(
                'task_id' => $task_id,
                'task'  => $task_name,
                'user' => $_SESSION['user_name'],
                'action' => 'action',

            ));
            return $statement->rowCount();
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }    
    }

    public function update($id, Array $input)
    {
        
        $statement = "
            UPDATE logs
            SET 
            task_id = :task_id,
            task  = :task,
            user = :user,
            action = :action
            WHERE id = :id;
        ";
        date_default_timezone_set("Asia/Baghdad");
        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array(
                'id' => (int) $id,
                'task_id' =>  $_SESSION['task_id'],
                'task'  => $input['task'],
                'user' => $input['user'],
                'action' => $input['action'],
              
            ));
            return $statement->rowCount();
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }    
    }

    public function delete($id)
    {
        $statement = "
            DELETE FROM logs
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