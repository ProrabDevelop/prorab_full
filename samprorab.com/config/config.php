<?

\core\engine\debugger::init(false);
const minify_html = false;
const sms_debug = true;

$config_file = [
    'db_host' => 'localhost',
    'db_name' => 'prorab',
    'db_user' => 'prorab',
    'db_pass' => 'qjBGElEI3nSgrMbf',
];

const RATING_UPDATE_TIME_MIN = 4;
const RATING_UPDATE_TIME_MAX = 6;


const WORK_WORD_COUNT = 50;
const REVIEW_WORD_COUNT = 30;

const JWT_PRIVATE = 'n0v3fjds4h31fese45dnf8gc1h9rw3eunfc76wr18cemj45gif7wg';

ORM::configure('mysql:host='.$config_file['db_host'].';dbname='.$config_file['db_name']);
ORM::configure('username', $config_file['db_user']);
ORM::configure('password', $config_file['db_pass']);
ORM::configure('driver_options', array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4'));

//список внутренних IP микросервисов, для исключения перезаписи ip пользователя $user->update_last_visit()
//используется в случае системной авторизации пользователем из внутреннего микросервиса
const micro_services_ip = [
    '185.146.157.224', //сервер мессенджера 185.146.157.224
];
















