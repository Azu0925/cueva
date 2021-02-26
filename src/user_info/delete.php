<?php
//DB接続
use Cueva\Classes\ {Env, Func};

    require_once '../../vendor/j4mie/idiorm/idiorm.php';
    require '../../vendor/autoload.php';

    ORM::configure('mysql:host='.Env::get("HOST").';port='.Env::get("PORT").';dbname='.Env::get("DB_NAME"));
    ORM::configure('username', Env::get('USER_ID'));
    ORM::configure('password', Env::get("PASSWORD"));
    ORM::configure('driver_options', array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));

//バリデーションチェック(勝手にアドレスとパスワードを入力させて退会すると思って書きました)
if((empty($_POST['user_address']))){
    $err_address = "Validation error for 'address'";
        if((empty($_POST['usser_password']))){
            $err_pass = "Validation error for 'password'";
        }
    exit;
}
//送られてきたトークンからユーザー情報を取得
$person = ORM::for_table('user')->where('token', $_POST['token'])->find_mamy();
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
if((empty($person['user_id']))){
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
    $delete = ORM::for_table('user')->where_like('token',$_COOKIE['token'])->find_many();
    $delete->delete();
    $delete_comp = "delete complete";
}
//jsonの返却
$response = array(
    'delete' => $delete,$delete_comp,
);
echo json_encode($response);
?>