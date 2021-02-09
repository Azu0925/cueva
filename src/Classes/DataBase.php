<?php
    namespace Cueva\Classes;

    use Cueva\Classes\Env;

    require __DIR__.'/vendor/autoload.php';
    
    class DataBase {
        public function __construct(){
            $this->link = @mysqli_connect(Env::get("HOST"), Env::get("USER_ID"), Env::get("PASSWORD"), Env::get("DB_NAME"));
            mysqli_set_charset($this->link, 'utf8');
            if(!$this->link){
                $err_msg = '予期せぬエラーが発生しました。しばらくたってから再度お試しください。(エラーコード：103)';
                return $err_msg;
            }

            return $this->link;
        }

        public function insert($link, $insert_list, $table){
            $i = 0;
            $insert_query_columns = '';
            $insert_query_values = '';
            foreach($insert_list as $column => $value){
                $i ++;
                if(count($insert_list) === $i){
                    $insert_query_columns .= $column;
                    $insert_query_values .= "'".mysqli_real_escape_string($this->link, $value)."'";
                }else{
                    $insert_query_columns .= $column.", ";
                    $insert_query_values .= "'".mysqli_real_escape_string($this->link, $value)."', ";
                }
            }
            $insert_query = "INSERT INTO ".$table."(".$insert_query_columns.") VALUES (".$insert_query_values.")";
            return mysqli_query($link, $insert_query);
        }

        public function select($link, $select_query){
            $table = [];
            $row = [];
            $query = mysqli_query($link, $select_query);
            while($row = mysqli_fetch_assoc($query)){
                $table[] = $row;
            }
            return $table;
        }

        public function update($link, $update_query){
            return mysqli_query($link, $update_query);
        }

        public function setSelect($select_list, $table){
            $sql = "SELECT ";
            $i = 0;

            foreach($select_list as $value){
                $i ++;
                if(count($select_list) === $i){
                    $sql .= $value.' ';
                }else{
                    $sql .= $value.', ';
                }
            }
            return $sql." FROM ".$table;
        }

        public function setUpdate($update_list, $table){
            $i = 0;
            $update_set_list = '';
            foreach($update_list as $column => $value){
                $i ++;
                if(count($update_list) === $i){
                    $update_set_list .= $column."='".$value."'";
                }else{
                    $update_set_list .= $column."='".$value."', ";
                }
            }
            return "UPDATE ".$table." SET ".$update_set_list." ";
        }

        public function setWhere(){

        }

        public function setJoin(){

        }

        public function setOrder(){

        }

        public function groupBy(){
            
        }
    }