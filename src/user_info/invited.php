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

    $person = ORM::for_table('user')->where('token', $_POST['token'])->find_one();
    if($person !== false){
      $person->as_array();
    }else{
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

    $invite_list = ORM::for_table('member')->join('team', array('member.team_id', '=', 'team.id'))->where('member_invitation', 0)->find_array();
      if ($invite_list != false) {
        $list = [];
        foreach($invite_list as $row){
          $list[] = array(
            "team_id" => $row['team_id'],
            "team_name" => $row['team_name']
          );
        }
        $result = array(
          "result" => $list
        );
        echo json_encode($result, JSON_UNESCAPED_UNICODE);
        exit;
      }else{  //配列が取得できなかった場合
        $result = array(
          "result" => array()
        );
      echo json_encode($result, JSON_UNESCAPED_UNICODE);
      exit;
    }
