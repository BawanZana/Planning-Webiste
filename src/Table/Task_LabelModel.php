<?php
namespace Src\Table;

class Task_LabelModel {

    private $db = null;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function findAll()
    {
        $statement = "
            SELECT 
            tasks_labels.id,tasks.name as task_name,labels.name as label_name
            FROM
                tasks_labels
            inner join labels on  tasks_labels.label_id=labels.id 
            inner join tasks on  tasks_labels.task_id=tasks.id  
            WHERE tasks_labels.task_id='".$_GET['task_id']."' ;
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
        tasks_labels.id,tasks.name as task_name,labels.name as label_name
        FROM
            tasks_labels
        inner join labels on  tasks_labels.label_id=labels.id 
        inner join tasks on  tasks_labels.task_id=tasks.id
            WHERE tasks_labels.id = ?;
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
            INSERT INTO tasks_labels
                (task_id, label_id)
            VALUES
                (:task_id, :label_id);
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array(
                'task_id' => $input['task_id'],
                'label_id'  => $input['label_id'],
                
            ));
            return $statement->rowCount();
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }    
    }

    

    public function delete($id)
    {
        $statement = "
            DELETE FROM tasks_labels
            WHERE id = ?;
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array($id));
            return $statement->rowCount();
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }    
    }
}