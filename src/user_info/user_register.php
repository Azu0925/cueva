<?php
// //DB接続
// use Cueva\Classes\ {Env, Func};

//     require_once '../../vendor/j4mie/idiorm/idiorm.php';
//     require '../../vendor/autoload.php';

//     ORM::configure('mysql:host='.Env::get("HOST").';port='.Env::get("PORT").';dbname='.Env::get("DB_NAME"));
//     ORM::configure('username', Env::get('USER_ID'));
//     ORM::configure('password', Env::get("PASSWORD"));
//     ORM::configure('driver_options', array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));
// //入力値の受け取り
// $name = $_POST['user_name'];
// $address = $_POST['user_address'];
// $password = $_POST['user_password'];
// //テーブルの名前
// $table = 'user';
// //バリデーションチェック
// if ((empty($_POST['user_name']))) {
//     $err_name = "Validation error for 'name'";
//     $error = array(
//         "error" => array(
//             array(
//                 "code" => "451",
//                 "message" => $error_name
//             )
//         )
//     );
//     echo json_encode($error);
//     exit;
// }
// if((empty($_POST['user_address']))){
//     $err_address = "Validation error for 'address'";
//     $error = array(
//         "error" => array(
//             array(
//                 "code" => "451",
//                 "message" => $error_address
//             )
//         )
//     );
//     echo json_encode($error);
//     exit;
// }
// if((empty($_POST['user_password']))){
//     $err_pass = "Validation error for 'password'";
//     $error = array(
//         "error" => array(
//             array(
//                 "code" => "451",
//                 "message" => $error_pass
//             )
//         )
//     );
//     echo json_encode($error);
//     exit;
// }
// //パスワードのハッシュ化
// $hash = password_hash($password, PASSWORD_DEFAULT);
// //ユーザー情報の登録
// $person = ORM::for_table($table)->create();
// $person->user_name = $name;
// $person->user_address = $address;
// $person->user_password = $hash;
// $person->save();
// //insert失敗の処理   
// if(!$person->save()){
//     $error = array(
//         "error" => array(
//             array(
//                 "code" => "452",
//                 "message" => "Insert error for database 452"
//             )
//         )
//     );
// }
// echo json_encode($error);
// //tokenの生成
// $token = uniqid(dechex(random_int(0, 255)));
// $person = ORM::for_table('user')->where('use_address', $_POST['user_address'])->find_one();
// $people = ORM::for_table('user')->find_many();
// foreach ($people as $person) {
//     $person->token = $token;
//     $person->save();
// }
// //jsonの返却
// $response = array(
//     'user' => $person,
// );
// echo json_encode($response);
//DB接続
use Cueva\Classes\ {Env, Func};

    require_once '../../vendor/j4mie/idiorm/idiorm.php';
    require '../../vendor/autoload.php';

    ORM::configure('mysql:host='.Env::get("HOST").';port='.Env::get("PORT").';dbname='.Env::get("DB_NAME"));
    ORM::configure('username', Env::get('USER_ID'));
    ORM::configure('password', Env::get("PASSWORD"));
    ORM::configure('driver_options', array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));
//入力値の受け取り
$name = '犬っころ';
$address = '鳥';
$password = 'サルゥ';
$token = 'ぬこ';
//テーブルの名前
$table = 'user';
// //バリデーションチェック
// if ((empty($_POST['user_name']))) {
//     $err_name = "Validation error for 'name'";
//     $error = array(
//         "error" => array(
//             array(
//                 "code" => "451",
//                 "message" => $error_name
//             )
//         )
//     );
//     echo json_encode($error);
//     exit;
// }
// if((empty($_POST['user_address']))){
//     $err_address = "Validation error for 'address'";
//     $error = array(
//         "error" => array(
//             array(
//                 "code" => "451",
//                 "message" => $error_address
//             )
//         )
//     );
//     echo json_encode($error);
//     exit;
// }
// if((empty($_POST['user_password']))){
//     $err_pass = "Validation error for 'password'";
//     $error = array(
//         "error" => array(
//             array(
//                 "code" => "451",
//                 "message" => $error_pass
//             )
//         )
//     );
//     echo json_encode($error);
//     exit;
// }
//パスワードのハッシュ化
// $hash = password_hash($password, PASSWORD_DEFAULT);
//ユーザー情報の登録
$person = ORM::for_table($table)->create();
$person->user_name = $name;
$person->user_address = $address;
$person->user_password = $password;
$person->token = $token;
$person->save();
// //insert失敗の処理   
// if(!$person->save()){
//     $error = array(
//         "error" => array(
//             array(
//                 "code" => "452",
//                 "message" => "Insert error for database 452"
//             )
//         )
//     );
// }
// echo json_encode($error);
// //tokenの生成
// $token = uniqid(dechex(random_int(0, 255)));
// $person = ORM::for_table('user')->where('use_address', $_POST['user_address'])->find_one();
// $people = ORM::for_table('user')->find_many();
// foreach ($people as $person) {
//     $person->token = $token;
//     $person->save();
// }
// //jsonの返却
// $response = array(
//     'user' => $person,
// );
// echo json_encode($response);
?>