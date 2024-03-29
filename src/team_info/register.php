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


    //入力値の受け取り
    $team_name = $_POST['team_name'];
    $team_description = $_POST['team_description'];
    
    //table指定
    $user_table = 'user';
    $member_table = 'member';
    $team_table = 'team';

    //tokenの取得
    $token = $_POST['token'];

    //登録されているユーザー情報取得
    $user_list = ORM::for_table($user_table)->where('token', $token)->find_one();    
    //var_dump($user_list);
    
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
    //user_idの取得
    $user_id = $user_list['id'];
    //user_nameの取得
    $user_name = $user_list['user_name'];

    //現在の日時の取得
    $new_team_create = date('Y-m-d H:i:s');
    //var_dump( $new_team_create);

    //チーム新規作成
    $new_team = ORM::for_table($team_table)->create();
    $new_team->team_name = $team_name;
    $new_team->team_description = $team_description;
    $new_team->team_create = $new_team_create;
    $new_team->team_host = $user_name;
    $new_team->save();


    if(!$new_team->save()){
        //エラー内容
        //jsonでエラーメッセージの返却
        $err = array('error' =>
        array( 
        array('code' => '452','message' => 'Insert error for database')),
    );
        echo json_encode($err, JSON_UNESCAPED_UNICODE);
        exit;
}


    //登録されたチームid取得
    $team_id = $new_team->id;
    //var_dump($team_id);

    //memberテーブルに作成者追加
    $new_member = ORM::for_table($member_table)->create();
    $new_member->user_id = $user_id;
    $new_member->team_id = $team_id;
    $new_member->member_invitation = 1;
    $new_member->save();

    if(!$new_member->save()){
        //エラー内容
        //jsonでエラーメッセージの返却
        $err = array('error' =>
        array( 
        array('code' => '452','message' => 'Insert error for database')),
    );
        echo json_encode($err, JSON_UNESCAPED_UNICODE);
        exit;
}

    //jsonでチームidの返却
    $response = array(
        "result" => array(
            'team_id' => $team_id
        )
    );
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
?>


