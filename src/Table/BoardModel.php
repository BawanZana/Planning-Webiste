<?php

namespace Src\Table;



class BoardModel extends BaseModel
{

    //Finding all boards.
    public function findAll()
    {
        $statement = "
            SELECT 
            boards.id,boards.name as board_name, users.name as user_name
            FROM
                boards
            inner join users on boards.user_id = users.id order by boards.id;    
        ";

        try {
            $statement = $this->db->query($statement);
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            return $result;
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }

    //Finding specific board.
    public function find($id)
    {
        $statement = "
            SELECT 
            boards.id,boards.name as board_name, users.name as user_name
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

    //Inserting specific board.
    public function insert(array $input)
    {
        $statement = "
            INSERT INTO boards 
                (name,user_id)
            VALUES
                (:name,:user_id);
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array(
                'name' => $input['name'],
                'user_id'  => $_SESSION['user_id'],

            ));
            return $statement->rowCount();
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }

    //Updating specific board.
    public function update($id, array $input)
    {

        $statement = "
            UPDATE boards
            SET 
                name = :name,
                user_id  = :user_id,
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
                'updated_at' => date('Y-m-d h:i:s'),

            ));
            return $statement->rowCount();
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }

    //Deleting specific board.
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
