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


    //tokenとmap_idの検索
    if (isset($_POST['token']) && isset($_POST['map_id'])) {
      $token = $_POST['token'];  //tokenを取得し変数へ格納 1234
      $map_id = $_POST['map_id']; //map_idを取得し変数へ格納 2

      $id = array();
      //token・map_idが一致するcard_idを取得
      $records = ORM::for_table('v_card_info')->where(array(
        'token' => $token,
        'm_id' => $map_id
      ))
        ->find_many();
      if ($records != false) {//$recordsに値が入っていればtrue
        $card = ORM::for_table('card')->where('map_id', $map_id)->find_array();
        $result = $card;
        echo json_encode($result, JSON_UNESCAPED_UNICODE);
        exit;
      } else {//$records取得失敗
        $result = array(
          "data" => array(
            
          )
        );
        echo json_encode($result, JSON_UNESCAPED_UNICODE);
        exit;
      }
    } else{
      //tokenとmap_idが取得できなかった場合
      $error = array(
      "error" => array(
        array(
          "code" => "453",
          "message" => "Paramter is null"
        )
      )
    );
    echo json_encode($error, JSON_UNESCAPED_UNICODE);
    exit;
    }
