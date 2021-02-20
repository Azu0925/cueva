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
$person = ORM::for_table('user')->where('token', $_POST['token'])->find_mamy();
//取ってきたユーザーIDから所属チームを取得
$team = ORM::for_table('member')->where('user_id', $_POST['user_id'])->find_mamy();
//エラー処理
    //認証失敗
    if((empty($_POST['token']))){
        $err = "Unauthorized 401";
        exit;    
    }
    //リソースが見つからなかった時
    if((empty($porson))){
        $err = "Not Found 404";
        exit;    
    }
    if((empty($team))){
        $err = "Not Found 404";
        exit;    
    }

//jsonの返却
$response = array(
    'user' => $porson,
    'team' => $team,
    'err' => $err,
);
echo json_encode($response);
?>