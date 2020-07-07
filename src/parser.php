<?php

// Parse incoming updates

$update = json_decode(file_get_contents("php://input"));

$isCallback = isset($update->callback_query);
$update = $isCallback ? $update->callback_query : $update->message;

$type = $isCallback ? $update->message->chat->type : $update->chat->type;
$chatId = $isCallback ? $update->message->chat->id : $update->chat->id;
$name = trim($update->from->first_name . " " . @$update->from->last_name);
$messageId = $isCallback ? $update->message->message_id : $update->message_id;
$userId = $update->from->id;
$text = $isCallback ? $db->real_escape_string(base64_decode($update->data)) : $db->real_escape_string($update->text);
$callbackId = $isCallback ? $update->id : null;
