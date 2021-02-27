    <?php
    //これをsrc直下にコピーしてファイル名の「.sample」部分を削除して動かしてちょ

    use Cueva\Classes\{Env, Func};

    require_once '../../vendor/j4mie/idiorm/idiorm.php';
    require '../../vendor/autoload.php';

  
    ORM::configure('mysql:host=' . Env::get("HOST") . ';port=' . Env::get("PORT") . ';dbname=' . Env::get("DB_NAME"));
    ORM::configure('username', Env::get('USER_ID'));
    ORM::configure('password', Env::get("PASSWORD"));
    ORM::configure('driver_options', array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));
    

    //tokenとmap_idの検索
    if (isset($_POST['token']) && isset($_POST['card_id'])) {
      $token = $_POST['token'];  //tokenを取得し変数へ格納
      $card_id = $_POST['card_id']; //map_idを取得し変数へ格納
      $select = ORM::for_table('v_card_info')
        ->where(array(
          'token' => $token,
          'c_id' => $card_id
        ))
        ->find_one();
      if ($select != false) { //card更新ユーザー名の取得
        $map_id = $select->m_id;//map_idの取得
        $information = ORM::for_table("card")->where(array(
          'id' => $card_id,
          'map_id' => $map_id
        ))
        ->find_array(); 
        if ($information != false) { //結果
          $result = $information;
          echo json_encode($result);
          exit;
        } else { //information取得失敗
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
      } else { //tokenとcard_idに関連性がなかった場合(チームメンバー以外の削除リクエスト)
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
    //tokenとcard_idが取得できなかった場合
    $error = array(
      "error" => array(
        array(
          "code" => "453",
          "message" => "Paramter is null"
        )
      )
    );

    echo json_encode($error);
