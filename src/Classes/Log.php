<?php
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\NativeMailerHandler;

require '../../vendor/autoload.php';

class Log {
    static $log = null;

    static function setup($logname = "cueva"){
        self::$log = new Logger($logname);

        $data_format = "Y-m-d H:i:s";
        $output = "[%datetime%][%level_name%]> %message% : %context% : %extra%\n";
        $formatter = new LineFormatter($output, $data_format);
        $formatter->includeStacktraces(true);
        $streamHandler = new StreamHandler('php://stdout', Logger::DEBUG);
        $streamHandler->setFormatter($formatter);
        self::$log->pushHandler($streamHandler);
        $rotatingFileHandler = new RotatingFileHandler(__DIR__."")
    }
}