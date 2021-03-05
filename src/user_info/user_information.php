<?php
//DB接続
    use Cueva\Classes\ {Env, Func};
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Headers: X-Requested-With, Origin, X-Csrftoken, Content-Type, Accept");
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, CONNECT, OPTIONS, TRACE, PATCH, HEAD");
    require_once '../../vendor/j4mie/idiorm/idiorm.php';
    require '../../vendor/autoload.php';

    ORM::configure('mysql:host='.Env::get("HOST").';port='.Env::get("PORT").';dbname='.Env::get("DB_NAME"));
    ORM::configure('username', Env::get('USER_ID'));
    ORM::configure('password', Env::get("PASSWORD"));
    ORM::configure('driver_options', array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));

    //送られてきたトークンの値から本人情報を取得
    $person = ORM::for_table('user')->where('token', $_POST['token'])->find_one();
    if($person !== false){
        $person->as_array();
    }
    //取ってきたユーザーIDから所属チームを取得
    $member = ORM::for_table('member')->where('user_id', $person['id'])->find_many();

    $team_list = [];
    foreach($member as $row){
        $team_list[] = ORM::for_table('team')->where('id', $row['team_id'])->select_many('id', 'team_name')->find_one()->as_array();

    }
    //エラー処理
    //認証失敗
    if((empty($_POST['token']))){
        $error = array(
            "error" => array(
                array(
                    "code" => "401",
                    "message" => "Unauthorized"
                )
            )
        );
        echo json_encode($error, JSON_UNESCAPED_UNICODE);
        exit;
    }
    //リソースが見つからなかった時
    if((empty($person))){
        $error = array(
            "error" => array(
                array(
                    "code" => "404",
                    "message" => "Not Found"
                )
            )
        );
        echo json_encode($error, JSON_UNESCAPED_UNICODE);
        exit;
    }
    /*if((empty($team))){
        $error = array(
            "error" => array(
                array(
                    "code" => "404",
                    "message" => "Not Found"
                )
            )
        );
        echo json_encode($error, JSON_UNESCAPED_UNICODE);
        exit;
    }*/
//jsonの返却
$response = array(
    "result" => array(
        "user_name" => $person['user_name'],
        "user_address" =>$person['user_address'],  
        "team_info" => $team_list
    )
);
 
echo json_encode($response, JSON_UNESCAPED_UNICODE);
?>