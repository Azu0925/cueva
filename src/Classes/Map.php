<?php
    namespace Cueva\Map;
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
                case "map":
                    if (isset($this->subscriptions[$conn->resourceId])) {
                        $target = $this->subscriptions[$conn->resourceId];
                        foreach ($this->subscriptions as $id=>$channel) {
                            if ($channel == $target) {
                                // envファイルの読み込み ※必要ないかも
                                // require dirname(__FILE__).'/../vendor/autoload.php';
                                // $dotenv = Dotenv\Dotenv::createImmutable(__DIR__. '/..');
                                // $dotenv->load();

                                // DB接続
                                ORM::configure('mysql:host='.Env::get("HOST").';port='.Env::get("PORT").';dbname='.Env::get("DB_NAME"));
                                ORM::configure('username', Env::get('USER_ID'));
                                ORM::configure('password', Env::get("PASSWORD"));

                                $table = 'map'; //テーブルの名前
                                $query = ORM::for_table($table)->where(array('team_id' => $data->message))->find_many(); // 該当データの取り出し

                                // 連想配列を二次元配列に格納
                                $map_list = []; 
                                foreach(ORM::for_table($table)->find_result_set() as $query) {
                                    $map_list[] = ($query->as_array('map_id', 'map_name', 'map_description', 'map_create', 'map_host', 'team_id', 'parameter_top', 'parameter_under', 'parameter_left', 'parameter_right'));
                                }
                                // JSON形式で返却
                                $json_map_list = json_encode($map_list);
                                $this->users[$id]->send($json_map_list);
                            }                       
                        }
                    }
                case "card":
                    if (isset($this->subscriptions[$conn->resourceId])) {
                        $target = $this->subscriptions[$conn->resourceId];
                        foreach ($this->subscriptions as $id=>$channel) {
                            if ($channel == $target) {
                                // envファイルの読み込み ※必要ないかも
                                // require dirname(__FILE__).'/../vendor/autoload.php';
                                // $dotenv = Dotenv\Dotenv::createImmutable(__DIR__. '/..');
                                // $dotenv->load();

                                // DB接続
                                ORM::configure('mysql:host='.Env::get("HOST").';port='.Env::get("PORT").';dbname='.Env::get("DB_NAME"));
                                ORM::configure('username', Env::get('USER_ID'));
                                ORM::configure('password', Env::get("PASSWORD"));

                                $table = 'card'; //テーブルの名前
                                $query = ORM::for_table($table)->where(array('map_id' => $data->message))->find_many(); // 該当データの取り出し

                                // 連想配列を二次元配列に格納
                                $card_list = []; 
                                foreach(ORM::for_table($table)->find_result_set() as $query) {
                                    $card_list[] = ($query->as_array('card_id', 'card_name', 'card_description', 'update_date', 'update_user', 'card_x', 'card_y', 'card_width', 'card_height', 'map_id'));
                                }
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