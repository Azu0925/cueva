<?php
    use Cueva\Classes\ {DataBase};

    require dirname(__DIR__).'/vendor/autoload.php';
    require_once './Classes/DataBase.php';
    $db = new DataBase;
    print($db->link);