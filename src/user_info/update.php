<?php

    use Cueva\Classes\ {Env, Func};
    require_once '../../vendor/j4mie/idiorm/idiorm.php';
    require '../../vendor/autoload.php';

    
    ORM::configure('mysql:host='.Env::get("HOST").';port='.Env::get("PORT").';dbname='.Env::get("DB_NAME"));
    ORM::configure('username', Env::get('USER_ID'));
    ORM::configure('password', Env::get("PASSWORD"));
    ORM::configure('driver_options', array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));


    //入力値の受け取り
    $user_id = $_POST['user_id'];
    $user_name = $_POST['user_name'];
    $user_address = $_POST['user_address'];
    
    //table指定
    $table = 'user';

    //tokenの取得
    $token = $_COOKIE['token'];

    //登録されているtokenの全件取得
    $list = ORM::for_table($table)->where('token', $token)->find_one();
    
    $user_list = [];
    foreach(ORM::for_table($table)->find_result_set() as $list) {
        $user_list[] = ($list->as_array('token'));
    }
    // var_dump($user_list);

    //tokenの照合
    if($token !== $list['token']){
        //エラー内容
        //jsonでエラーメッセージの返却
        $err = array('error' =>
        array( 
        array('code' => '401','message' => 'Unauthorized')),
    );
        echo json_encode($err);
}

    //アカウント内容の変更
    $user_table = ORM::for_table($table)->where('token',$token)->find_result_set()
        ->set('user_id',$user_id ,'user_name',$user_name ,'user_address',$user_address)
        ->save();
    
    //アカウント内容変更時エラーメッセージ
    if(!$user_table){
        //エラー内容
        //jsonでエラーメッセージの返却
        $err = array(
            'error' =>
        array( 
        array('code' => '452','message' => 'Insert error for database')),
    );
        echo json_encode($err);
    }
    
    //jsonで返却
    $response = array(
        'result' => true,
    );
    echo json_encode($response);
    
?>
