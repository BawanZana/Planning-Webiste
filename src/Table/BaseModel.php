<?php

namespace Src\Table;

class BaseModel
{
    protected $db = null;

    public function __construct()
    {
        $this->db = getConnection();
    }

    public function getDB()
    {
        return $this->db;
    }
}
