<?php
require 'vendor/autoload.php';
require 'Api.php';
require 'Sender.php';

(new josegonzalez\Dotenv\Loader(__DIR__ . '/.env'))->parse()->define();

$api    = new Api();
$sender = new Sender();

$config = $api->getConfig();
$db     = $api->getDb();
$token  = $config['token'];
$msgCollection = '';

try {
    foreach ($db as $item) {
        $data = [
            "token"      => $token,
            "region"     => $item['region'],
            "firstname"  => $item['firstname'],
            "lastname"   => $item['lastname'],
            'secondname' => isset($item['secondname']) ? $item['secondname'] : '',
            'birthdate'  => isset($item['birthdate']) ? $item['birthdate'] : ''
        ];
        $data ['task'] = $api->get($data, 'task');

        do {
            $status = $api->get($data, 'status');
            sleep(5);
        } while ($status > 0);

        $result = $api->get($data, 'result')[0]['result'];
        $data ['email'] =isset($item['email']) ? $item['email'] : '';//TODO
        if (is_array($result) && count($result) > 0) {
            $msg = 'Найдено исполнительное производство на '
                . $item['firstname'] . ' '
                . $item['lastname'] . ' '
                . date(
                    'Y-m-d H:i:s'
                ) . PHP_EOL;
            $msgCollection .= $msg;

            file_put_contents(FILE, $msg, FILE_APPEND);
            echo $config['warning_color'] . $msg . $config['close_color'];
        }
        else {
            $msg = 'Не найдено для '
                . $item['firstname'] . ' '
                . $item['lastname'] . ' '
                . date(
                    'Y-m-d H:i:s'
                ) . PHP_EOL;
            file_put_contents(FILE, $msg, FILE_APPEND);
            echo $msg;
        }
        sleep(5);
    }
    if($msgCollection){
        $sender->send('Уведомление ФССП о проверке', $msgCollection);
    }

    echo 'Result completed without errors' . PHP_EOL;
} catch (\Exception $e) {
    $msg = 'Ошибка скрипта API ФССП: '
        . $e->getMessage() . ' '
        . date('Y-m-d H:i:s')
        . PHP_EOL;
    $sender->send('Проблемы с API ФССП: ', $e->getMessage());
    file_put_contents(FILE, $msg, FILE_APPEND);

    echo $config['warning_color'] . 'Result completed with errors: ' . $e->getMessage() . $config['close_color'];
}



