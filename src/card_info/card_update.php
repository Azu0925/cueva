    <?php
    //これをsrc直下にコピーしてファイル名の「.sample」部分を削除して動かしてちょ

    use Cueva\Classes\{Env, Func};

    require_once '../../vendor/j4mie/idiorm/idiorm.php';
    require '../../vendor/autoload.php';

    date_default_timezone_set('Asia/Tokyo'); //タイムゾーン設定
    ORM::configure('mysql:host=' . Env::get("HOST") . ';port=' . Env::get("PORT") . ';dbname=' . Env::get("DB_NAME"));
    ORM::configure('username', Env::get('USER_ID'));
    ORM::configure('password', Env::get("PASSWORD"));
    ORM::configure('driver_options', array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));
    $table = 'v_map_delete'; //テーブルの名前


     
    if (!isset($_POST['card_name'])) { //バリデーションチェック
      $error = array(
        "error" => array(
          array(
            "code" => "400",
            "message" => "Bad Request"
          )
        )
      );
      echo json_encode($error);
      exit;
    }

    
    if (!is_int($_POST['card_x'])) {  //バリデーションチェック
      $error = array(
        "error" => array(
          array(
            "code" => "400",
            "message" => "Bad Request"
          )
        )
      );
      echo json_encode($error);
      exit;
    }

    
    if (!is_int($_POST['card_y'])) {  //バリデーションチェック
      $error = array(
        "error" => array(
          array(
            "code" => "400",
            "message" => "Bad Request"
          )
        )
      );
      echo json_encode($error);
      exit;
    }
  
    if (!is_int($_POST['card_width'])) {  //バリデーションチェック
      $error = array(
        "error" => array(
          array(
            "code" => "400",
            "message" => "Bad Request"
          )
        )
      );
      echo json_encode($error);
      exit;
    }

    
    if (!is_int($_POST['card_height'])) {  //バリデーションチェック
      $error = array(
        "error" => array(
          array(
            "code" => "400",
            "message" => "Bad Request"
          )
        )
      );
      echo json_encode($error);
      exit;
    }

    //tokenとmap_idの検索
    if (isset($_POST['token']) && isset($_POST['map_id'])) {

      $card_name = $_POST['card_name'];//card_name 作成するカードの名前
      $card_description = $_POST['card_description']; //card_description 作成するカードの詳細
      $card_x = $_POST['card_x']; //card_x カードのx座標
      $card_y = $_POST['card_y']; //card_y カードのy座標
      $card_width = $_POST['card_width']; //card_width カードの横幅
      $card_height = $_POST['card_height']; //card_height カードの縦の長さ

      $token = $_POST['token'];  //tokenを取得し変数へ格納
      $map_id = $_POST['map_id']; //map_idを取得し変数へ格納
      $select = ORM::for_table('v_map_delete')
        ->where(array(
          'token' => $token,
          'm_id' => $map_id
        ))
        ->find_many();
      if ($select != false) { //card更新ユーザー名の取得
        $record = ORM::for_table('user')->where('token', $token)->find_one();
        $update_user = $record->name;
        $card_id = $_POST['card_id']; //card_id 更新するカードのid
        $update = ORM::for_table("card")->where('id', $card_id)->find_one(); //card更新処理１
        if ($update != false) { //card更新処理２(更新内容の挿入)
          $update->card_name = $card_name;
          $update->card_description = $card_description;
          $update->update_date = date("Y/m/d H:i:s");
          $update->update_user = $update_user;
          $update->card_x = $card_x;
          $update->card_y = $card_y;
          $update->card_width = $card_width;
          $update->card_height = $card_height;
          $update->save(); //更新
          $result = array(
            "result" => array(
              array(
                "result" => true
              )
            )
          );
          echo json_encode($result);
          exit;
        } else { //Dleteエラー処理
          $error = array(
            "error" => array(
              array(
                "code" => "452",
                "message" => "Delete error for database"
              )
            )
          );
          echo json_encode($error);
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
    }
    //tokenとmap_idが取得できなかった場合
    $error = array(
      "error" => array(
        array(
          "code" => "453",
          "message" => "Paramter is null"
        )
      )
    );

    echo json_encode($error);
