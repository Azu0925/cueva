<?php


    $token = $_POST['token']
    $person = ORM::for_table($table)->where('token', $token)->find_one();
    $person->token = null;
    $person->save()
    {
        "result": true
      }
      {
        "error": [
          {
            "code": エラーコード(int),
            "message": エラーメッセージ(string)
          },
          {
            "code": 同上,
            "message": 同上
          }
        ]
      }
      ?>
    

