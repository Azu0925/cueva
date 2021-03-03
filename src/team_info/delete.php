<?php
//DB接続
use Cueva\Classes\ {Env, Func};

    require_once '../../vendor/j4mie/idiorm/idiorm.php';
    require '../../vendor/autoload.php';

    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Headers: X-Requested-With, Origin, X-Csrftoken, Content-Type, Accept");
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, CONNECT, OPTIONS, TRACE, PATCH, HEAD");

    ORM::configure('mysql:host='.Env::get("HOST").';port='.Env::get("PORT").';dbname='.Env::get("DB_NAME"));
    ORM::configure('username', Env::get('USER_ID'));
    ORM::configure('password', Env::get("PASSWORD"));
    ORM::configure('driver_options', array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));

    //送られてきたトークンからチーム情報を取得
    //userテーブルからuser_idを取得
    $user = ORM::for_table('user')->where_like('token',$_POST['token'])->find_many();
    //取ってきたuser_idからmemberテーブルのteam_idを取ってくる
    $member = ORM::for_table('member')->where_like('user_id',$user['user_id'])->find_many();
    $team = ORM::for_table('team')->where_like('id',$_POST['id'])->find_many();
    //チーム情報が見つからなかった時のエラー　見つかったらレコードの削除
    if(isset($team)){
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
    else{
        $team->delete();
        //jsonの返却
        $response = array(
            'result' =>true
        );
        
        echo json_encode($response);    
    }
?>