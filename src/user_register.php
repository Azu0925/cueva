<?php
//DB接続
use Cueva\Classes\ {Env, Func};

require_once '../vendor/j4mie/idiorm/idiorm.php';
require '../vendor/autoload.php';

ORM::configure('mysql:host='.Env::get("HOST").';port='.Env::get("PORT").';dbname='.Env::get("DB_NAME"));
ORM::configure('username', Env::get('USER_ID'));
ORM::configure('password', Env::get("PASSWORD"));
$table = 'user'; //テーブルの名前
$query = ORM::for_table($table)->find_one();
var_dump($query->as_array());

//パスワードのハッシュ化
$str = rand(10000,100000);
$salt = uniqid('', true);
for($i = 0; $i<$str; $i++){
    $pass = md5($salt.$_POST['user_password']);
}
//ユーザー情報の登録
$person = ORM::for_table('person')->create();
$person->user_name = $_POST['user_name'];
$person->user_address = $_POST['user_address'];
$person->user_password = $pass;
$person->save();


$mysql = "INSERT INTO user (user_name, user_address, user_password) VALUES('" . $_POST['user_name'] . "' , '" . $_SESSION['user_address'] . "' , '" . $_SESSION['user_password'] . "')";
mysqli_query($link,$mysql);
//tokenの生成
$unique = uniqid('', true);
//tokenが被っているかどうか

//tokenのDB格納
$mysql = "INSERT INTO user (token) VALUES('". $unique ."')";
mysqli_query($link,$mysql);
//jsonの返却
$response = array(
    
);

echo json_encode($response);
?>