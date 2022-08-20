<?php
require 'vendor/autoload.php';

use Dotenv\Dotenv;

use Src\System\DatabaseConnector;

//Database connection
$dotenv = new DotEnv(__DIR__);
$dotenv->load();

$dbConnection = (new DatabaseConnector())->getConnection();
