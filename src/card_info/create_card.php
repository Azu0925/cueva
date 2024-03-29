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
    $card_description = $_POST['card_description'];
    $card_x = $_POST['card_x'];
    $card_y = $_POST['card_y'];
    $card_width	= $_POST['card_width'];
    $card_height = $_POST['card_height'];
    $map_id = $_POST['map_id'];
    

    //tokenのバリデーションチェック
    if(!isset($token)){
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

    //card_nameのバリデーションチェック
    if (!preg_match("/^.{0,30}$/",$card_name)) {
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

    //card_descriptionのバリデーションチェック
    if (!preg_match("/^.{0,100}$/",$card_description)) {
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

    // var_dump($list);

    //tokenの照合
    if($user_list === false){
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

    //user_nameの取得
    $user_name = $user_list['user_name'];

    $map_list = ORM::for_table('map')->where('id', $map_id)->find_one();
    if($map_list === false){
        $err = array(
            "error" => array(
                'code' => '403',
                'message' => 'Forbidden'
            )
        );
        echo json_encode($err, JSON_UNESCAPED_UNICODE);
        exit;
    }
    $map_list->as_array();
    
    $member_list = ORM::for_table($member_table)
    ->where(
        array(
            'user_id' => $user_id,
            'team_id' => $map_list['team_id']
        )
    )
    ->find_one();

    //チームに所属していない場合false
    if($member_list === false){
        //エラー内容
        //jsonでエラーメッセージの返却
        $err = array('error' =>
        array( 
            array(
                'code' => '403',
                'message' => 'Forbidden'
                )
            ),
        );
        echo json_encode($err, JSON_UNESCAPED_UNICODE);
        exit;
    }
    $member_list->as_array();
    
    //現在の日時の取得
    $update_date = date('Y-m-d H:i:s');
    
    //カードの新規作成
    $create_card = ORM::for_table($card_table)->create();
    $create_card->card_name = $card_name;
    $create_card->card_description = $card_description;
    $create_card->update_date = $update_date;
    $create_card->update_user = $user_name;
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

