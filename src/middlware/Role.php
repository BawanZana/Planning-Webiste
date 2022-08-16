<?php
namespace Src\middlware;

class Role
{
    public function productOwner()
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

    public function developer()
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

    public function tester()
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


?>