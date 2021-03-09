    <?php
    //これをsrc直下にコピーしてファイル名の「.sample」部分を削除して動かしてちょ

    use Cueva\Classes\{Env, Func};

    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Headers: X-Requested-With, Origin, X-Csrftoken, Content-Type, Accept");
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, CONNECT, OPTIONS, TRACE, PATCH, HEAD");

    require_once '../../vendor/j4mie/idiorm/idiorm.php';
    require '../../vendor/autoload.php';

    date_default_timezone_set('Asia/Tokyo'); //タイムゾーン設定
    ORM::configure('mysql:host=' . Env::get("HOST") . ';port=' . Env::get("PORT") . ';dbname=' . Env::get("DB_NAME"));
    ORM::configure('username', Env::get('USER_ID'));
    ORM::configure('password', Env::get("PASSWORD"));
    ORM::configure('driver_options', array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));
    $table = 'v_map_delete'; //テーブルの名前


    $map_description = $_POST['map_description'];
    $map_name = $_POST['map_name']; 
    $parameter_top = $_POST['parameter_top'];
    $parameter_under = $_POST['parameter_under'];
    $parameter_left = $_POST['parameter_left'];
    $parameter_right = $_POST['parameter_right'];

    //tokenとteam_idの検索
    if (isset($_POST['token']) && isset($_POST['team_id'])) {
      if (mb_strlen($map_description) > 100 || $_POST['map_name'] == '') {//map_nameは空だとerr・map_descriptionは100文字以上だとerr
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
      $token = $_POST['token'];  //tokenを取得し変数へ格納
      $team_id = $_POST['team_id']; //map_idを取得し変数へ格納
      $select = ORM::for_table('v_map_create')
        ->where(array(
          'token' => $token,
          't_id' => $team_id
        ))
        ->find_many();
      if ($select != false) { //map更新ユーザー名の取得
        $create_user = ORM::for_table('user')->where('token', $token)->find_one();
        $create_user->user_name;
        $create_map = ORM::for_table('map')->create(); //map新規作成処理1
        if ($create_map != false) { //map更新処理(内容の挿入)
          if (!isset($_POST['map_name'])) { //map_name 作成するmapの名前がなかったらfalse
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
          $create_map->map_name = $map_name;
          $create_map->map_description = $map_description;
          $create_map->team_id = $team_id;
          $create_map->map_create = date("Y/m/d H:i:s");
          $create_map->map_host = $create_user->user_name;
          $create_map->parameter_top = $parameter_top;
          $create_map->parameter_under = $parameter_under;
          $create_map->parameter_left = $parameter_left;
          $create_map->parameter_right = $parameter_right;
          $create_map->save(); //更新
          $id = $create_map->id;
          $result = array(
            "result" => array(
              array(
                "map_id"=>$id
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
                "message" => "Insert error for database"
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
