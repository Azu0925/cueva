    <?php
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Headers: X-Requested-With, Origin, X-Csrftoken, Content-Type, Accept");
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, CONNECT, OPTIONS, TRACE, PATCH, HEAD");
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

    if (!isset($_POST['card_id'])) {  //card_idの値が入っていなければエラー
      $error = array(
        "error" => array(
          array(
            "code" => "400",
            "message" => "Bad Request"
          )
        )
      );
      echo json_encode($error, JSON_UNESCAPED_UNICODE);
      exit;
    }

    if (!isset($_POST['card_name'])) { //card_nameの値が入ってなければエラー
      $error = array(
        "error" => array(
          array(
            "code" => "400",
            "message" => "Bad Request"
          )
        )
      );
      echo json_encode($error, JSON_UNESCAPED_UNICODE);
      exit;
    }
    if (mb_strlen($_POST['card_description']) > 100) { //card_descriptionが100文字以上であればエラー
      $error = array(
        "error" => array(
          array(
            "code" => "400",
            "message" => "Bad Request"
          )
        )
      );
      echo json_encode($error, JSON_UNESCAPED_UNICODE);
      exit;
    }


    if (!is_numeric($_POST['card_x'])) {  //card_xがintでなければエラー
      $error = array(
        "error" => array(
          array(
            "code" => "40",
            "message" => "Bad Request"
          )
        )
      );
      echo json_encode($error, JSON_UNESCAPED_UNICODE);
      exit;
    }


    if (!is_numeric($_POST['card_y'])) {  //card_yがintでなければエラー
      $error = array(
        "error" => array(
          array(
            "code" => "400",
            "message" => "Bad Request"
          )
        )
      );
      echo json_encode($error, JSON_UNESCAPED_UNICODE);
      exit;
    }

    if (!is_numeric($_POST['card_width'])) {  //card_widthがintでなければエラー
      $error = array(
        "error" => array(
          array(
            "code" => "400",
            "message" => "Bad Request"
          )
        )
      );
      echo json_encode($error, JSON_UNESCAPED_UNICODE);
      exit;
    }


    if (!is_numeric($_POST['card_height'])) {  //card_heightがintでなければエラー
      $error = array(
        "error" => array(
          array(
            "code" => "400",
            "message" => "Bad Request"
          )
        )
      );
      echo json_encode($error, JSON_UNESCAPED_UNICODE);
      exit;
    }

    //tokenとmap_idの検索
    if (isset($_POST['token']) && isset($_POST['map_id'])) {
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
        if ($update != false) { //card更新処理２(更新内容POST値を変数へ代入・更新内容の挿入)
          //変数へ代入
          $card_name = $_POST['card_name'];
          $card_description = $_POST['card_description'];
          $card_x = $_POST['card_x'];
          $card_y = $_POST['card_y']; 
          $card_width = $_POST['card_width']; 
          $card_height = $_POST['card_height'];
          //変数をカラムに追加
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
          echo json_encode($result, JSON_UNESCAPED_UNICODE);
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
          echo json_encode($error, JSON_UNESCAPED_UNICODE);
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
        echo json_encode($error, JSON_UNESCAPED_UNICODE);
        exit;
      }
    }
    
    $error = array( //POSTの値に不備があった場合にエラー
      "error" => array(
        array(
          "code" => "453",
          "message" => "Paramter is null"
        )
      )
    );

    echo json_encode($error, JSON_UNESCAPED_UNICODE);
