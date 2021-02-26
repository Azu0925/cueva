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
if((empty($_POST['user_password']))){
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
// const NAME = "/^.{1,30}+$/";
//入力値の受け取り
$name = $_POST['user_name'];
$user_address = $_POST['user_address'];
$password = $_POST['user_password'];

//名前の文字数制限
$max = 30;
$min = 1;
$name_len = strlen($name);
if($max < $name_len || $name_len < $min){
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


//パスワードの文字数制限
if(!preg_match(PASS,$_POST['user_password'])){
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
//アドレスの文字制限
if(!preg_match(MAIL,$user_address)){
    $error = array(
        "error" => array(
            array(
                "code" => "400",
                "message" => "Validation error for 'password'"
            )
        )
    );
    echo json_encode($error);
    exit;
}
// //名前の文字制限
// if(!preg_match(NAME,$name)){
//     $error = array(
//         "error" => array(
//             array(
//                 "code" => "400",
//                 "message" => 'Bad Request'
//             )
//         )
//     );
//     echo json_encode($error);
//     exit;
// }
//アドレスに＠が含まれるかどうかチェック
$heystack = $user_address; // 捜査対象となる文字列
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
$person->user_address = $user_address;
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

$person->token = $token;
$person->save();

//jsonの返却
$response = array(
    "result" => array(
        "token" => $token
    )
);
echo json_encode($response);
?>
