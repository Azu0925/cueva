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
    
    /*$list = [];
    foreach(ORM::for_table($user_table)->find_result_set() as $user_list) {
        $list = ($user_list->as_array('user_name','token'));
    }*/
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

    //userテーブルのidを取得
    $user_id = $user_list['id'];

    //user_nameの取得
    $user_name = $user_list['user_name'];

    //team_idの取得
    $team_id = $_POST['team_id'];

    //member_tableからユーザーidの情報取得
    $member_list = ORM::for_table($member_table)
        ->where(
            array(
                'team_id' => $team_id,
                'user_id' => $user_id
            )
        )
        ->find_one();

    //team_idの照合
    if($member_list === false){
        //エラー内容
        //jsonでエラーメッセージの返却
        $err = array('error' =>
        array( 
            array('code' => '403','message' => 'Forbidden')),
        );
        echo json_encode($err, JSON_UNESCAPED_UNICODE);
        exit;
    }

    //現在の日時の取得
    $team_create = date('Y-m-d H:i:s');
    //var_dump( $team_create);

    //チーム情報の変更
    $update_team = ORM::for_table($team_table)->where('id',$team_id)->find_result_set()
    ->set('team_name',$team_name , 'team_description',$team_description , 'team_create',$team_create , 'team_host',$user_name)
    ->save();

    //チーム内容変更時エラーメッセージ
    if(!$update_team->save()){
        //エラー内容
        //jsonでエラーメッセージの返却
        array('error' =>
        array( 
        array('code' => '452','message' => 'Insert error for database')),
    );
        echo json_encode($err, JSON_UNESCAPED_UNICODE);
        exit;
}
        
    //jsonの返却
    $response = array(
        'result' => true,
    );
    echo json_encode($response, JSON_UNESCAPED_UNICODE);

?>
