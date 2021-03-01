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

    //テーブルの名前
    $table = 'card';
    //カード作成者がチームに所属しているかどうか
if(isset($_POST['token']) && is_int($_POST['card_x']) && is_int($_POST['card_x']) && is_int($_POST['card_width']) && is_int($_POST['card_height'])&& is_int($_POST['map_id'])){
    $card_name = $_POST['card_name']; //card_name 作成するカードの名前
    $update_user = $_POST['update_user'];
    $card_description = $_POST['description'];//カードの説明文
    $update_date = date('Y-m-d H:i:s');//作成日
    $card_x = $_POST['card_x'];//座標X
    $card_y = $_POST['card_y'];//座標Y
    $card_width	= $_POST['card_width'];//カードの横幅
    $card_height = $_POST['card_height'];//カードの縦幅
    //名前の文字数制限
    $max = 30;
    $min = 1;
    $name_len = strlen($card_name);
    if($max < $name_len || $name_len < $min){
        $error = array(
            "error" => array(
                array(
                    "code" => "451",
                    "message" => "Validation error for 'card_name'"
                )
            )
        );
        echo json_encode($error);
        exit;
    }
//カードの説明の文字数制限
    $max = 100;
    $description_len = strlen($card_description);
    if($max < $description_len){
        $error = array(
            "error" => array(
                array(
                    "code" => "451",
                    "message" => "Validation error for 'card_description'"
                )
            )
        );
        echo json_encode($error);
        exit;
    }
    $token = $_POST['token'];  //tokenを取得し変数へ格納
    $map_id = $_POST['map_id']; //map_idを取得し変数へ格納
    $select = ORM::for_table('v_map_delete')
        ->where(array(
        'token' => $token,
        'm_id' => $map_id
        ))
        ->find_many();
    if ($select != false) { //card作成ユーザー名の取得
        $record = ORM::for_table('user')->where('token', $token)->find_one();
        $update_user = $record->name;
    if ($update_user != false) { //card更新処理２(更新内容の挿入)
        //カード情報の登録  
        //名前と説明以外は初期値が入っている前提
        $person = ORM::for_table($table)->create();
        $person->card_name = $card_name;
        $person->card_discription = $card_discription;
        $person->update_date =$update_date;
        $person->update_user =$update_user;
        $person->card_x =$card_x;
        $person->card_y =$card_y;
        $person->card_width =$card_width;
        $person->card_height =$card_height;
        $person->save();
        $result = array(
            "result" => array(
            array(
                "result" => true,
                "person" -> $person,
            )
            )
        );
        echo json_encode($result,JSON_UNESCAPED_UNICODE);
        exit;
        } 
    } else { //tokenとmap_idに関連性がなかった場合(チームメンバー以外の削除リクエスト)
        $error = array(
        "error" => array(
            array(
            "code" => "403",
            "message" => "Forbidden"
            )
        )
        );
        echo json_encode($error);
        exit;
    }
    
}else{
    //tokenとmap_idが取得できなかった場合
    $error = array(
    "error" => array(
        array(
        "code" => "400",
        "message" => "Bad Request"
        )
    )
    );
    echo json_encode($error);
}
?>

