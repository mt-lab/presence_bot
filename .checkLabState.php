<?php

    $TIMEOUT = 3 * 60; // 3 minutes

    $TIMESTAMP_FILEPATH = ".botTimestamp";
    $LAB_STATE_FILEPATH = ".labState";

    $LAB_OPEN = "LAB_OPEN";
    $LAB_CLOSED = "LAB_CLOSED";

    $BOT_TOKEN = < YOUR BOT TOKEN >;
    $CHAT_ID = < DESIRED CHAT TO RECIEVE NOTIFICATIONS >;
    $REQUEST_BASE = "https://api.telegram.org/bot" . $BOT_TOKEN;

    $OPEN_MESSAGE = "\xF0\x9F\x9F\xA2MT.lab is open!";
    $CLOSED_MESSAGE = "\xF0\x9F\x94\xB4MT.lab is closed!";

    function getFileTimestamp(){
        global $TIMESTAMP_FILEPATH;

        if (file_exists($TIMESTAMP_FILEPATH)){
            $timestampFile = fopen($TIMESTAMP_FILEPATH, "r") or die("Unable to open file!");
            $timestamp = (int) fgets($timestampFile);
            fclose($timestampFile);
            return $timestamp;
        } else {
            setFileTimestamp(0);
        }
    }

    function setFileTimestamp($time){
        global $TIMESTAMP_FILEPATH;

        $timestampFile = fopen($TIMESTAMP_FILEPATH, "w") or die("Unable to open file!");
        fwrite($timestampFile, $time);
        fclose($timestampFile);
    }

    function getLabState(){
        global $LAB_STATE_FILEPATH, $LAB_CLOSED;

        if (file_exists($LAB_STATE_FILEPATH)){
            $labStateFile = fopen($LAB_STATE_FILEPATH, "r") or die("Unable to open file!");
            $labState = fgets($labStateFile);
            fclose($labStateFile);
            return $labState;
        } else {
            setLabState($LAB_CLOSED);
        }
    }

    function setLabState($labState){
        global $LAB_STATE_FILEPATH;

        $labStateFile = fopen($LAB_STATE_FILEPATH, "w") or die("Unable to open file!");
        fwrite($labStateFile, $labState);
        fclose($labStateFile);
    }

    function sendMessage($text){
        global $REQUEST_BASE, $CHAT_ID;

        $request = $REQUEST_BASE . "/sendMessage";

        $bot = curl_init();

        curl_setopt($bot, CURLOPT_URL, $request);

        curl_setopt($bot, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($bot, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($bot, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($bot, CURLOPT_TIMEOUT, 60);
        curl_setopt($bot, CURLOPT_POST, 1);

        curl_setopt($bot, CURLOPT_POSTFIELDS, array('chat_id' => $CHAT_ID,
                                                    'text' => $text,
                                                    'disable_notification' => true));
        $response = curl_exec($bot);
        $response = json_decode($response, true);

        $messageID = $response['result']['message_id'];

        $request = $REQUEST_BASE . "/pinChatMessage";

        curl_setopt($bot, CURLOPT_URL, $request);
        curl_setopt($bot, CURLOPT_POSTFIELDS, array('chat_id' => $CHAT_ID,
                                                    'message_id' => $messageID,
                                                    'disable_notification' => true));
        $response = curl_exec($bot);

        curl_close($bot);
    }

    $currentTimestamp = time();
    $fileTimestamp = getFileTimestamp();
    $dT = abs($fileTimestamp - $currentTimestamp);

    $prevLabState = getLabState();

    if ($dT < $TIMEOUT){
        if ($prevLabState == $LAB_CLOSED){
            sendMessage($OPEN_MESSAGE);
            setLabState($LAB_OPEN);
        }
    } else {
        if ($prevLabState == $LAB_OPEN){
            sendMessage($CLOSED_MESSAGE);
            setLabState($LAB_CLOSED);
        }
    }

?>
