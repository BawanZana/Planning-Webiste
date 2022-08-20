<?php

namespace Src\Table;

class StatusModel extends BaseModel
{

    //Finding all statuses that belongs to specific board.
    public function findAll()
    {

        $statement = "
            SELECT 
            statuses.id,statuses.name as status_name, statuses.description,boards.name as board_name, users.name as user_name
            FROM
                ((statuses
            inner join boards on statuses.board_id = boards.id ) 
            inner join users on statuses.user_id = users.id)
            WHERE statuses.board_id=" . $_GET['board_id'] . ";
            
        ";

        try {
            $statement = $this->db->query($statement);
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);

            return $result;
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }

    //Finding specific status.
    public function find($id)
    {
        $statement = "
            SELECT 
            statuses.id,statuses.name as status_name, statuses.description,boards.name as board_name, users.name as user_name
            FROM
                ((statuses
            inner join boards on statuses.board_id = boards.id ) 
            inner join users on statuses.user_id = users.id)
            WHERE statuses.id = ?;
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

    //Inserting new status.
    public function insert(array $input)
    {
        $statement = "
            INSERT INTO statuses 
                (name, description,board_id, user_id)
            VALUES
                (:name, :description, :board_id,:user_id);
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array(
                'name' => $input['name'],
                'description'  => $input['description'],
                'board_id' => $input['board_id'],
                'user_id' => $_SESSION['user_id'],
            ));
            return $statement->rowCount();
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }

    //Updating specific status.
    public function update($id, array $input)
    {

        $statement = "
            UPDATE statuses
            SET 
                name = :name,
                description  = :description,
                board_id = :board_id,
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
                'description'  => $input['description'] ?? null,
                'board_id' => $input['board_id'],
                'user_id' => $_SESSION['user_id'],
                'updated_at' => date('Y-m-d h:i:s'),
            ));
            return $statement->rowCount();
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }

    //Deleting specific status.
    public function delete($id)
    {
        $statement = "
            DELETE FROM statuses
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
