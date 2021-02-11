<?php
namespace Cueva\Classes;

class Func {
    public static function is_matched($value, $checked_value){
        if ($value === $checked_value) {
            return true;
        }
        return false;
    }

    /*
    function upload_img($upload_file, $name){
        $path_info = pathinfo($upload_file['name']);
        $file_name = './data/img/'.$name.'.'.$path_info['extension'];
        if(move_uploaded_file($upload_file['tmp_name'], $file_name)){
            return $file_name;
        }
        return false;
    }
    */

    //日付の形式を変換
    public static function date_ja_format($date){
        return date('Y年m月d日', strtotime($date));
    }
  
    //日付を数値のみに変換
    public static function date_to_num($date){
        if ($date == '') {
            return $date;
        }
        return date('Ymd', strtotime($date));
    }

    //引数に値が入ってなかったらNULLを入れる
    public static function is_value($value){
        if ($value == '') {
            return 'NULL';
        }
        return $value;
    }

    /*送られてきた文字列をスペースごとに分割しては配列で返す。
    $keywords :string
    返り値 :array
    ※全角スペースはすべて半角に置き換えてます。
    */

    public static function keywords_array($keywords){
        $keywords = str_replace('　', ' ', $keywords);
        return explode(' ', $keywords);
    }

    //xss対策
    public static function hsc($text){
        return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
    }
}