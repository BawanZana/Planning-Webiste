<?php
namespace Src\Table;

class TaskModel {

    private $db = null;

    public function __construct($db)
    {
        $this->db = $db;
    }


    public function search($name,$order,$filter)
    {
        if($filter != null){$filter="WHERE statuses.name='".$filter."'";}
        
        $statement = "
            SELECT 
            tasks.id,tasks.name as task_name,tasks.image,tasks.due_date ,tasks.description,statuses.name as status_name, users.name as user_name, tasks.flag
            FROM
                ((tasks
            inner join users on tasks.user_id=users.id)
            inner join statuses on tasks.status_id=statuses.id) 
            WHERE tasks.name LIKE '%".$name."%'".$filter."
            ORDER BY tasks.created_at ".$order.";
        ";

        try {
            $statement = $this->db->query($statement);
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            return $result;
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }

    public function findAll($order,$filter)
    {
        if($filter != null){$filter="WHERE statuses.name='".$filter."'";}
       
        $statement = "
            SELECT 
            tasks.id,tasks.name as task_name,tasks.image,tasks.due_date, tasks.description,statuses.name as status_name, users.name as user_name, tasks.flag
            FROM
                ((tasks
            inner join users on tasks.user_id=users.id)
            inner join statuses on tasks.status_id=statuses.id)
            ".$filter."
            ORDER BY tasks.created_at ".$order.";
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
            tasks.id,tasks.name as task_name, tasks.description,tasks.image,tasks.due_date,statuses.name as status_name, users.name as user_name, tasks.flag
            FROM
                ((((tasks
            inner join users on tasks.user_id=users.id)
            inner join statuses on tasks.status_id=statuses.id)
            left join tasks_labels on tasks.id=tasks_labels.task_id)
            left join labels on tasks_labels.label_id=labels.id)
            WHERE tasks.id = ?;
        ";

        $ID=(int)$_POST['status_id'];
        

        $statement1 = "
        SELECT 
            name 
        FROM
            statuses
        WHERE id = ".$ID.";
        ";
        
        try {
            $statement1 = $this->db->prepare($statement1);
            $statement1->execute(array());
            
            $result1 = $statement1->fetchAll(\PDO::FETCH_ASSOC);
            $_POST['task_status_name']=$result1[0]['name'];
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }  
        
        
        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array($id));
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            return $result;
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }    
    }

    public function insert()
    {
        $statement = "
            INSERT INTO tasks 
                (name,description,flag,due_date,image,status_id,user_id)
            VALUES
                (:name,:description,:flag,:due_date,:image,:status_id,:user_id);
        ";
        
        try {
            // var_dump(date('d/m/Y' ,strtotime ($_POST['task_due_date'])));
            $statement = $this->db->prepare($statement);
            $statement->execute(array(
                'name' => $_POST['task_name'],
                'description'  => $_POST['task_description'],
                'image'  =>$_SESSION['task_image'],
                'due_date'  => date('Y-m-d' ,strtotime ($_POST['task_due_date'])),
                'status_id' => $_POST['status_id'],
                'user_id' => $_SESSION['user_id'],
                'flag' => (int)$_POST['task_flag'] ?? 1,
            ));
            
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }    

        $statement_task = "
            SELECT
            id as task_id,name as task
            FROM
               tasks
           
            ORDER BY task_id DESC
            LIMIT 1;
        ";

        try {
            $statement_task = $this->db->query($statement_task);
            $result = $statement_task->fetchAll(\PDO::FETCH_ASSOC);
            // return $result;
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }

        $statement_log = "
            INSERT INTO logs 
                (task_id,task, user,action)
            VALUES
                (:task_id,:task, :user, :action);
        ";

        try {
            $statement_log = $this->db->prepare($statement_log);
            $statement_log->execute(array(
                'task_id' => $result[0]['task_id'],
                'task' => $result[0]['task'],
                'user'  => $_SESSION['user_name'],
                'action' => 'insert',
                
            ));
            // return $statement_log->rowCount();
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }    
        return $statement->rowCount();
    }

    public function update($id)
    {
        
        $statement_task = "
        SELECT 
            id,name
        FROM
        tasks
        WHERE id=".$id.";
        ";
        try {
            $statement_task = $this->db->query($statement_task);
            $result = $statement_task->fetchAll(\PDO::FETCH_ASSOC);
            $name=$result[0]['name'];
            $ID=$result[0]['id'];
            // return $statement_log->rowCount();
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }  
       
        $statement_log = "
        INSERT INTO logs 
            (task_id,task, user,action)
        VALUES
            (:task_id,:task, :user, :action);
        ";
        try {
            $statement_log = $this->db->prepare($statement_log);
            $statement_log->execute(array(
                'task_id' => $ID,
                'task' => $name,
                'user'  => $_SESSION['user_name'],
                'action' => 'update',
                
            ));
            // return $statement_log->rowCount();
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }  
        
        
        $statement = "
            UPDATE tasks
            SET 
                
                name = :name,
                description  = :description,
                image = :image,
                due_date = :due_date,
                status_id = :status_id,
                user_id = :user_id,
                flag = :flag,
                updated_at = :updated_at
            WHERE id = :id;
        ";

        date_default_timezone_set("Asia/Baghdad");
        
        try {
            $statement = $this->db->prepare($statement);
            
            $statement->execute(array(
                
                'id' => (int) $id,
                'name' => $_POST['task_name'],
                'description'  => $_POST['task_description'],
                'image'  => $_SESSION['task_image'],
                'due_date'  => date('Y-m-d' ,strtotime ($_POST['task_due_date'])),
                'status_id' => (int)$_POST['status_id'],
                'user_id' => $_SESSION['user_id'],
                'flag' => $_POST['task_flag'] ?? 1,
                'updated_at' => date('Y-m-d h:i:s'),
                
            ));
            
            return $statement->rowCount();
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }  
        
        
        
    }

    public function delete($id)
    {
        $statement_task = "
        SELECT 
            name
        FROM
        tasks
        WHERE id=".$id.";
        ";
        try {
            $statement_task = $this->db->query($statement_task);
            $result = $statement_task->fetchAll(\PDO::FETCH_ASSOC);
            $name=$result[0]['name'];
            // return $statement_log->rowCount();
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }  

        $statement_log = "
        INSERT INTO logs 
            (task_id,task, user,action)
        VALUES
            (:task_id,:task, :user, :action);
        ";
        try {
            $statement_log = $this->db->prepare($statement_log);
            $statement_log->execute(array(
                'task_id' => $id,
                'task' => $name,
                'user'  => $_SESSION['user_name'],
                'action' => 'delete',
                
            ));
            // return $statement_log->rowCount();
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }  

        $statement = "
            DELETE FROM tasks
            WHERE id = :id;
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array('id' => $id));
            
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }    

       
        return $statement->rowCount();
    }
}