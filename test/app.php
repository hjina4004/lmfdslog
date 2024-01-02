<?php

require __DIR__ . '/../vendor/autoload.php';

use Lmfriends\Lmfdslog\PDOHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

date_default_timezone_set('Asia/Seoul');

$dbHost = "localhost";  // 호스트 주소(localhost, 127.0.0.1)
$dbName = "homestead";  // 데이타 베이스(DataBase) 이름
$dbUser = "homestead";  // DB 아이디
$dbPass = "secret";     // DB 패스워드
$dbChar = "utf8";       // 문자 인코딩
$pdo = new PDO("mysql:host={$dbHost};dbname={$dbName};charset={$dbChar}", $dbUser, $dbPass);

$logger = new Logger('test-main');
$logger->pushHandler(new StreamHandler(__DIR__ . '/logs/app.log'));
$logger->pushHandler(new PDOHandler($pdo, 'monologs', ['extra']));

$logger->info('Adding a new user', ['extra' => ['username' => 'jina']]);
$logger->warning('경고 메시지입니다.');
$logger->error('에러 메시지입니다.');

echo "This is the testing of PDOHandler.\n";
