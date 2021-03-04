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
    $user_address = $_POST['user_address'];
    $user_password = $_POST['user_password'];
    
    //table指定
    $table = 'user';

    //登録されているユーザー情報の全件取得
    $list = ORM::for_table($table)->where('user_address', $user_address)->find_many();

    $user_list = [];
    foreach(ORM::for_table($table)->find_result_set() as $list) {
        $user_list = ($list->as_array('id' , 'user_name' , 'user_address' , 'user_password' ,'token'));
    }

    //メールアドレスの照合
    if($user_address !== $list['user_address']){
        //エラー内容
        //jsonでエラーメッセージの返却
        $err = array(
            'error' =>
        array( 
        array('code' => '401','message' => 'Unauthorized')),
    );
        echo json_encode($err);
        exit;
}
    
    //パスワードの取得
    $hash = $user_list['user_password'];

    //パスワードの照合
    if(!password_verify($user_password, $hash)){
        //エラー内容
        //jsonでエラーメッセージの返却
        echo 'a';
        $err = array(
            'error' =>
        array( 
        array('code' => '401','message' => 'Unauthorized')),
    );
        echo json_encode($err);
        exit;
}

    //user_idの取得
    $id = $user_list['id'];
    
    //token生成
    $generate_token = uniqid(dechex(random_int(0, 255)));
    // var_dump($token);

    //token上書き
    ORM::for_table($table)->where('id',$id)->find_result_set()
        ->set('token', $generate_token)
        ->save();

    //jsonでtokenの返却
    $response = array(
        'token' => $generate_token,
    );
    echo json_encode($response);

?>
