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


    //テーブルの名前
    $user_table = 'user';
    $member_table = 'member';

    //値の取得
    $token = $_POST['token'];
    $team_id = $_POST['team_id'];

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
        echo json_encode($err, JSON_UNESCAPED_UNICODE);
        exit;
}

    //memberテーブル情報取得
    $member_list = ORM::for_table($member_table)->where('team_id', $team_id)->find_one();
    
    $list = [];
    foreach(ORM::for_table($member_table)->find_result_set() as $member_list) {
        $list = ($member_list->as_array('team_id'));
    }
    // var_dump($list);

    //team_idの照合
    if($team_id !== $member_list['team_id']){
        //エラー内容
        //jsonでエラーメッセージの返却
        $err = array('error' =>
        array( 
        array('code' => '401','message' => 'Unauthorized')),
    );
        echo json_encode($err, JSON_UNESCAPED_UNICODE);
        exit;
}

    //team_idの取得
    $team_id = $member_list['team_id'];

    $user_reject = ORM::for_table($member_table)->where('team_id', $team_id)->find_one();
    
    $user_reject->delete();
    
    //delete時エラーメッセージ
    if(!$user_reject->delete()){
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
