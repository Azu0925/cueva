<?php

    use Cueva\Classes\ {Env, Func};
    require_once '../../vendor/j4mie/idiorm/idiorm.php';
    require '../../vendor/autoload.php';

    
    ORM::configure('mysql:host='.Env::get("HOST").';port='.Env::get("PORT").';dbname='.Env::get("DB_NAME"));
    ORM::configure('username', Env::get('USER_ID'));
    ORM::configure('password', Env::get("PASSWORD"));


    //入力値の受け取り
    $user_address = $_POST['user_address'];
    $user_password = $_POST['user_password'];
    
    //table指定
    $table = 'user';

    //登録されているユーザー情報の全件取得
    $list = ORM::for_table($table )->where('user_address', $user_address)->find_many();
    var_dump($list);

    //メールアドレスの照合
    if($user_address !== $list['user_address']){
        $err = 'Unauthorized 401';
    }
    
    //パスワードの取得
    $hash = $list['user_password'];

    //パスワードの照合
    if(password_verify($user_passord, $hash) !== $list['user_password']){
        $err = 'Unauthorized 401';
    }

    //user_idの取得
    $id = $list['user_id'];

    //token生成
    $token = uniqid(dechex(random_int(0, 255)));
    var_dump($token);

    //token上書き
    ORM::for_table($table)->where('user_id',$id)->find_result_set()
        ->set('token', $token)
        ->save();


    //jsonの返却
    

    
?>