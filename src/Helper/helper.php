<?php


function getConnection()
{
    global $dbConnection;
    
    return $dbConnection;
}

//Authentication check
function authenticated()
{
    if(isset($_SESSION['logged_in']))
    {
        return $_SESSION['logged_in'];
    }
    
}
