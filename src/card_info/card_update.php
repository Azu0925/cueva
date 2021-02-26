    <?php
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
      if ($select != false) { //map削除処理
        $delete = ORM::for_table("map")->where('id', $map_id)->find_one();
        if ($delete != false) {
          $delete->delete();
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
              "code" => "400",
              "message" => "Bad Request"
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
          "code" => "404",
          "message" => "Not Found"
        )
      )
    );

    echo json_encode($error);
