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

if((empty($_POST['token']))){
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
//送られてきたトークンからユーザー情報を取得
$person = ORM::for_table('user')->where('token', $_POST['token'])->find_one()->as_array();

//ユーザー情報が見つからなかった時のエラー　見つかったらレコードの削除
// if((empty($person['user_id']))){
//     $err = "Not Found 404";
//     exit;
// }
// else{
//     $delete = ORM::for_table('user')->where_like('token',$_POST['token'])->find_many();
//     $delete->delete();
//     $delete_comp = "delete complete";
// }
if((empty($person['id']))){
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
    $delete = ORM::for_table('user')->where_like('token',$_POST['token'])->find_one();
    $delete->delete();
}
//jsonの返却
$response = array(
    "result" => "true"
);
echo json_encode($response);
?>