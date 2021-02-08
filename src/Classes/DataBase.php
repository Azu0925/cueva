<?php
    namespace Cueva\Classes;
    
    class DataBase {
        const HOST = 'localhost';
        const USER_ID = 'root';
        const PASSWORD = '';
        const DB_NAME = 'retas';

        public function __construct(){
            $this->link = @mysqli_connect(self::HOST, self::USER_ID, self::PASSWORD, self::DB_NAME);
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
                    $insert_query_values .= "'".$value."'";
                }else{
                    $insert_query_columns .= $column.", ";
                    $insert_query_values .= "'".$value."', ";
                }
            }

            $insert_query = "INSERT INTO ".$table."(".$insert_query_columns.") VALUES (".$insert_query_values.")";

            return mysqli_query($link, $insert_query);
        }

        public function setSelect($link, $select_list, $table_name, $join = null, $where = null, $order = null){
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
            return $sql;
        }

        public function setWhere(){

        }

        public function setJoin(){

        }

        public function setorder(){

        }
    }
