<?php


namespace IntelCost\Furniture\services;


class UserSession
{
    public static function init()
    {
        session_start();
    }

    public static function setCurrentEmailUser($user_email){
        $_SESSION['user_email'] = $user_email;
    }

    public static function getCurrenEmailUser(){
        return isset($_SESSION['user_email']) ? $_SESSION['user_email'] : null;
    }

    public static function close(){
        session_unset();
        session_destroy();
    }
}