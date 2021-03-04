<?php
//DB接続
use Cueva\Classes\ {Env, Func};

    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Headers: X-Requested-With, Origin, X-Csrftoken, Content-Type, Accept");
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, CONNECT, OPTIONS, TRACE, PATCH, HEAD");

    require_once '../../vendor/j4mie/idiorm/idiorm.php';
    require '../../vendor/autoload.php';

    ORM::configure('mysql:host='.Env::get("HOST").';port='.Env::get("PORT").';dbname='.Env::get("DB_NAME"));
    ORM::configure('username', Env::get('USER_ID'));
    ORM::configure('password', Env::get("PASSWORD"));
    ORM::configure('driver_options', array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));

    //送られてきたトークンの値から本人情報を取得
    $token = $_POST['token'];
    $team_id = $_POST['team_id'];

    $user = ORM::for_table('user')->where_like('token',$token)->find_one();

    if($user === false){
        $error = array(
            "error" => array(
                array(
                    "code" => "401",
                    "message" => "Unauthorized"
                )
            )
        );
        echo json_encode($error, JSON_UNESCAPED_UNICODE);
        exit;        
    }

    $member = ORM::for_table('member')
        ->where_like(array(
            'user_id' => $user['id'],
            'team_id' => $team_id
        ))
        ->find_one();

    //認証失敗
    if($member === false){
        $error = array(
            "error" => array(
                array(
                    "code" => "403",
                    "message" => "Forbidden"
                )
            )
        );
        echo json_encode($error, JSON_UNESCAPED_UNICODE);
        exit;
    }

    $team_info = ORM::for_table('team')->where('id', $team_id)->find_one()->as_array();
    $map_info = ORM::for_table('map')->where('team_id', $team_info['id'])->find_many();

    $list = [];
    foreach($map_info as $row){
        $list[] = $row['id'];
    }
    //jsonの返却
    $response = array(
        "result" => array(
            "team_id" => $team_info['id'],
            "team_name" => $team_info['team_name'],
            "team_description" => $team_info['team_description'],
            "map_id" => $list
        )
    );
    //21var_dump($response);
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    ?>
