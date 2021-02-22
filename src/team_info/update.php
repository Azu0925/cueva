<?php

    use Cueva\Classes\ {Env, Func};
    require_once '../../vendor/j4mie/idiorm/idiorm.php';
    require '../../vendor/autoload.php';

    
    ORM::configure('mysql:host='.Env::get("HOST").';port='.Env::get("PORT").';dbname='.Env::get("DB_NAME"));
    ORM::configure('username', Env::get('USER_ID'));
    ORM::configure('password', Env::get("PASSWORD"));
    ORM::configure('driver_options', array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));


    //入力値の受け取り
    $team_id = $_POST['team_id'];
    $team_name = $_POST['team_name'];
    $team_description = $_POST['team_description'];
    $team_create = $_POST['team_create'];
    $team_host = $_POST['team_host'];
    
    //table指定
    $user_table = 'user';
    $member_table = 'member';
    $team_table = 'team';

    //tokenの取得
    //
    $token = [''];

    //登録されているユーザー情報取得
    $user_list = ORM::for_table($user_table)->where('token', $token)->find_one();
    
    $list = [];
    foreach(ORM::for_table($user_table)->find_result_set() as $user_list) {
        $list[] = ($user_list->as_array('user_id','token'));
    }
    // var_dump($list);

    $user_id = $list['user_id'];
    
    //tokenの照合
    if($token !== $user_list['token']){
        //エラー内容
        //jsonでエラーメッセージの返却
        $err = array('error' =>
        array( 
        array('code' => '401','message' => 'Unauthorized')),
    );
        echo json_encode($err);
}

    //member_tableからユーザーidとチームidの情報取得
    $member_list = ORM::for_table($member_table)->where('user_id', $user_id)->find_many();
    
    $list = [];
    foreach(ORM::for_table($member_table)->find_result_set() as $member_list) {
        $list[] = ($member_list->as_array('member_no','user_id','team_id','member_invitation'));
    }
    // var_dump($list);

    //チームにユーザーが所属しているかチェック
    if($user_id !== $member_list['user_id']){
        //エラー内容
        //jsonでエラーメッセージの返却
        $err = array('error' =>
        array( 
        array('code' => '403','message' => 'Forbidden')),
    );
        echo json_encode($err);
}
    

    //member_tableからteam_idの取得
    $team_id = $list['team_id'];

    //team_tableから登録されているチームidの全件取得
    $team_list = ORM::for_table($team_table)->where('team_id', $team_id)->find_one();
    
    $list = [];
    foreach(ORM::for_table($team_table)->find_result_set() as $team_list) {
        $list[] = ($team_list->as_array('team_id'));
    }
    // var_dump($team_list);

    //チーム情報の変更
    $team_info = ORM::for_table($team_table)->where('team_id',$id)->find_result_set()
    ->set('team_id',$team_id ,'team_name',$team_name ,'team_description',$team_description,'team_create',$team_create,'team_host',$team_host)
    ->save();

    //チーム内容変更時エラーメッセージ
    if(!$team_table){
        //エラー内容
        $err_code = '452';
        $err_message = 'Insert error for database';
    }
        
    //jsonの返却
    $response = array(
        'result' => true,
    );
    echo json_encode($response);

?>

