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
            switch ($data->command) {
                case "subscribe":
                    $this->subscriptions[$conn->resourceId] = $data->channel;
                    break;
                case "invite":
                    if (isset($this->subscriptions[$conn->resourceId])) {
                        $target = $this->subscriptions[$conn->resourceId];
                        foreach ($this->subscriptions as $id=>$channel) {
                            if ($channel == $target) {
                                // インスタンス生成
                                $env = new Env;
                                // DB接続
                                $link = @mysqli_connect($env->get("HOST"), $env->get("USER_ID"), $env->get("PASSWORD"), $env->get("DB_NAME"));
                                mysqli_set_charset($link, 'utf8');

                                // 返却するリスト生成
                                $query = mysqli_query($link, "SELECT * FROM member WHERE member_invitetion = ".$data->message);
                                $member_list = [];
                                while($row = mysqli_fetch_assoc($query)){
                                    $member_list[] = $row;
                                }
                                // var_dump($member_list);

                                // JSON形式で返却
                                $json_member_list = json_encode($member_list);
                                $this->users[$id]->send($json_member_list);
                            }                       
                        }
                    }
                case "maps":
                    if (isset($this->subscriptions[$conn->resourceId])) {
                        $target = $this->subscriptions[$conn->resourceId];
                        foreach ($this->subscriptions as $id=>$channel) {
                            if ($channel == $target) {
                                // インスタンス生成
                                $env = new Env;
                                // DB接続
                                $link = @mysqli_connect($env->get("HOST"), $env->get("USER_ID"), $env->get("PASSWORD"), $env->get("DB_NAME"));
                                mysqli_set_charset($link, 'utf8');

                                // 返却するリスト生成
                                $query = mysqli_query($link, "SELECT * FROM map WHERE team_id = ".$data->message);
                                $maps_list = [];
                                while($row = mysqli_fetch_assoc($query)){
                                    $maps_list[] = $row;
                                }
                                // var_dump($maps_list);

                                // JSON形式で返却
                                $json_maps_list = json_encode($maps_list);
                                $this->users[$id]->send($json_maps_list);
                            }                       
                        }
                    }
                case "map":
                    if (isset($this->subscriptions[$conn->resourceId])) {
                        $target = $this->subscriptions[$conn->resourceId];
                        foreach ($this->subscriptions as $id=>$channel) {
                            if ($channel == $target) {
                                // インスタンス生成
                                $env = new Env;
                                // DB接続
                                $link = @mysqli_connect($env->get("HOST"), $env->get("USER_ID"), $env->get("PASSWORD"), $env->get("DB_NAME"));
                                mysqli_set_charset($link, 'utf8');

                                // 返却するリスト生成
                                $query = mysqli_query($link, "SELECT * FROM map WHERE id = ".$data->message);
                                $map_list = [];
                                while($row = mysqli_fetch_assoc($query)){
                                    $map_list[] = $row;
                                }
                                // var_dump($map_list);

                                // JSON形式で返却
                                $json_map_list = json_encode($map_list);
                                $this->users[$id]->send($json_map_list);
                            }                       
                        }
                    }
                case "cards":
                    if (isset($this->subscriptions[$conn->resourceId])) {
                        $target = $this->subscriptions[$conn->resourceId];
                        foreach ($this->subscriptions as $id=>$channel) {
                            if ($channel == $target) {
                                // インスタンス生成
                                $env = new Env;
                                // DB接続
                                $link = @mysqli_connect($env->get("HOST"), $env->get("USER_ID"), $env->get("PASSWORD"), $env->get("DB_NAME"));
                                mysqli_set_charset($link, 'utf8');

                                // 返却するリスト生成
                                $query = mysqli_query($link, "SELECT * FROM card WHERE map_id = ".$data->message);
                                $cards_list = [];
                                while($row = mysqli_fetch_assoc($query)){
                                    $cards_list[] = $row;
                                }
                                // var_dump($cards_list);

                                // JSON形式で返却
                                $json_cards_list = json_encode($cards_list);
                                $this->users[$id]->send($json_cards_list);
                            }                   
                        }
                    }
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

                                // 返却するリスト生成
                                $query = mysqli_query($link, "SELECT * FROM card WHERE id = ".$data->message);
                                $card_list = [];
                                while($row = mysqli_fetch_assoc($query)){
                                    $card_list[] = $row;
                                }
                                // var_dump($card_list);

                                // JSON形式で返却
                                $json_card_list = json_encode($card_list);
                                $this->users[$id]->send($json_card_list);
                            }                   
                        }
                    }
            }
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