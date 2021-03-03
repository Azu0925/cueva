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

    if (isset($_POST['token'])) {//tokenが取得できた場合
      $token =  $_POST['token'];
      $select = ORM::for_table('v_user_invited')->where('token', $token)
      ->find_array();
      if ($select != false) {
        $result = $select;
        echo json_encode($result);
        exit;
      }else{
        //tokenが取得できなかった場合
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
    }else{
      //tokenが取得できなかった場合
      $error = array(
        "error" => array(
          array(
            "code" => "453",
            "message" => "Paramter is null"
          )
        )
      );
      echo json_encode($error, JSON_UNESCAPED_UNICODE);
    }
