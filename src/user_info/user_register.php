<?php
//DB接続
use Cueva\Classes\ {Env, Func};

    require_once '../../vendor/j4mie/idiorm/idiorm.php';
    require '../../vendor/autoload.php';

    
    ORM::configure('mysql:host='.Env::get("HOST").';port='.Env::get("PORT").';dbname='.Env::get("DB_NAME"));
    ORM::configure('username', Env::get('USER_ID'));
    ORM::configure('password', Env::get("PASSWORD"));

//入力値の受け取り
$name = $_POST['name'];
$address = $_POST['user_address'];
$password = $_POST['user_password'];
//テーブルの名前
$table = 'user';
//パスワードのハッシュ化
$hash = password_hash($password, PASSWORD_DEFAULT);
//ユーザー情報の登録
$person = ORM::for_table($table)->create();
$person->user_name = $name;
$person->user_address = $address;
$person->user_password = $hash;
$person->save();
//tokenの生成
$token = uniqid(dechex(random_int(0, 255)));
$person = ORM::for_table('user')->where('use_address', $_POST['user_address'])->find_one();
$people = ORM::for_table('user')->find_many();
foreach ($people as $person) {
    $person->token = $token;
    $person->save();
}
//jsonの返却
$response = array(
    'user' => $porson,
);
echo json_encode($response);
?>