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
    $team_host = $_POST['team_host'];
    
    //table指定
    $user_table = 'user';
    $team_table = 'team';

    //tokenの取得
    $token = $_POST['token'];

    //登録されているユーザー情報取得
    $user_list = ORM::for_table($user_table)->where('token', $token)->find_one();
    
    $list = [];
    foreach(ORM::for_table($user_table)->find_result_set() as $user_list){
        $list[] = ($user_list->as_array('token'));
    }
    // var_dump($list);
    
//     //tokenの照合
//     if($token !== $user_list['token']){
//         //エラー内容
//         //jsonでエラーメッセージの返却
//         $err = array('error' =>
//         array( 
//         array('code' => '401','message' => 'Unauthorized')),
//     );
//         echo json_encode($err);
//         exit;
// }

    //チーム新規作成
    $new_team = ORM::for_table($team_table)->create();
    $new_team->team_name = $team_name;
    $new_team->team_description = $team_description;
    $new_team->team_host = $team_host;
    $new_team->save();


    if(!$new_team->save()){
        //エラー内容
        //jsonでエラーメッセージの返却
        $err = array('error' =>
        array( 
        array('code' => '452','message' => 'Insert error for database')),
    );
        echo json_encode($err);
        exit;
}

    //jsonでチームidの返却
    $response = array(
        'team_id' => $team_id,
    );
    echo json_encode($response);
?>


