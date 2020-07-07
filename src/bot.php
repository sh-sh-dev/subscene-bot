<?php
include "config.php";
include "vendor/functions.php";
include "vendor/user.php";
include "vendor/subscene.php";
include "parser.php";

global $db, $subscene, $token, $isCallback, $type, $chatId, $name, $messageId, $userId, $text, $callbackId;

$user = new User($userId);
$core = new Subscene($subscene["email"], $subscene["password"]);

// Only private chats
if ($type !== "private")
    bot("sendMessage", [
        "chat_id" => $chatId,
        "text" => "🔴 امکان استفاده از این ربات در گروه ها وجود ندارد.",
        "reply_to_message_id" => $messageId
    ], true);

// Start or Help commands
if (in_array($text, ["/start", "/help"])) {
    action("typing");

    $user->create($name);

    bot("sendMessage", [
        "chat_id" => $userId,
        "text" => "سلام\nمن برات زیرنویس فیلما رو پیدا میکنم. الآن اسم یک فیلم (به انگلیسی) رو بفرست.\nمثلا John wick",
        "reply_to_message_id" => $messageId,
    ], true);
}

// Manage banned users
if (!$user->get(["active"]))
    bot("sendMessage", [
        "chat_id" => $chatId,
        "text" => "شما دسترسی انجام این کار را ندارید.",
        "reply_to_message_id" => $messageId
    ], true);

// Manage Callbacks
if ($isCallback) {
    $exp = explode(":", $text);
    $text = $exp[1];

    switch ((int)$exp[0]) {
        // Download
        case 1:
            $subtitles = $core->getSubtitles("https://subscene.com/subtitles/$text")["subtitles"];
            if (count($subtitles) === 0)
                bot("answerCallbackQuery",[
                    "callback_query_id" => $callbackId,
                    "text" => "هیچ زیرنویسی برای این فیلم پیدا نشد.",
                    "show_alert" => true
                ], true);
            else
                bot("answerCallbackQuery",[
                    "callback_query_id" => $callbackId,
                    "text" => "در حال پردازش..."
                ]);

            $subtitles = $subtitles[0];

            $subtitle = $core->getSubtitleInfo($subtitles["url"]);
            $subtitle["id"] = parseSubtitleId($subtitles["url"]);

            bot("sendDocument", [
                "chat_id" => $chatId,
                "document" => $subtitle["url"],
                "caption" => "*$subtitle[title]*\n\n$subtitle[info]",
                "parse_mode" => "Markdown",
                "reply_to_message_id" => $messageId,
                "reply_markup" => json_encode([
                    "inline_keyboard" => [
                        [
                            [
                                "text" => "پیش‌نمایش",
                                "callback_data" => base64_encode("2:$subtitle[id]")
                            ]
                        ],
                        [
                            [
                                "text" => "هماهنگ نبود؟ کلیک کنید.",
                                "callback_data" => base64_encode("3:$text")
                            ]
                        ]
                    ]
                ])
            ]);
            break;
        // Preview
        case 2:
            bot("answerCallbackQuery",[
                "callback_query_id" => $callbackId,
                "text" => "در حال پردازش..."
            ]);

            $text = explode("/", $text);
            $subtitle = $core->getSubtitleInfo("https://subscene.com/subtitles/$text[0]/farsi_persian/$text[1]");

            // Convert html to Markdown
            $preview = str_replace(["<br>", "</br>"], "\n", $subtitle["preview"]);
            $preview = strip_tags($preview);
            $preview = str_replace("--&gt;", ">", $preview);

            bot("sendMessage", [
                "chat_id" => $chatId,
                "text" => $preview,
                "reply_to_message_id" => $messageId
            ]);

            break;
        // Refresh Subtitle
        case 3:
            bot("answerCallbackQuery",[
                "callback_query_id" => $callbackId,
                "text" => "در حال پردازش..."
            ]);

            $subtitles = $core->getSubtitles("https://subscene.com/subtitles/$text")["subtitles"][1];

            $subtitle = $core->getSubtitleInfo($subtitles["url"]);
            $subtitle["id"] = parseSubtitleId($subtitles["url"]);

            bot("sendDocument", [
                "chat_id" => $chatId,
                "document" => $subtitle["url"],
                "caption" => "*♻️ $subtitle[title]*\n\n$subtitle[info]",
                "parse_mode" => "Markdown",
                "reply_to_message_id" => $messageId,
                "reply_markup" => json_encode([
                    "inline_keyboard" => [
                        [
                            [
                                "text" => "پیش‌نمایش",
                                "callback_data" => base64_encode("2:$subtitle[id]")
                            ]
                        ]
                    ]
                ])
            ]);
            break;
    }
}

// Search Subtitle
else {
    action("typing");

    $loader = bot("sendMessage", [
        "chat_id" => $chatId,
        "text" => "در حال جستجو...",
        "reply_to_message_id" => $messageId,
    ], false, true)->result->message_id;

    $movies = $core->search($text);
    $accepted = [];
    $result = [];

    $percent = 50;
    loader($percent);

    $i = 1;
    foreach ($movies as $movie) {
        $title = $movie["title"];
        $url = str_replace(["http://", "https://", "subscene.com/subtitles/"], "", $movie["url"]);

        if ($i > 5)
            break;
        if (similar_text($text, $title) < 7 || strlen(base64_encode("1:$url")) > 64 || in_array($url, $accepted))
            continue;

        $accepted[] = $url;
        $result[] = [
            [
                "text" => $title,
                "callback_data" => base64_encode("1:$url")
            ]
        ];

        $percent += 5;
        loader($percent);

        $i++;
    }

    loader(100);

    if (count($result) === 0) bot("sendMessage", [
        "chat_id" => $chatId,
        "text" => "هیچ فیلمی با این نام پیدا نشد.",
        "reply_to_message_id" => $messageId
    ], true);

    bot("sendMessage", [
        "chat_id" => $chatId,
        "text" => "حالا یکی از فیلم‌های زیر رو انتخاب کن:",
        "reply_to_message_id" => $messageId,
        "reply_markup" => json_encode([
            "inline_keyboard" => $result
        ])
    ]);
}
