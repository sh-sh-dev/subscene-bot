<?php

/**
 * Make request to Telegram API
 *
 * @param string $method
 * @param null|array $data
 * @param bool $die
 * @param bool $return
 *
 * @return bool|object|string
 */
function bot($method, $data = null, $die = false, $return = false) {
    global $token;

    $url = "https://api.telegram.org/bot$token/$method";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, $return);
    if (!empty($data))
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));

    $result = curl_exec($ch);

    if ($die)
        die();

    if (curl_error($ch))
        return curl_error($ch);
    else
        return $return ? json_decode($result) : true;
}

/**
 * Send a Chat Action to Telegram API
 *
 * @param $action
 */
function action($action) {
    global $chatId;

    bot("sendChatAction",[
        "chat_id" => $chatId,
        "action" => $action
    ]);
}

/**
 * Send a loader message to Telegram API
 *
 * @param null|string $percent
 */
function loader($percent = null) {
    global $chatId, $loader;

    $method = $percent === 100 ? "deleteMessage" : "editMessageText";

    bot($method, [
        "chat_id" => $chatId,
        "text" => "در حال جستجو... `[$percent%]`",
        "message_id" => $loader,
        "parse_mode" => "Markdown"
    ]);
}

/**
 * Fetch subtitle Id from Url
 *
 * @param $url
 *
 * @return string
 */
function parseSubtitleId($url) {
    $search = ["http://", "https://", "subscene.com/subtitles/", "farsi_persian/"];

    return str_replace($search, "", $url);
}
