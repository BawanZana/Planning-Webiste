<?php


function getConnection()
{
    global $dbConnection;
    
    return $dbConnection;
}

//Authentication check
function authenticated()
{
    return $_SESSION['logged_in'];
}
