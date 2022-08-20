<?php
namespace Src\Guard;

//Checking role of the logged in user. 
class Role
{
    
    public static function productOwner()
    {
        if($_SESSION['user_type'] == 1)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public static function developer()
    {
        if($_SESSION['user_type'] == 2)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public static function tester()
    {
        if($_SESSION['user_type'] == 3)
        {
            return true;
        }
        else
        {
            return false;
        }
    }
}
