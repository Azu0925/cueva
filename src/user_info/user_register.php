<?php
//DB接続
use Cueva\Classes\ {Env, Func};

    require_once '../../vendor/j4mie/idiorm/idiorm.php';
    require '../../vendor/autoload.php';

    ORM::configure('mysql:host='.Env::get("HOST").';port='.Env::get("PORT").';dbname='.Env::get("DB_NAME"));
    ORM::configure('username', Env::get('USER_ID'));
    ORM::configure('password', Env::get("PASSWORD"));
    ORM::configure('driver_options', array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));


//テーブルの名前
$table = 'user';
//バリデーションチェック
if ((empty($_POST['user_name']))) {
    $error = array(
        "error" => array(
            array(
                "code" => "451",
                "message" => "Validation error for 'name'"
            )
        )
    );
    echo json_encode($error);
    exit;
}
if((empty($_POST['user_address']))){
    $err_address = "Validation error for 'address'";
    $error = array(
        "error" => array(
            array(
                "code" => "451",
                "message" => "Validation error for 'name'"
            )
        )
    );
    echo json_encode($error);
    exit;
}
if((empty($_POST['usser_password']))){
    $error = array(
        "error" => array(
            array(
                "code" => "451",
                "message" => "Validation error for 'password'"
            )
        )
    );
    echo json_encode($error);
    exit;
}
//正規表現
//メールアドレス
const MAIL = "/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/";
//ログインID
const PASS = "/^[a-zA-Z0-9]{8,30}+$/";
//名前
const NAME = "/^.{1,30}+$/";
//入力値の受け取り
$name = $_POST['user_name'];
$address = $_POST['user_address'];
$password = $_POST['user_password'];
//パスワードの文字数制限
if(!preg_match(PASS,$_POST['user_password'])){
    $error = array(
        "error" => array(
            array(
                "code" => "400",
                "message" => "Bad Request"
            )
        )
    );
    echo json_encode($error);
    exit;
}
//アドレスの文字制限
if(!preg_match(MAIL,$address)){
    $error = array(
        "error" => array(
            array(
                "code" => "400",
                "message" => 'Bad Request'
            )
        )
    );
    echo json_encode($error);
    exit;
}
//名前の文字制限
if(!preg_match(NAME,$name)){
    $error = array(
        "error" => array(
            array(
                "code" => "400",
                "message" => 'Bad Request'
            )
        )
    );
    echo json_encode($error);
    exit;
}
//アドレスに＠が含まれるかどうかチェック
$heystack = $address; // 捜査対象となる文字列
$needle   = '@'; // 見つけたい文字列
if ( strpos( $heystack, $needle ) === false ) {
    $error = array(
        "error" => array(
            array(
                "code" => "400",
                "message" => "Bad Request"
            )
        )
    );
    echo json_encode($error);
    exit;
  } 
//パスワードのハッシュ化
$hash = password_hash($password, PASSWORD_DEFAULT);
//ユーザー情報の登録
$person = ORM::for_table($table)->create();
$person->user_name = $name;
$person->user_address = $address;
$person->user_password = $hash;
$person->save();
//insert失敗の処理   
if(!$person->save()){
    $error = array(
        "error" => array(
            array(
                "code" => "452",
                "message" => "Insert error for database"
            )
        )
    );
    echo json_encode($error);
    exit;
}

//tokenの生成
$token = uniqid(dechex(random_int(0, 255)));
$person = ORM::for_table('user')->where('user_address', $user_address)->find_one();
$people = ORM::for_table('user')->find_many();
foreach ($people as $person) {
    $person->token = $token;
    $person->save();
}
//jsonの返却
$response = array(
    'user' => $person,
);
echo json_encode($response);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <form action="./user_register.php" method="post" >
    <p>氏名<input type="text" name="user_name"><br>
    <button type="submit">登録する</button></p>
    </form>
</body>
</html>