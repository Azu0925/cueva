<?php
//DB接続
use Cueva\Classes\ {Env, Func};

    require_once '../../vendor/j4mie/idiorm/idiorm.php';
    require '../../vendor/autoload.php';

    ORM::configure('mysql:host='.Env::get("HOST").';port='.Env::get("PORT").';dbname='.Env::get("DB_NAME"));
    ORM::configure('username', Env::get('USER_ID'));
    ORM::configure('password', Env::get("PASSWORD"));
    ORM::configure('driver_options', array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));

//送られてきたトークンからチーム情報を取得
$token = $_POST['token'];
$user = ORM::for_table('user')->where_like('token',$token)->find_many();
$member = ORM::for_table('member')->where_like('user_id',$user['user_id'])->find_many();
$team = ORM::for_table('team')->where_like('team_id',$member['team_id'])->find_many();
//チーム情報が見つからなかった時のエラー　見つかったらレコードの削除
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
else{
    $team->delete();
    $member->delete();
    $delete_comp = "delete complete";
    // require_once './';
}

//jsonの返却
$response = array(
    'delete' => $team,$member,$delete_comp,
);
echo json_encode($response);
?>