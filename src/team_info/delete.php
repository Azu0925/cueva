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
    $table = 'v_map_delete'; //テーブルの名前

    //$testToken = 'test';

    //tokenとteam_idの検索
    if (isset($_POST['token']) && isset($_POST['team_id'])) {
      $token = $_POST['token'];  //tokenを取得し変数へ格納
      $team_id = $_POST['team_id']; //team_idを取得し変数へ格納
      $select = ORM::for_table('v_map_create')
        ->where(array(
          'token' => $token,
          't_id' => $team_id
        ))
        ->find_many();
      //var_dump($team_id);
      if ($select != false) { //team削除処理

        $data = array();
        //teamが一致するmap_idを取得
        $records = ORM::for_table('map')->where('team_id', $team_id)
          ->find_many();
        //該当するmap_idを配列に格納
        foreach ($records as $record) {
          $data[] = $record->id;
        }
        //配列に格納されたmap_idを元にカードを削除
        for ($i = 0; $i < count($data); $i++) {
          $cards = ORM::for_table('card')->where('map_id', $data[$i])
            ->delete_many();
        }

        $m_delete = ORM::for_table("member")->where('team_id', $team_id);
        $map_delete = ORM::for_table("map")->where('team_id', $team_id);
        $delete = ORM::for_table("team")->where('id', $team_id);
        if ($delete != false) {
          $m_delete->delete_many();
          $map_delete->delete_many();
          $delete->delete_many();
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
      } else { //tokenとteam_idに関連性がなかった場合(チームメンバー以外の削除リクエスト)
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
