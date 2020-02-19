<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 22.11.2018
 * Time: 17:33
 */

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

