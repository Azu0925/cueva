<?php
    namespace Cueva\Classes;
    use Cueva\Classes\ {Env, Func};
    use Ratchet\MessageComponentInterface;
    use Ratchet\ConnectionInterface;

    class Map implements MessageComponentInterface {
        protected $clients;
        private $subscriptions;
        private $users;

        public function __construct() {
            $this->clients = new \SplObjectStorage;
            $this->subscriptions = [];
            $this->tables = [];
            $this->users = [];
        }

        public function onOpen(ConnectionInterface $conn) {
            // Store the new connection to send messages to later
            $this->clients->attach($conn);
            $this->users[$conn->resourceId] = $conn;
            echo "New connection! ({$conn->resourceId})\n";
        }

        public function onMessage(ConnectionInterface $conn, $msg){
            $data = json_decode($msg);
            if ($data->command === "subscribe") {
                $this->subscriptions[$conn->resourceId] = $data->channel;
                echo '接続：'.$data->channel.PHP_EOL;
            }elseif($data->command === "notification"){
                if (isset($this->subscriptions[$conn->resourceId])) {
                    $target = $this->subscriptions[$conn->resourceId];
                    foreach ($this->subscriptions as $id=>$channel) {
                        if ($channel == $target) {
                            // インスタンス生成
                            $env = new Env;
                            // DB接続
                            $link = @mysqli_connect($env->get("HOST"), $env->get("USER_ID"), $env->get("PASSWORD"), $env->get("DB_NAME"));
                            mysqli_set_charset($link, 'utf8');
                            // $dataが数値型かどうか
                            //var_dump(!is_numeric($data->message));
                            
                            if(!is_numeric($data->message)){
                                $error = array(
                                    "error" => array(
                                        array(
                                            "code" => "451",
                                            "message" => "Validation error for 'message'"
                                        )
                                    )
                                );
                                $json_member_list = json_encode($error, JSON_UNESCAPED_UNICODE);
                            }else{
                                // 返却するリスト生成
                                $query = mysqli_query($link, "SELECT count(id) FROM member WHERE user_id = ".$data->message." AND member_invitation=0");
                                $member_list = array(
                                    "event" => "notifiication",
                                    "data" => mysqli_fetch_assoc($query)["count(id)"]
                                );
                                // var_dump($member_list);
                                // JSON形式で返却
                                $json_member_list = json_encode($member_list, JSON_UNESCAPED_UNICODE);
                            }
                            $this->users[$id]->send($json_member_list);
                            mysqli_close($link);
                        }                       
                    }
                }
            }
                /*case "maps":
                    if (isset($this->subscriptions[$conn->resourceId])) {
                        $target = $this->subscriptions[$conn->resourceId];
                        foreach ($this->subscriptions as $id=>$channel) {
                            if ($channel == $target) {
                                // インスタンス生成
                                $env = new Env;
                                // DB接続
                                $link = @mysqli_connect($env->get("HOST"), $env->get("USER_ID"), $env->get("PASSWORD"), $env->get("DB_NAME"));
                                mysqli_set_charset($link, 'utf8');

                                // $dataが数値型かどうか
                                if(!is_numeric($data)){
                                    $error = array(
                                        "error" => array(
                                            array(
                                                "code" => "451",
                                                "message" => "Validation error for 'message'"
                                            )
                                        )
                                    );
                                    //echo json_encode($error, JSON_UNESCAPED_UNICODE);
                                    break;
                                }

                                // 返却するリスト生成
                                $query = mysqli_query($link, "SELECT * FROM map WHERE team_id = ".$data->message);
                                $maps_list = [];
                                while($row = mysqli_fetch_assoc($query)){
                                    $maps_list[] = $row;
                                }
                                // var_dump($maps_list);

                                // JSON形式で返却
                                $json_maps_list = json_encode($maps_list, JSON_UNESCAPED_UNICODE);
                                $this->users[$id]->send($json_maps_list);
                            }                       
                        }
                    }
                */
            elseif($data->command === "update_parameter"){
                if (isset($this->subscriptions[$conn->resourceId])) {
                    $target = $this->subscriptions[$conn->resourceId];
                    foreach ($this->subscriptions as $id=>$channel) {
                        if ($channel == $target) {
                            // インスタンス生成
                            $env = new Env;
                            // DB接続
                            $link = @mysqli_connect($env->get("HOST"), $env->get("USER_ID"), $env->get("PASSWORD"), $env->get("DB_NAME"));
                            mysqli_set_charset($link, 'utf8');
                            // $dataが数値型かどうか
                            if (!is_numeric($data->message)) {
                                $error = array(
                                    "error" => array(
                                        array(
                                            "code" => "451",
                                            "message" => "Validation error for 'message'"
                                        )
                                    )
                                );
                                $json_map_list = json_encode($error, JSON_UNESCAPED_UNICODE);
                            } else {
                                // 返却するリスト生成 ".$data->message
                                $query = mysqli_query($link, "SELECT * FROM map WHERE id = ".$data->message);
                                $map_list = mysqli_fetch_assoc($query);
                                // var_dump($map_list);
                                $result = array(
                                    "event" => "update_parameter",
                                    "data" => $map_list
                                );
                                // JSON形式で返却
                                $json_map_list = json_encode($result, JSON_UNESCAPED_UNICODE);
                            }
                            $this->users[$id]->send($json_map_list);
                            mysqli_close($link);
                        }
                    }
                }
            }elseif($data->command === "update_data"){
                if (isset($this->subscriptions[$conn->resourceId])) {
                    $target = $this->subscriptions[$conn->resourceId];
                    foreach ($this->subscriptions as $id=>$channel) {
                        if ($channel == $target) {
                            // インスタンス生成
                            $env = new Env;
                            // DB接続
                            $link = @mysqli_connect($env->get("HOST"), $env->get("USER_ID"), $env->get("PASSWORD"), $env->get("DB_NAME"));
                            mysqli_set_charset($link, 'utf8');
                            // $dataが数値型かどうか
                            if(!is_numeric((int)$data->message)){
                                $error = array(
                                    "error" => array(
                                        array(
                                            "code" => "451",
                                            "message" => "Validation error for 'message'"
                                        )
                                    )
                                );
                                $json_cards_list = json_encode($error, JSON_UNESCAPED_UNICODE);
                            }else{
                                // 返却するリスト生成
                                $query = mysqli_query($link, "SELECT * FROM card WHERE map_id = ".$data->message);
                                $cards_list = [];
                                while($row = mysqli_fetch_assoc($query)){
                                    $cards_list[] = $row;
                                }
                                // var_dump($cards_list);
                                $result = array(
                                    "event" => "update_map",
                                    "data" => $cards_list
                                );
                                // JSON形式で返却
                                echo "カード更新成功：".$target.PHP_EOL;
                                $json_cards_list = json_encode($result, JSON_UNESCAPED_UNICODE);
                            }
                            $this->users[$id]->send($json_cards_list);
                            mysqli_close($link);
                        }                   
                    }
                }
            }else{
            }
                /*
                case "card":
                    if (isset($this->subscriptions[$conn->resourceId])) {
                        $target = $this->subscriptions[$conn->resourceId];
                        foreach ($this->subscriptions as $id=>$channel) {
                            if ($channel == $target) {
                                // インスタンス生成
                                $env = new Env;
                                // DB接続
                                $link = @mysqli_connect($env->get("HOST"), $env->get("USER_ID"), $env->get("PASSWORD"), $env->get("DB_NAME"));
                                mysqli_set_charset($link, 'utf8');

                                // $dataが数値型かどうか
                                if(!is_numeric($data)){
                                    $error = array(
                                        "error" => array(
                                            array(
                                                "code" => "451",
                                                "message" => "Validation error for 'message'"
                                            )
                                        )
                                    );
                                    echo json_encode($error, JSON_UNESCAPED_UNICODE);
                                    break;
                                }

                                // 返却するリスト生成
                                $query = mysqli_query($link, "SELECT * FROM card WHERE id = ".$data->message);
                                $card_list = [];
                                while($row = mysqli_fetch_assoc($query)){
                                    $card_list[] = $row;
                                }
                                // var_dump($card_list);

                                // JSON形式で返却
                                $json_card_list = json_encode($card_list, JSON_UNESCAPED_UNICODE);
                                $this->users[$id]->send($json_card_list);
                            }                   
                        }
                    }
                */
        }

        public function onClose(ConnectionInterface $conn) {
            // The connection is closed, remove it, as we can no longer send it messages
            $this->clients->detach($conn);
            unset($this->users[$conn->resourceId]);
            unset($this->subscriptions[$conn->resourceId]);
            echo "Connection {$conn->resourceId} has disconnected\n";
        }

        public function onError(ConnectionInterface $conn, \Exception $e) {
            echo "An error has occurred: {$e->getMessage()}\n";

            $conn->close();
        }
    }