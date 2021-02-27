<?php

    use Cueva\Classes\ {Env, Func};
    require_once '../../vendor/j4mie/idiorm/idiorm.php';
    require '../../vendor/autoload.php';

    
    ORM::configure('mysql:host='.Env::get("HOST").';port='.Env::get("PORT").';dbname='.Env::get("DB_NAME"));
    ORM::configure('username', Env::get('USER_ID'));
    ORM::configure('password', Env::get("PASSWORD"));
    ORM::configure('driver_options', array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));


    //入力値の受け取り
    $map_name = $_POST['map_name'];
    $map_description = $_POST['map_description'];
    $map_host = $_POST['map_host'];
    $parameter_top = $_POST['parameter_top'];
    $parameter_under = $_POST['parameter_under'];
    $parameter_left = $_POST['parameter_left'];
    $parameter_right = $_POST['parameter_right'];

    //table指定
    $map_table = 'map';

    //map_idの取得
    $map_id = $_POST['id'];

    //mapテーブルかｒ情報取得
    $map_list = ORM::for_table($map_table)->where('id', $map_id)->find_many();
    
    $list = [];
    foreach(ORM::for_table($map_table)->find_result_set() as $map_list) {
        $list[] = ($map_list->as_array('id'));
    }
    // // var_dump($list);

    //現在の日時の取得
    $map_create = date('Y-m-d H:i:s');
    //var_dump( $map_create);

    //map編集
    $map_update = ORM::for_table($map_table)->where('id',$map_id)->find_result_set()
        ->set('map_name',$map_name ,'map_description',$map_description , 'map_create',$map_create , 'map_host',$map_host , 'parameter_top',$parameter_top , 'parameter_under',$parameter_under , 'parameter_left',$parameter_left ,'parameter_right',$parameter_right)
        ->save();
    
    //map編集時エラーメッセージ
    if(!$map_update->save()){
        //エラー内容
        //jsonでエラーメッセージの返却
        $err = array(
            'error' =>
        array( 
        array('code' => '452','message' => 'Insert error for database')),
    );
        echo json_encode($err);
        exit;
}
    
    //jsonで返却
    $response = array(
        'result' => true,
    );
    echo json_encode($response);
    
?>
