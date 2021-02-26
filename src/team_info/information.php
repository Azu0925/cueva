<?php
//DB接続
use Cueva\Classes\ {Env, Func};

    require_once '../../vendor/j4mie/idiorm/idiorm.php';
    require '../../vendor/autoload.php';

    ORM::configure('mysql:host='.Env::get("HOST").';port='.Env::get("PORT").';dbname='.Env::get("DB_NAME"));
    ORM::configure('username', Env::get('USER_ID'));
    ORM::configure('password', Env::get("PASSWORD"));
    ORM::configure('driver_options', array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));

//送られてきたトークンの値から本人情報を取得
$token = $_COOKIE['token'];
$user = ORM::for_table('user')->where_like('token',$token)->find_many();
$member = ORM::for_table('member')->where_like('user_id',$user['user_id'])->find_many();
$team = ORM::for_table('team')->where_like('team_id',$member['team_id'])->find_many();
//エラー処理
    //認証失敗
    if((empty($_COOKIE['token']))){
        $error = array(
            "error" => array(
                array(
                    "code" => "401",
                    "message" => "Unauthorized"
                )
            )
        );
        echo json_encode($error);
        exit;
    }
    //リソースが見つからなかった時
    if((empty($user))){
        $error = array(
            "error" => array(
                array(
                    "code" => "404",
                    "message" => "Not Found"
                )
            )
        );
        echo json_encode($error);
        exit;
    }
    if((empty($team))){
        $error = array(
            "error" => array(
                array(
                    "code" => "404",
                    "message" => "Not Found"
                )
            )
        );
        echo json_encode($error);
        exit;
    }
//jsonの返却
$response = array(
    'user' => $user,
    'team' => $team,
);
echo json_encode($response);
?>
