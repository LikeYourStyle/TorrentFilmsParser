<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 22.11.2018
 * Time: 17:33
 */
define('BOT_TOKEN', '701374217:AAHBjH23Ljb3-3QsIRpF_qb6ZhU2r62EyfM');
define('API_URL', 'https://api.telegram.org/bot' . BOT_TOKEN . '/');
define('GROUP_INVITE', 'https://t.me/joinchat/AAAAAE6T7s_0YO8UFs3Klg');
define('GROUP_ID', -1001318317775);

function notifyBot($film_name, $film_link, $film_rate){
    $bot = initBot();
    if (!empty($film_rate)) {
        $media = new \TelegramBot\Api\Types\InputMedia\ArrayOfInputMedia();
        $media->addItem(new \TelegramBot\Api\Types\InputMedia\InputMediaPhoto($film_rate, "Новый фильм: ".$film_name."\nСсылка:\n".$film_link));
        $bot->sendMediaGroup(GROUP_ID, $media);
    } else {
        $message = "Новый фильм: \n".$film_name."\nСсылка:\n".$film_link;
        $bot->sendMessage(GROUP_ID, $message);
    }
    $bot->run();
}

function d_notifyBot($error){
    $bot = initBot();
    $bot->sendMessage(355353616, $error); //my telegram id
    $bot->run();
}

function initBot(){
    include_once 'bot_lib/vendor/autoload.php';
    return $bot = new \TelegramBot\Api\Client(BOT_TOKEN);
}

function teleToLog($log) {
    $myFile = 'log.txt';
    $fh = fopen($myFile, 'a') or die('can\'t open file');
    if ((is_array($log)) || (is_object($log))) {
        $updateArray = print_r($log, TRUE);
        fwrite($fh, $updateArray."\n");
    } else {
        fwrite($fh, $log . "\n");
    }
    fclose($fh);
}

function requestToTelegram($data, $type = 'sendMessage') {
    if( $curl = curl_init() ) {
        curl_setopt($curl, CURLOPT_URL, API_URL . $type);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl, CURLOPT_POST, TRUE);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        $content = curl_exec($curl);
        curl_close($curl);
        return $content;
    }
}

function sendMessage($message, $chat_id) {
    $data = array(
        'text' => $message,
        'chat_id' => $chat_id,
    );
    requestToTelegram($data);
}

function inviteToGroup($text, $group_invite_link, $chat_id){
    $inviteResponses = array();
    $inviteResponses [] = 'добавь';
    $inviteResponses [] = 'чат';
    $inviteResponses [] = 'груп';

    $need_responce = false;

    foreach ($inviteResponses as $response){
        if (stristr(mb_strtolower($text),$response)){
            $need_responce = true;
            break;
        }
    }

    if ($need_responce) {
        //случайная фраза привет от бота
        $answer_text = "Вот ссылка на группу присоединяйся!\n".$group_invite_link;
        $answer = array(
            'text' => $answer_text,
            'chat_id' => $chat_id,
        );
        requestToTelegram($answer);
    } else{
        $bot_resp = "Такой команды я не знаю. Возможно скоро создатель её добавит.";

        $answer = array(
            'text' => $bot_resp,
            'chat_id' => $chat_id,
        );
        requestToTelegram($answer);
    }
}

function showKeyboard($chat_id){
    $bot_answer = 'Попроси меня ссылку на группу! Уведомления о новых фильмах будут приходить автоматически!';

    $replyMarkup = array(
        'keyboard' => array(
            array("Получить ссылку на группу")
        ),
        'resize_keyboard' => true
    );
    $encodedMarkup = json_encode($replyMarkup);

    $content = array(
        'chat_id' => $chat_id,
        'text' => $bot_answer,
        'reply_markup' => $encodedMarkup
    );
    requestToTelegram($content);
}

