    <?php
    //これをsrc直下にコピーしてファイル名の「.sample」部分を削除して動かしてちょ

        use Cueva\Classes\ {Env, Func};

        require_once '../../vendor/j4mie/idiorm/idiorm.php';
        require '../../vendor/autoload.php';

        
        ORM::configure('mysql:host='.Env::get("HOST").';port='.Env::get("PORT").';dbname='.Env::get("DB_NAME"));
        ORM::configure('username', Env::get('USER_ID'));
        ORM::configure('password', Env::get("PASSWORD"));

        $table = 'user'; //テーブルの名前
        // $query = ORM::for_table($table)->find_one();
        // var_dump($query->as_array());
        

        $token = NULL;//$_POST['token'];//tokenを取得し変数へ格納
        var_dump($token);
        $update = ORM::for_table($table)->where('token', $token) ->find_one();
        $update->token = "ウンチ";
                $update->save('token', $token);
                var_dump($update);

        // {
        //     "result": true
            
        // }
        // {
        //     "error": [
        //     {
        //         "code": エラーコード(int),
        //         "message": エラーメッセージ(string)
        //     },
        //     {
        //         "code": 同上,
        //         "message": 同上
        //     }
        //     ]
        // }
        

