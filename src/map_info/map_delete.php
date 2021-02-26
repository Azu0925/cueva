    <?php


    use Cueva\Classes\{Env, Func};

    require_once '../../vendor/j4mie/idiorm/idiorm.php';
    require '../../vendor/autoload.php';


    ORM::configure('mysql:host=' . Env::get("HOST") . ';port=' . Env::get("PORT") . ';dbname=' . Env::get("DB_NAME"));
    ORM::configure('username', Env::get('USER_ID'));
    ORM::configure('password', Env::get("PASSWORD"));
    ORM::configure('driver_options', array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));
    $table = 'user'; //テーブルの名前

    //$testToken = 'test';

    $delete = ORM::for_table('person')
            ->where(array(
                'name' => '山田太郎',
                'フィールド名' => '値'
            ))
            ->delete_many();

    //tokenカラムをNULLにアップデート
    if (isset($_POST['token']/*$testToken*/)) {
      $token = $_POST['token']; //tokenを取得し変数へ格納
      $update = ORM::for_table($table)->where('token', $token/*$testToken*/)->find_one();
      $update->token = NULL;
      $update->save();
    
    if (($update->save())) {
      $result = array(
        "result" => array(
          array(
            "result" => true
          )
        )
      );
      echo json_encode($result);
      exit;
    }else{
      $error = array(
        "error" => array(
          array(
            "code" => "452",
            "message" => "Update error for database"
          )
        )
      );
      echo json_encode($error);
      exit;
    }
  }
  $error = array(
    "error" => array(
      array(
        "code" => "404",
        "message" => "Not Found"
      )
    )
  );

    echo json_encode($error);
