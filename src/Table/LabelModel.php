<?php

namespace Src\Table;

class LabelModel extends BaseModel
{

    //Finding all labels.
    public function findAll()
    {
        $statement = "
            SELECT 
                labels.id,labels.name as label_name,users.name as user_name
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

    //Finding specific label.
    public function find($id)
    {
        $statement = "
        SELECT 
            labels.id,labels.name as label_name,users.name as user_name
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

    //Inserting new label.
    public function insert(array $input)
    {
        $statement = "
            INSERT INTO labels 
                (name,user_id)
            VALUES
                (:name,:user_id);
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array(
                'name' => $input['name'],
                'user_id' => $_SESSION['user_id'],

            ));
            return $statement->rowCount();
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }

    //Updating specific label.
    public function update($id, array $input)
    {

        $statement = "
            UPDATE labels
            SET 
            name = :name,
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
                'user_id' => $_SESSION['user_id'],
                'updated_at' => date('Y-m-d h:i:s'),

            ));
            return $statement->rowCount();
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }

    //Deleting specific label.
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
