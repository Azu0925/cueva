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

    if (isset($_POST['map_name'])) {
      # code...

      $map_name = $_POST['map_name']; //card_name 作成するカードの名前
      if ((isset($card_name) == false) && ($card_name == NULL)) { //バリデーションチェック
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
      $map_description = $_POST['map_description']; //card_description 作成するカードの詳細

      //tokenとteam_idの検索
      if (isset($_POST['token']) && isset($_POST['team'])) {
        $token = $_POST['token'];  //tokenを取得し変数へ格納
        $team_id = $_POST['team_id']; //map_idを取得し変数へ格納
        $select = ORM::for_table('v_map_create')
          ->where(array(
            'token' => $token,
            't_id' => $team_id
          ))
          ->find_many();
        if ($select != false) { //card更新ユーザー名の取得
          $record = ORM::for_table('user')->where('token', $token)->find_one();
          $create_user = $record->name;
          $create_map = ORM::for_table('map')->create(); //map新規作成処理1
          if ($update != false) { //card更新処理２(更新内容の挿入)
            $create_map->map_name = $card_name;
            $create_map->map_description = $card_description;
            $create_map->map_create = date("Y/m/d H:i:s");
            $create_map->map_host = $create_user;
            $create_map->save(); //更新
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
    }
    //tokenとteam_idが取得できなかった場合
    $error = array(
      "error" => array(
        array(
          "code" => "453",
          "message" => "Paramter is null"
        )
      )
    );

    echo json_encode($error, JSON_UNESCAPED_UNICODE);
