<?php
//DB接続
use Cueva\Classes\ {Env, Func};

    require_once '../../vendor/j4mie/idiorm/idiorm.php';
    require '../../vendor/autoload.php';

    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Headers: X-Requested-With, Origin, X-Csrftoken, Content-Type, Accept");
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, CONNECT, OPTIONS, TRACE, PATCH, HEAD");

    ORM::configure('mysql:host='.Env::get("HOST").';port='.Env::get("PORT").';dbname='.Env::get("DB_NAME"));
    ORM::configure('username', Env::get('USER_ID'));
    ORM::configure('password', Env::get("PASSWORD"));
    ORM::configure('driver_options', array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));

    //値受け取り
    $token = $_POST['token'];
    $team_id = $_POST['team_id'];

    //tableの指定
    $user_table = 'user';
    $member_table = 'member';
    $team_table = 'team';

    //登録されているユーザー情報取得
    $user_list = ORM::for_table($user_table)->where('token',$token)->find_one();
 
    $list = [];
    foreach(ORM::for_table($user_table)->find_result_set() as $user_list){
        $list = ($user_list->as_array('id','token'));
    }

    //tokenの照合
    if(!$token !==  $user_list['token']){
        $err = array('error' =>
        array(
        array('code' =>'401','messeage' => 'Unauthorized')),
    );
        echo json_encode($err, JSON_UNEESCAPE_UNICODE);
        exit;
}
    //user_idの取得
    $user_id = $user_list['id'];

    //メンバー情報取得
    $member_list = ORM::for_table($member_table)->where('user_id', $user_id)->find_one();

    $list = [];
    foreach(ORM::for_table($member_table)->find_result_set() as $member_list) {
        $list = ($member_list->as_array('user_id','team_id'));
    }
    // var_dump($list);

    //user_idの照合
    if($user_id !== $member_list['user_id']){
        //エラー内容
        //jsonでエラーメッセージの返却
        $err = array('error' =>
        array( 
        array('code' => '403','message' => 'Forbidden')),
    );
        echo json_encode($err, JSON_UNESCAPED_UNICODE);
        exit;
    }

    //team_idの取得
    $team_id = $list['team_id'];
    // var_dump($team_id);

    //team_idの情報取得
    $team_list = ORM::for_table($team_table)->where('id', $team_id)->find_one();

    $list = [];
    foreach(ORM::for_table($team_table)->find_result_set() as $team_list) {
        $list = ($team_list->as_array('id'));
    }
    //var_dump($list);

    //team_idの照合
    if($team_id !== $team_list['id']){
        //エラー内容
        //jsonでエラーメッセージの返却
        $err = array('error' =>
        array( 
        array('code' => '403','message' => 'Forbidden')),
    );
        echo json_encode($err, JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    //teamの削除
    if($team_id->delete()){
        //エラー内容
        //jsonでエラーメッセージの返却
        $err = array('error' =>
        array( 
        array('code' => '450','message' => 'Can not connected for database')),
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