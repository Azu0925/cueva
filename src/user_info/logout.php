    <?php
    //これをsrc直下にコピーしてファイル名の「.sample」部分を削除して動かしてちょ

        use Cueva\Classes\ {Env, Func};

        require_once '../../vendor/j4mie/idiorm/idiorm.php';
        require '../../vendor/autoload.php';

        
        ORM::configure('mysql:host='.Env::get("HOST").';port='.Env::get("PORT").';dbname='.Env::get("DB_NAME"));
        ORM::configure('username', Env::get('USER_ID'));
        ORM::configure('password', Env::get("PASSWORD"));
        ORM::configure('driver_options', array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));

        $table = 'user'; //テーブルの名前
         $query = ORM::for_table($table)->find_one();
        // var_dump($query->as_array());


        $record = ORM::for_table('user')->where('token', 'ウンチ')->find_one();
        echo $record->user_id."\n";
        echo $record->token."\n";
        echo $record->user_password."\n";

        $people = ORM::for_table('user')->find_many();
        foreach ($people as $person) {
        $person->token = 'panpan';
         $person->save();
        }
/*
        $token = 'ウンチ';//$_POST['token'];//tokenを取得し変数へ格納
        try{
        $update = ORM::for_table($table)->where('token', 'ウンチ')->find_one();
        $update->token = 'パスワード';
        $update = save();
        var_dump($update);
    }catch (PDOException $e){
        echo "エラー発生：".$e->getMessage();
      }catch (Exception $e) {
        echo "エラー発生：".$e->getMessage();
      }
                var_dump($update);*/

        

