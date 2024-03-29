    <?php
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Headers: X-Requested-With, Origin, X-Csrftoken, Content-Type, Accept");
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, CONNECT, OPTIONS, TRACE, PATCH, HEAD");
    //これをsrc直下にコピーしてファイル名の「.sample」部分を削除して動かしてちょ

    use Cueva\Classes\{Env, Func};

    require_once '../../vendor/j4mie/idiorm/idiorm.php';
    require '../../vendor/autoload.php';

  
    ORM::configure('mysql:host=' . Env::get("HOST") . ';port=' . Env::get("PORT") . ';dbname=' . Env::get("DB_NAME"));
    ORM::configure('username', Env::get('USER_ID'));
    ORM::configure('password', Env::get("PASSWORD"));
    ORM::configure('driver_options', array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));
    

    //tokenとcard_idの検索
    if (isset($_POST['token']) && isset($_POST['card_id'])) {
      $token = $_POST['token'];  //tokenを取得し変数へ格納
      $card_id = $_POST['card_id']; //card_idを取得し変数へ格納
      $select = ORM::for_table('v_card_info')
        ->where(array(
          'token' => $token,
          'c_id' => $card_id
        ))
        ->find_one();
      if ($select != false) { //card表示ユーザー名の取得
        $map_id = $select->m_id;//map_idの取得
        $information = ORM::for_table("card")->where(array(
          'id' => $card_id,
          'map_id' => $map_id
        ))
        ->find_one(); 
        if ($information != false) { //結果
          $result = array(
            "result" => array(
              "card_id" => $information->id,
              "card_name" => $information->card_name,
              "card_description" => $information->card_description,
              "update_time" => $information->update_date,
              "update_user" => $information->update_user,
              "card_x" => $information->card_x,
              "card_y" => $information->card_y,
              "card_width" => $information->card_width,
              "card_height" => $information->card_height,
              "map_id" => $information->map_id
            )
          );
          echo json_encode($result, JSON_UNESCAPED_UNICODE);
          exit;
        } else { //information取得失敗
          $error = array(
            "error" => array(
              array(
                "code" => "452",
                "message" => "Reference error for database"
              )
            )
          );
          echo json_encode($error, JSON_UNESCAPED_UNICODE);
          exit;
        }
      } else { //tokenとcard_idに関連性がなかった場合(チームメンバー以外の削除リクエスト)
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
    //tokenとcard_idが取得できなかった場合
    $error = array(
      "error" => array(
        array(
          "code" => "453",
          "message" => "Paramter is null"
        )
      )
    );

    echo json_encode($error, JSON_UNESCAPED_UNICODE);
