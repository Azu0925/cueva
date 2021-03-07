<?php

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


    //table指定
    $user_table = 'user';
    $member_table = 'member';
    $team_table = 'team';
    $map_table = 'map';

    //入力値の受け取り
    $token = $_POST['token'];
    $map_id = $_POST['map_id'];
    $map_name = $_POST['map_name'];
    $map_description = $_POST['map_description'];
    $parameter_top = $_POST['parameter_top'];
    $parameter_under = $_POST['parameter_under'];
    $parameter_left = $_POST['parameter_left'];
    $parameter_right = $_POST['parameter_right'];


    //tokenのバリデーションチェック
    if(!isset($token)){
        $err = array(
            "error" => array(
                array(
                "code" => "400",
                "message" => "Bad Request"
                )
            )
        );
        echo json_encode($err, JSON_UNESCAPED_UNICODE);
        exit;
    }

    //map_nameのバリデーションチェック
    if(!isset($map_name)){
        $err = array(
            "error" => array(
                array(
                "code" => "400",
                "message" => "Bad Request"
                )
            )
        );
        echo json_encode($err, JSON_UNESCAPED_UNICODE);
        exit;
    }

    //map_descriptionのバリデーションチェック
    if (!preg_match("/^.{0,100}$/",$map_description)) {
        $err = array(
            "error" => array(
            array(
                "code" => "400",
                "message" => "Bad Request"
            )
        )
        );
        echo json_encode($err, JSON_UNESCAPED_UNICODE);
        exit;
    }

    //parameter_topのバリデーションチェック
    if(!isset($parameter_top)){
        $err = array(
            "error" => array(
                array(
                "code" => "400",
                "message" => "Bad Request"
                )
            )
        );
        echo json_encode($err, JSON_UNESCAPED_UNICODE);
        exit;
    }

    //parameter_underのバリデーションチェック
    if(!isset($parameter_under)){
        $err = array(
            "error" => array(
                array(
                "code" => "400",
                "message" => "Bad Request"
                )
            )
        );
        echo json_encode($err, JSON_UNESCAPED_UNICODE);
        exit;
    }

    //parameter_leftのバリデーションチェック
    if(!isset($parameter_left)){
        $err = array(
            "error" => array(
                array(
                "code" => "400",
                "message" => "Bad Request"
                )
            )
        );
        echo json_encode($err, JSON_UNESCAPED_UNICODE);
        exit;
    }

    //parameter_rightのバリデーションチェック
    if(!isset($parameter_right)){
        $err = array(
            "error" => array(
                array(
                "code" => "400",
                "message" => "Bad Request"
                )
            )
        );
        echo json_encode($err, JSON_UNESCAPED_UNICODE);
        exit;
    }

    //登録されているユーザー情報取得
    $user_list = ORM::for_table($user_table)->where('token', $token)->find_one();

    // var_dump($list);

    //tokenの照合
    if($user_list === false){
        //エラー内容
        //jsonでエラーメッセージの返却
        $err = array('error' =>
        array( 
        array('code' => '401','message' => 'Unauthorized')),
    );
        echo json_encode($err, JSON_UNESCAPED_UNICODE);
        exit;
    }
    $user_list->as_array();
    //user_idの取得
    $user_id = $user_list['id'];
    //var_dump($user_id);

    //user_nameの取得
    $map_host = $user_list['user_name'];
    //var_dump($map_host);
    $map_list = ORM::for_table('map')->where('id', $map_id)->find_one();
    if($map_list === false){
        $err = array(
            'error' =>
                array( 
                    array(
                        'code' => '401',
                        'message' => 'Unauthorized'
                    )
                ),
            );
        echo json_encode($err, JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    $map_list->as_array();

    //メンバー情報取得
    $member_list = ORM::for_table($member_table)
        ->where(
            array(
                'user_id' => $user_id,
                'id' => $map_list['team_id']
            )
        )
        ->find_one();

    if($member_list === false){
        $err = array(
            'error' =>
                array( 
                    array(
                        'code' => '401',
                        'message' => 'Unauthorized'
                    )
                ),
            );
        echo json_encode($err, JSON_UNESCAPED_UNICODE);
        exit;
    }

    //現在の日時の取得
    $map_create = date('Y-m-d H:i:s');
    //var_dump( $map_create);

    //map編集
    $map_update = ORM::for_table($map_table)->where('id', $map_id)->find_one();
          $map_update->map_name = $map_name;
          $map_update->map_description = $map_description;
          $map_update->map_create = $map_create;
          $map_update->map_host = $map_host;
          $map_update->parameter_top = $parameter_top;
          $map_update->parameter_under = $parameter_under;
          $map_update->parameter_left = $parameter_left;
          $map_update->parameter_right = $parameter_right;
          $map_update->save();
    //map編集時エラーメッセージ
    if(!$map_update->save()){
        //エラー内容
        //jsonでエラーメッセージの返却
        $err = array(
            'error' =>
        array( 
        array('code' => '452','message' => 'Insert error for database')),
    );
        echo json_encode($err, JSON_UNESCAPED_UNICODE);
        exit;
    }

    //jsonで返却
    $response = array(
    'result' => true,
    );
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    
?>
