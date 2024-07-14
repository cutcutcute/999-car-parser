
<?php
require 'vendor/autoload.php';

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\BrowserConsoleHandler; 

// Создаем объект логгера
$logger = new Logger('myLogger');

// Добавляем обработчики (handlers) для логгера - например, запись в файл
$logger->pushHandler(new StreamHandler('app.log', Logger::DEBUG));
$logger->pushHandler(new StreamHandler('php://stdout', Logger::DEBUG));


