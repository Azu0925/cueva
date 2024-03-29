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
    if (isset($_POST['token']) && isset($_POST['map_id']) && isset($_POST['team_id'])) {
      $token = $_POST['token'];  //tokenを取得し変数へ格納
      $map_id = $_POST['map_id']; //map_idを取得し変数へ格納
      $team_id = $_POST['team_id']; //map_idを取得し変数へ格納
      $select = ORM::for_table('v_map_delete')
        ->where(array(
          'token' => $token,
          'm_id' => $map_id,
          't_id' => $team_id
        ))
        ->find_one();
      if ($select != false) { //map表示
        $information = ORM::for_table("map")->where(array(
          'id' => $map_id,
          'team_id' => $team_id
        ))
        ->find_one(); 
        if ($information != false) { //結果
          $result = array(
            "result" => array(
                "map_id" => $information->id,
                "map_name" => $information->map_name,
                "map_description" => $information->map_description,
                "team_id" => $information->team_id,
                "parameter_top" => $information->parameter_top,
                "parameter_under" => $information->parameter_under,
                "parameter_left" => $information->parameter_left,
                "parameter_right" => $information->parameter_right,
              )
          );
          echo json_encode($result, JSON_UNESCAPED_UNICODE);
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
          echo json_encode($error, JSON_UNESCAPED_UNICODE);
          exit;
        }
      } else { //token・map_id・team_idに関連性がなかった場合(チームメンバー以外の削除リクエスト)
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
