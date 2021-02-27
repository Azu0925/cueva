<?php

    use Cueva\Classes\ {Env, Func};
    require_once '../../vendor/j4mie/idiorm/idiorm.php';
    require '../../vendor/autoload.php';

 
    ORM::configure('mysql:host='.Env::get("HOST").';port='.Env::get("PORT").';dbname='.Env::get("DB_NAME"));
    ORM::configure('username', Env::get('USER_ID'));
    ORM::configure('password', Env::get("PASSWORD"));
    ORM::configure('driver_options', array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));

    //tableの指定
    $user_table = 'user';
    $member_table = 'member';

    //tokenの取得
    $token = $_POST['token'];

    //登録されているユーザー情報取得
    $user_list = ORM::for_table($user_table)->where('token', $token)->find_one();
    
    $list = [];
    foreach(ORM::for_table($user_table)->find_result_set() as $user_list) {
        $list = ($user_list->as_array('token'));
    }
    // var_dump($list);
    
    //tokenの照合
    if($token !== $user_list['token']){
        //エラー内容
        //jsonでエラーメッセージの返却
        $err = array('error' =>
        array( 
        array('code' => '401','message' => 'Unauthorized')),
    );
        echo json_encode($err);
        exit;
}

    //user_idの取得
    $user_id = $_POST['user_id'];

    //team_idの取得
    $team_id = $_POST['team_id'];

    //memberテーブルにチーム招待の情報を保存
    $new_member = ORM::for_table($member_table)->create();
    $new_member->user_id = $user_id;
    $new_member->team_id = $team_id;
    $new_member->member_invitation = 0;
    $new_member->save();

    if(!$new_member->save()){
        //エラー内容
        //jsonでエラーメッセージの返却
        $err = array('error' =>
        array( 
        array('code' => '452','message' => 'Insert error for database')),
    );
        echo json_encode($err);
        exit;
}



    //jsonの返却
    $response = array(
        'result' => true,
    );
    echo json_encode($response);

    

    
