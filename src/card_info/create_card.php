<?php
//DB接続
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

    //tableの指定
    $user_table = 'user';
    $member_table = 'member';
    $team_table = 'team';
    $map_table = 'map';
    $card_table = 'card';

    //値の受け取り
    $token = $_POST['token'];
    $card_name = $_POST['card_name'];
    $card_description = $_POST['description'];
    $update_user = $_POST['update_user'];
    $card_x = $_POST['card_x'];
    $card_y = $_POST['card_y'];
    $card_width	= $_POST['card_width'];
    $card_height = $_POST['card_height'];
    
    //card_nameのバリデーションチェック
    if (!preg_match("/^.{1,30}$/",$card_name)) {
        $err = array(
            "error" => array(
            array(
                "code" => "400",
                "message" => "Bad Request"
            )
        )
        );
        echo json_encode($err, JSON_UNESCAPED_UNICODE);
        exit;
    }

    //map_descriptionのバリデーションチェック
    if (!preg_match("/^.{0,100}$/",$map_description)) {
        $err = array(
            "error" => array(
            array(
                "code" => "400",
                "message" => "Bad Request"
            )
        )
        );
        echo json_encode($err, JSON_UNESCAPED_UNICODE);
        exit;
    }

    //card_xのバリデーションチェック
    if(!is_numeric($card_x)){
        $err = array(
            "error" => array(
            array(
                "code" => "400",
                "message" => "Bad Request"
            )
        )
        );
        echo json_encode($err, JSON_UNESCAPED_UNICODE);
        exit;
    }

    //card_yのバリデーションチェック
    if(!is_numeric($card_y)){
        $err = array(
            "error" => array(
            array(
                "code" => "400",
                "message" => "Bad Request"
            )
        )
        );
        echo json_encode($err, JSON_UNESCAPED_UNICODE);
        exit;
    }

    //ccard_widthのバリデーションチェック
    if(!is_numeric($card_width)){
        $err = array(
            "error" => array(
            array(
                "code" => "400",
                "message" => "Bad Request"
            )
        )
        );
        echo json_encode($err, JSON_UNESCAPED_UNICODE);
        exit;
    }

    //ccard_heightのバリデーションチェック
    if(!is_numeric($card_height)){
        $err = array(
            "error" => array(
            array(
                "code" => "400",
                "message" => "Bad Request"
            )
        )
        );
        echo json_encode($err, JSON_UNESCAPED_UNICODE);
        exit;
    }

    //登録されているユーザー情報取得
    $user_list = ORM::for_table($user_table)->where('token', $token)->find_one();

    $list = [];
    foreach(ORM::for_table($user_table)->find_result_set() as $user_list) {
        $list = ($user_list->as_array('id','user_name','token'));
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

    //team_idの情報取得
    $team_list = ORM::for_table($team_table)->where('id', $team_id)->find_one();

    $list = [];
    foreach(ORM::for_table($team_table)->find_result_set() as $team_list) {
        $list = ($team_list->as_array('team_id'));
    }
    // var_dump($list);

    //team_idの照合
    if($team_id !== $team_list['team_id']){
        //エラー内容
        //jsonでエラーメッセージの返却
        $err = array('error' =>
        array( 
        array('code' => '403','message' => 'Forbidden')),
    );
        echo json_encode($err, JSON_UNESCAPED_UNICODE);
        exit;
    }

    //map_idの取得
    $map_id = $_POST['map_id'];

    //map_idの情報取得
    $map_list = ORM::for_table($map_table)->where('id', $map_id)->find_one();

    $list = [];
    foreach(ORM::for_table($map_table)->find_result_set() as $map_list) {
        $list = ($map_list->as_array('id','team_id'));
    }
    // var_dump($list);

    //map_idの照合
    if($map_id !== $map_list['map_id']){
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

    //現在の日時の取得
    $update_date = date('Y-m-d H:i:s');
    
    //カードの新規作成
    $create_card = ORM::for_table($card_table)->create();
    $create_card->card_name = $card_name;
    $create_card->card_discription = $card_discription;
    $create_card->update_date = $update_date;
    $create_card->update_user = $update_user;
    $create_card->card_x = $card_x;
    $create_card->card_y = $card_y;
    $create_card->card_width = $card_width;
    $create_card->card_height = $card_height;
    $create_card->map_id = $map_id;
    $create_card->save();

    //INSERT出来なかった時の処理
    if(!$create_card->save()){
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

