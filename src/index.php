<?php
    use Cueva\Classes\ {DataBase, Env};

    require '../vendor/autoload.php';

    $db = new DataBase(Env::get("HOST"), Env::get("USER_ID"), Env::get("PASSWORD"), Env::get("DB_NAME"));
    if(isset($db->err_msg)){
        echo $db->err_msg;
    }