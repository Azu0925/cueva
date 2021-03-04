<?php
//DB接続
use Cueva\Classes\{Env, Func};

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: X-Requested-With, Origin, X-Csrftoken, Content-Type, Accept");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, CONNECT, OPTIONS, TRACE, PATCH, HEAD");
require_once '../../vendor/j4mie/idiorm/idiorm.php';
require '../../vendor/autoload.php';

ORM::configure('mysql:host=' . Env::get("HOST") . ';port=' . Env::get("PORT") . ';dbname=' . Env::get("DB_NAME"));
ORM::configure('username', Env::get('USER_ID'));
ORM::configure('password', Env::get("PASSWORD"));
ORM::configure('driver_options', array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));

//tokenとteam_idの検索
if (isset($_POST['token']) && isset($_POST['team_id'])) {
    $token = $_POST['token'];  //tokenを取得し変数へ格納
    $team_id = $_POST['team_id']; //team_idを取得し変数へ格納

    $user_id = ORM::for_table("user")->where('token', $token)->find_one();
    if ($user_id != false) {
        $id = $user_id->id;

        $join = ORM::for_table("member")->where(array(
            'user_id' => $id,
            'team_id' => $team_id,
            'member_invitation' => 0
        ))->find_result_set();

        if ($join != false) {
            $join->set('member_invitation', 1)
                ->save();
                $result = array(
                    "result" => array(
                      array(
                        "result" => true
                      )
                    )
                  );
                  echo json_encode($result, JSON_UNESCAPED_UNICODE);
            exit;
        } else {
            $error = array(
                "error" => array(
                    array(
                        "code" => "452",
                        "message" => "Select error for database"
                    )
                )
            );
            echo json_encode($error, JSON_UNESCAPED_UNICODE);
            exit;
        }
    } else { //selectエラー処理(user_id)
        $error = array(
            "error" => array(
                array(
                    "code" => "452",
                    "message" => "Select error for database"
                )
            )
        );
        echo json_encode($error, JSON_UNESCAPED_UNICODE);
        exit;
    }
} else {
    //tokenとteam_idが取得できなかった場合
    $error = array(
        "error" => array(
            array(
                "code" => "453",
                "message" => "Paramter is null"
            )
        )
    );
}

echo json_encode($error, JSON_UNESCAPED_UNICODE);
