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
$table = 'card';
//カード作成者がチームに所属しているかどうか

//バリデーションチェック
if ((empty($_POST['card_name']))) {
    $error = array(
        "error" => array(
            array(
                "code" => "451",
                "message" => "Validation error for 'card_name'"
            )
        )
    );
    echo json_encode($error);
    exit;
}
//入力値の受け取り
$card_name = $_POST['card_name'];
$card_discription = $_POST['discription'];
//名前の文字数制限
$max = 30;
$min = 1;
$name_len = strlen($card_name);
if($max < $name_len || $name_len < $min){
    $error = array(
        "error" => array(
            array(
                "code" => "451",
                "message" => "Validation error for 'card_name'"
            )
        )
    );
    echo json_encode($error);
    exit;
}
//カードの説明の文字数制限

//カード情報の登録
$person = ORM::for_table($table)->create();
$person->card_name = $card_name;
$person->card_discription = $card_discription;
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

//jsonの返却
$response = array(
    "result" => array(
        "token" => $token
    )
);
echo json_encode($response);
?>

