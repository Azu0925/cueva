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
        // $query = ORM::for_table($table)->find_one();
        // var_dump($query->as_array());
        

        $token = 'ウンチ';//$_POST['token'];//tokenを取得し変数へ格納
        $id = 1;
        $update = ORM::for_table($table)->where('user_id', $id)->find_result_set()
        ->set('token', 'pasu')
        ->save();
                var_dump($update);

        
