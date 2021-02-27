<?php

    use Cueva\Classes\ {Env, Func};
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
    
    // $list = [];
    // foreach(ORM::for_table($user_table)->find_result_set() as $user_list) {
    //     $list[] = ($user_list->as_array('user_id','token'));
    // }
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
    //ユーザーidの取得
    $user_id = $uesr_list['id'];

    //member_tableからユーザーidの情報取得
    $member_list = ORM::for_table($member_table)->where('user_id', $user_id)->find_one();
    
    // $list = [];
    // foreach(ORM::for_table($member_table)->find_result_set() as $member_list) {
    //     $list[] = ($member_list->as_array('member_no','user_id','team_id','member_invitation'));
    // }
    // var_dump($list);

    if($user_id !== $member_list['user_id']){
        //エラー内容
        //jsonでエラーメッセージの返却
        $err = array('error' =>
        array( 
        array('code' => '404','message' => 'Not Found')),
    );
        echo json_encode($err);
        exit;
}
    //member_tableからteam_idの取得
    $team_id = $member_list['team_id'];

    //team_tableから登録されているidの取得
    $team_list = ORM::for_table($team_table)->where('id', $team_id)->find_one();
    
    // $list = [];
    // foreach(ORM::for_table($team_table)->find_result_set() as $team_list) {
    //     $list[] = ($team_list->as_array('team_id'));
    // }

    // var_dump($team_list);

    //チームにユーザーが所属しているかチェック
    if($team_id !== $team_list['id']){
        //エラー内容
        //jsonでエラーメッセージの返却
        $err = array('error' =>
        array( 
        array('code' => '404','message' => 'Not Found')),
    );
        echo json_encode($err);
        exit;
}

    //チーム情報の変更
    $update_team = ORM::for_table($team_table)->where('id',$team_id)->find_result_set()
    ->set('team_name',$team_name ,'team_description',$team_description)
    ->save();

    //チーム内容変更時エラーメッセージ
    if(!$update_team->save()){
        //エラー内容
        //jsonでエラーメッセージの返却
        array('error' =>
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

?>
