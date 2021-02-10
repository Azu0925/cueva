<?php
namespace Cueva\Classes;

class DataBase {
    public function __construct($host, $user_id, $password, $db_name){
        $this->link = @mysqli_connect($host, $user_id, $password, $db_name);
        if (!$this->link) {
            $this->err_msg = '予期せぬエラーが発生しました。しばらくたってから再度お試しください。(エラーコード：103)';
            return;
        }
        mysqli_set_charset($this->link, 'utf8');
    }

    public function __destruct(){
        mysqli_close($this->link);
    }

    public function insert($link, $insert_list, $table){
        $i = 0;
        $insert_query_columns = '';
        $insert_query_values = '';
        foreach($insert_list as $column => $value){
            $i ++;
            if(count($insert_list) === $i){
                $insert_query_columns .= $column;
                $insert_query_values .= "? ";
            }else{
                $insert_query_columns .= $column.", ";
                $insert_query_values .= "?, ";
            }
        }
        $insert_query = "INSERT INTO ".$table."(".$insert_query_columns.") VALUES (".$insert_query_values.")";
        return $insert_query;
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
        if($select_list === '*'){
            return $sql.mysqli_real_escape_string($this->link, $select_list)." FROM ".$table;
        }
        foreach($select_list as $value){
            $i ++;
            if(count($select_list) === $i){
                $sql .= mysqli_real_escape_string($this->link, $value).' ';
            }else{
                $sql .= mysqli_real_escape_string($this->link, $value).', ';
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
                $update_set_list .= mysqli_real_escape_string($this->link, $column)."='".mysqli_real_escape_string($this->link, $value)."'";
            }else{
                $update_set_list .= mysqli_real_escape_string($this->link, $column)."='".mysqli_real_escape_string($this->link, $value)."', ";
            }
        }
        return "UPDATE ".$table." SET ".$update_set_list." ";
    }

    public function setWhere($column, $value){
        return " WHERE ".mysqli_real_escape_string($this->link, $column)."=".mysqli_real_escape_string($this->link, $value);
    }

    public function setJoin(){
    }

    public function setOrder(){
    }
    
    public function groupBy(){
        
    }
}