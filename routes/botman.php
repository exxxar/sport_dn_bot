<?php

use App\Drivers\TelegramInlineQueryDriver;
use App\Http\Controllers\BotManController;
use App\Prize;
use App\Product;
use App\User;
use BotMan\BotMan\BotMan;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Laravel\Facades\Telegram;

$botman = resolve('botman');

function createUser($bot)
{
    $telegramUser = $bot->getUser();
    $id = $telegramUser->getId();
    $username = $telegramUser->getUsername();
    $lastName = $telegramUser->getLastName();
    $firstName = $telegramUser->getFirstName();

    $user = User::where("telegram_chat_id", $id)->first();
    if ($user == null)
        $user = \App\User::create([
            'name' => $username ?? "$id",
            'email' => "$id@t.me",
            'password' => bcrypt($id),
            'fio_from_telegram' => "$firstName $lastName",
            'telegram_chat_id' => $id,
            'is_admin' => false,
            'is_vip' => false,
            'cashback_money' => false,
            'phone' => '',
            'birthday' => '',
        ]);
    return $user;
}

/*function basketMenu($bot, $message)
{
    $telegramUser = $bot->getUser();
    $id = $telegramUser->getId();

    $basket = json_decode($bot->userStorage()->get("basket")) ?? [];

    $count = count($basket) ?? null;

    foreach ($basket as $product) {
        $count += $product->price;
    }


    $keyboard = [
        ["Оформить заказ " . ($count == null ? "(0₽)" : "(" . $count . "₽)")],
        ["Главное меню"],
    ];
    $bot->sendRequest("sendMessage",
        [
            "chat_id" => "$id",
            "text" => $message,
            "parse_mode" => "Markdown",
            'reply_markup' => json_encode([
                'keyboard' => $keyboard,
                'one_time_keyboard' => false,
                'resize_keyboard' => true
            ])
        ]);
}*/

function mainMenu($bot, $message)
{
    $telegramUser = $bot->getUser();
    $id = $telegramUser->getId();

    $user = User::where("telegram_chat_id", $id)->first();

    if (is_null($user))
        $user = createUser($bot);


    $keyboard = [

    ];

    array_push($keyboard, ["\xE2\x9A\xA1Акции и мероприятия", "\xE2\x98\x95Услуги"]);
    if (!$user->is_vip)
        array_push($keyboard, ["\xE2\x9A\xA1Анкета VIP-пользователя"]);
    else
        array_push($keyboard, ["\xE2\x9A\xA1Special CashBack system"]);

    /*    array_push($keyboard,["\xF0\x9F\x8E\xB0Розыгрыш"]);*/
    array_push($keyboard, ["\xF0\x9F\x92\xADО Нас"]);

    if ($user->is_admin)
        array_push($keyboard, ["\xE2\x9A\xA0Админ. статистика"]);

    $bot->sendRequest("sendMessage",
        [
            "chat_id" => "$id",
            "text" => $message,
            "parse_mode" => "Markdown",
            'reply_markup' => json_encode([
                'keyboard' => $keyboard,
                'one_time_keyboard' => false,
                'resize_keyboard' => true
            ])
        ]);
}

/*function filterMenu($bot, $message)
{
    $telegramUser = $bot->getUser();
    $id = $telegramUser->getId();

    $upper_layer = $bot->userStorage()->get("upper") ?? null;
    $inner_filling = $bot->userStorage()->get("inner") ?? null;
    $form = $bot->userStorage()->get("form") ?? null;
    $count = $bot->userStorage()->get("count") ?? null;

    $keyboard = [
        ["Заказать свой ролл"],
        ["Покрытие" . ($upper_layer == null ? "\xE2\x9D\x8E" : "\xE2\x9C\x85"), "Начинка" . ($inner_filling == null ? "\xE2\x9D\x8E" : "(" . count(json_decode($inner_filling, true)) . ")\xE2\x9C\x85")],
        ["Форма ролла" . ($form == null ? "\xE2\x9D\x8E" : "\xE2\x9C\x85"), "Количество" . ($count == null ? "\xE2\x9D\x8E" : "\xE2\x9C\x85")],
        ["Сбросить фильтр"],
        ["Главное меню"],
    ];
    $bot->sendRequest("sendMessage",
        [
            "chat_id" => "$id",
            "text" => $message,
            "parse_mode" => "Markdown",
            'reply_markup' => json_encode([
                'keyboard' => $keyboard,
                'one_time_keyboard' => false,
                'resize_keyboard' => true
            ])
        ]);
}*/
$botman->hears('.*Админ. статистика', function ($bot) {
    $telegramUser = $bot->getUser();
    $id = $telegramUser->getId();

    $user = User::where("telegram_chat_id", $id)->first();

    if (is_null($user))
        return;

    if (!$user->is_admin)
        return;

    $users_in_bd = User::all()->count();
    $vip_in_bd = User::where("is_vip", true)->get()->count();


    $vip_in_bd_day = User::whereDate('updated_at', Carbon::today())
        ->where("is_vip", true)
        ->orderBy("id", "DESC")
        ->get()
        ->count();

    $users_in_bd_day = User::whereDate('created_at', Carbon::today())
        ->orderBy("id", "DESC")
        ->get()
        ->count();

    $message = sprintf("Всего пользователей в бд: %s\nВсего VIP:%s\nПользователей за день:%s\nVIP за день:%s",
        $users_in_bd,
        $vip_in_bd,
        $users_in_bd_day,
        $vip_in_bd_day
    );

    $is_working = $user->is_working;

    $keybord = [
        [
            ['text' => !$is_working ? "Я работаю!" : "Я не работаю!", 'callback_data' => "/working " . ($is_working ? "on" : "off")]
        ]
        /* [
             ['text' => "Статистика в PDF", 'callback_data' => "/get_statistic_pdf"]
         ],*/

    ];
    $bot->sendRequest("sendMessage",
        [
            "chat_id" => "$id",
            "text" => $message,
            "parse_mode" => "Markdown",
            'reply_markup' => json_encode([
                'inline_keyboard' =>
                    $keybord
            ])
        ]);

})->stopsConversation();


$botman->hears(".*Анкета VIP-пользователя|/do_vip", BotManController::class . "@vipConversation");
$botman->hears('.*Розыгрыш', function ($bot) {
    $telegramUser = $bot->getUser();
    $id = $telegramUser->getId();

    $keybord = [
        [
            ['text' => "Условия розыгрыша и призы", 'url' => "https://telegra.ph/Usloviya-rozygrysha-01-01"]
        ],
        [
            ['text' => "Ввести код и начать", 'callback_data' => "/lottery"]
        ]
    ];
    $bot->sendRequest("sendMessage",
        [
            "chat_id" => "$id",
            "text" => "Розыгрыш призов",
            "parse_mode" => "Markdown",
            'reply_markup' => json_encode([
                'inline_keyboard' =>
                    $keybord
            ])
        ]);
});

$botman->hears('/working (on|off)', function ($bot, $working) {
    $telegramUser = $bot->getUser();
    $id = $telegramUser->getId();

    $user = User::where("telegram_chat_id", $id)->first();

    if (is_null($user))
        return;

    if (!$user->is_admin)
        return;

    $user->is_working = $working == "on" ? false : true;
    $user->save();

    $bot->reply($user->is_working ? "Теперь вас МОГУТ выбирать для работы с CashBack" : "Теперь вас НЕ могут выбирать для работы с CashBack");
});

$botman->hears('.*О нас', function ($bot) {
    $bot->reply("https://telegra.ph/Uslugi--Stoimost-06-29");
});

$botman->hears('.*Акции и мероприятия', function ($bot) {
    $bot->reply("https://telegra.ph/Body-Master--Doneck--Makeevka-06-28-2");
});


$botman->hears("/start ([0-9a-zA-Z=]+)", BotManController::class . '@startDataConversation');

$botman->hears('/start', function ($bot) {
    createUser($bot);
    mainMenu($bot, 'BODY MASTER - территория красивого и здорового тела, хорошего настроения и заряда энергией⚡');
})->stopsConversation();

$botman->hears('.*Услуги', function ($bot) {
    $telegramUser = $bot->getUser();
    $id = $telegramUser->getId();

    /* $media = [
         ["type" => "photo", "media" => "https://sun9-21.userapi.com/c857616/v857616755/2355ee/jY6DlCvQnk8.jpg"],
         ["type" => "photo", "media" => "https://sun9-49.userapi.com/c857616/v857616755/2355f8/tBvlw3739EY.jpg"],
         ["type" => "photo", "media" => "https://sun9-74.userapi.com/c857616/v857616755/235602/6CpGc4O7hL4.jpg"],
         ["type" => "photo", "media" => "https://sun9-1.userapi.com/c857616/v857616755/23560c/4qyrvMTZc18.jpg"],
         ["type" => "photo", "media" => "https://sun9-8.userapi.com/c857616/v857616755/235616/pJIJtVpT9oU.jpg"],
         ["type" => "photo", "media" => "https://sun9-34.userapi.com/c857616/v857616755/235620/q6UIEjYFX48.jpg"],
         ["type" => "photo", "media" => "https://sun9-27.userapi.com/c857616/v857616755/23562a/dwTBMS49hNg.jpg"],
         ["type" => "photo", "media" => "https://sun9-58.userapi.com/c857616/v857616755/235634/imO2Y_0MTm4.jpg"],
         ["type" => "photo", "media" => "https://sun9-47.userapi.com/c857616/v857616755/23563e/RF81WmcIhCk.jpg"],
         ["type" => "photo", "media" => "https://sun9-71.userapi.com/c857616/v857616755/235648/Xss62b3AExk.jpg"],
         ["type" => "photo", "media" => "https://sun9-41.userapi.com/c857236/v857236551/1f1eae/OST0kPEnB9A.jpg"],
         ["type" => "photo", "media" => "https://sun9-59.userapi.com/c857236/v857236551/1f1eb8/OqXo8ukMcAQ.jpg"],
     ];

     $bot->sendRequest("sendMediaGroup",
         [
             "chat_id" => "$id",
             "parse_mode" => "Markdown",
             "media" => json_encode($media),

         ]);*/

    $bot->sendRequest("sendMessage", [
        "chat_id" => "$id",
        "parse_mode" => "HTML",
        "text" => "Услуги | Стоимость

~ 8 тренировок | 1000₽
~ 12 тренировок | 1200 ₽
~ безлимит (месяц) | 1800₽

•• ДНЕВНОЙ (11:00-15:00)

~ 8 тр | 700₽
~ 12 тр | 850₽
~ безлимит | 1400₽
~ гостевой визит | 200₽

~ 3 мес. | 4900₽ 1 мес. = 1633₽
~ 6 мес. | 8600₽ 1 мес. = 1434₽
~ Годовой (+2 в 🎁 = 14 мес.) | 14000₽ (1 мес. = 1000₽)
~ семейный* | -15% от стоимости абонемента

1 ПТ* = 500₽ (🔝 тренер)
*персональная тренировка с тренером

~ 10 ПТ | 4000₽ / 3000₽
~ СПЛИТ (2 чел., 10 ПТ) | 6800₽ / 5100₽ (1чел.=3400₽ / 2550₽)**

~ ГРУПА ПТ 3-5 чел = 3700₽

~ CROSS FIT
 • 8 тр = 1200₽
 • 12 тр = 1600₽
✔️ 1 тр = 300₽ (присоединиться к группе, гостевой визит)
       "
    ]);

});
$botman->hears('.*Special CashBack system', function ($bot) {
    $telegramUser = $bot->getUser();
    $id = $telegramUser->getId();

    $user = User::where("telegram_chat_id", $id)->first();

    if (is_null($user))
        $user = createUser($bot);

    $cashback = $user->cashback_money ?? 0;

    $is_vip = $user->is_vip ?? false;


    if (!$is_vip) {
        $keyboard = [
            [
                ['text' => "\xF0\x9F\x8D\xB8Оформить VIP-статус", 'callback_data' => "/do_vip"],
            ],
        ];
        $bot->sendRequest("sendMessage",
            [
                "chat_id" => "$id",
                "parse_mode" => "markdown",
                "text" => "У вас нет VIP-статуса, но вы можете его оформить!",
                'reply_markup' => json_encode([
                    'inline_keyboard' =>
                        $keyboard
                ])
            ]);

        return;
    }

    $message = sprintf("У вас *%s* руб.!\n_Для начисления CashBack при оплате за абонимент дайте отсканировать данный QR-код администратору_ *Body Master!*", $cashback);
    $keyboard = [
        [
            ['text' => "Мой бюджет", 'callback_data' => "/my_money"],

        ],

    ];

    $work_admin_count = User::where("is_admin", true)
            ->where("is_working", true)
            ->get()
            ->count() ?? 0;

    if ($work_admin_count > 0) {
        array_push($keyboard, [
            ['text' => "Запрос на CashBack", 'switch_inline_query_current_chat' => ""],
        ]);
    }


    $tmp_id = (string)$id;
    while (strlen($tmp_id) < 10)
        $tmp_id = "0" . $tmp_id;

    $code = base64_encode("001" . $tmp_id);

    $qr_url = env("QR_URL") . "https://t.me/" . env("APP_BOT_NAME") . "?start=$code";

    $keyboard2 = [
        [
            ['text' => "Воспользоваться системой CashBack", 'url' => "https://t.me/" . env("APP_BOT_NAME") . "?start=$code"],
        ],
        [
            ['text' => "Подробности в нашем канале", 'url' => "https://body-master.ru"],
        ],
    ];

    $bot->sendRequest("sendPhoto",
        [
            "chat_id" => "$id",
            "photo" => "https://sun9-18.userapi.com/impg/i-pNPcWSshCsok7CC5UTo6p8x1UcshDEsrFqjw/wvrQz72AC-o.jpg?size=1160x500&quality=96&sign=058649ec4aa3d60a40141ea102d6d69f&type=album",
            "caption" => "Теперь ты можешь получать 10% CashBack от всех твоих покупок и 5% от покукпок друзей! Для этого подключи друзей к данной системе!\n_Дай отсканировать QR-код друзьям или делись ссылкой с друзьями и получай больше CashBack с каждой их покупки!_",
            "parse_mode" => "Markdown",
            'reply_markup' => json_encode([
                'inline_keyboard' =>
                    $keyboard2
            ])
        ]);

    $bot->sendRequest("sendPhoto",
        [
            "chat_id" => "$id",
            "caption" => "$message",
            "parse_mode" => "Markdown",
            "photo" => $qr_url,
            'reply_markup' => json_encode([
                'inline_keyboard' =>
                    $keyboard
            ])
        ]);

});
$botman->hears('/lottery', BotManController::class . '@lotteryConversation');
$botman->hears('/check_lottery_slot ([0-9]+)', function ($bot, $slotId) {


    $telegramUser = $bot->getUser();
    $id = $telegramUser->getId();

    $prize = Prize::find($slotId);

    $message = "*" . $prize->title . "*\n"
        . "_" . $prize->description . "_\n";


    $bot->sendRequest("sendPhoto",
        [
            "chat_id" => "$id",
            "photo" => $prize->image_url,
            "caption" => $message,
            "parse_mode" => "Markdown",
        ]);

    $user = User::where("telegram_chat_id", $id)->first();

    try {

        Telegram::sendMessage([
            'chat_id' => env("CHANNEL_ID"),
            'parse_mode' => 'Markdown',
            'text' => sprintf(($prize->type === 0 ? "Заявка на получение приза" : "*Пользователь получил виртуальный приз*") . "\nНомер телефона:_%s_\nПриз: [#%s] \"%s\"",
                $user->phone,
                $prize[0]->id,
                $prize[0]->title),
            'disable_notification' => 'false'
        ]);
    } catch (\Exception $e) {
        Log::info("Ошибка отправки заказа в канал!");
    }


});
$botman->hears('/my_money', function ($bot) {
    $telegramUser = $bot->getUser();
    $id = $telegramUser->getId();

    $keyboard = [
        [
            ['text' => "Начисления", 'callback_data' => "/cashback_up"],
            ['text' => "Списания", 'callback_data' => "/cashback_down"],
        ],
    ];

    $bot->sendRequest("sendMessage",
        [
            "chat_id" => "$id",
            "text" => "*Управление вашими начислениями и расходами CashBack*",
            "parse_mode" => "Markdown",
            'reply_markup' => json_encode([
                'inline_keyboard' =>
                    $keyboard
            ])
        ]);
});
$botman->hears('/cashback_up', function ($bot) {
    $telegramUser = $bot->getUser();
    $id = $telegramUser->getId();

    $keyboard = [
        [
            ['text' => "Списания", 'callback_data' => "/cashback_down"],
        ],
    ];


    $user = User::where("telegram_chat_id", $id)->first();

    if (is_null($user))
        $user = createUser($bot);

    $cashback = \App\CashBackHistory::where("user_id", $user->id)
        ->where("type", 0)
        ->orderBy("id", "desc")
        ->take(20)
        ->skip(0)
        ->get();

    if (count($cashback) == 0)
        $message = "На текущий момент у вас нет начислений CashBack";
    else {
        $tmp = "";

        foreach ($cashback as $key => $value)
            $tmp .= sprintf("#%s %s начислено %s руб., чек: %s\n ", ($key + 1), $value->created_at, $value->amount, $value->bill_number);

        $message = sprintf("*Статистика 20 последних начислений Cashback*\n%s", $tmp);

    }


    $bot->sendRequest("sendMessage",
        [
            "chat_id" => "$id",
            "text" => $message,
            "parse_mode" => "Markdown",
            'reply_markup' => json_encode([
                'inline_keyboard' =>
                    $keyboard
            ])
        ]);
});

$botman->hears('/admins', function ($bot) {

    $telegramUser = $bot->getUser();
    $id = $telegramUser->getId();

    $users =
        User::where("is_admin", true)
            ->where("is_working", true)
            ->orderBy("id", "DESC")
            ->take(8)
            ->skip(0)
            ->get();

    if (empty($users)) {
        $bot->reply("К сожалению, на текущий момент нет доступных администраторов:(");
        return;
    }

    foreach ($users as $user) {

        $tmp_user_id = (string)$user->telegram_chat_id;
        while (strlen($tmp_user_id) < 10)
            $tmp_user_id = "0" . $tmp_user_id;

        $code = base64_encode("005" . $tmp_user_id);
        $url_link = "https://t.me/" . env("APP_BOT_NAME") . "?start=$code";

        $message = sprintf("Администратор #%s %s (%s)", $user->id, ($user->fio_from_telegram ?? $user->name), ($user->phone ?? 'Без телефона'));
        $keyboard = [
            [
                ['text' => "\xF0\x9F\x91\x89Запросить CashBack у администратора", "url" => "$url_link"],
            ],

        ];

        $bot->sendRequest("sendMessage",
            [
                "chat_id" => "$id",
                "text" => $message,
                "parse_mode" => "Markdown",
                'reply_markup' => json_encode([
                    'inline_keyboard' =>
                        $keyboard
                ])
            ]);

    }


});

$botman->hears('/cashback_down', function ($bot) {
    $telegramUser = $bot->getUser();
    $id = $telegramUser->getId();


    $user = User::where("telegram_chat_id", $id)->first();

    if (is_null($user))
        $user = createUser($bot);

    $cashback = \App\CashBackHistory::where("user_id", $user->id)
        ->where("type", 1)
        ->orderBy("id", "desc")
        ->take(20)
        ->skip(0)
        ->get();

    if (count($cashback) == 0)
        $message = "На текущий момент у вас нет списаний CashBack";
    else {
        $tmp = "";

        foreach ($cashback as $key => $value)
            $tmp .= sprintf("#%s %s списано %s руб. \n ", ($key + 1), $value->created_at, $value->amount);

        $message = sprintf("*Статистика 20 последних списаний Cashback*\n%s", $tmp);

    }

    $keyboard = [
        [
            ['text' => "Начисления", 'callback_data' => "/cashback_up"],
        ],
    ];

    $bot->sendRequest("sendMessage",
        [
            "chat_id" => "$id",
            "text" => $message,
            "parse_mode" => "Markdown",
            'reply_markup' => json_encode([
                'inline_keyboard' =>
                    $keyboard
            ])
        ]);
});

$botman->receivesImages(function ($bot, $images) {
    $telegramUser = $bot->getUser();
    $id = $telegramUser->getId();

    $user = User::where("telegram_chat_id", $id)->first();

    if (is_null($user))
        $user = createUser($bot);

    $is_vip = $user->is_vip ?? false;


    if (!$is_vip) {
        $keyboard = [
            [
                ['text' => "\xF0\x9F\x8D\xB8Оформить VIP-статус", 'callback_data' => "/do_vip"],
            ],
        ];
        $bot->sendRequest("sendMessage",
            [
                "chat_id" => "$id",
                "parse_mode" => "markdown",
                "text" => "У вас нет VIP-статуса, но вы можете его оформить!",
                'reply_markup' => json_encode([
                    'inline_keyboard' =>
                        $keyboard
                ])
            ]);

        return;
    }

    $tmp_id = (string)$id;
    while (strlen($tmp_id) < 10)
        $tmp_id = "0" . $tmp_id;

    $code_accept = base64_encode("002" . $tmp_id);
    $code_decline = base64_encode("003" . $tmp_id);

    $keyboard = [
        [
            ['text' => "Подтвердить", 'url' => "https://t.me/body_master_bot?start=$code_accept"],
            ['text' => "Отклонить", 'url' => "https://t.me/body_master_bot?start=$code_decline"],
        ],
    ];


    foreach ($images as $image) {

        $url = $image->getUrl();

        $message = sprintf("Пользователь #%s (%s) отправил скриншот. Проверьте!",
            ($user->fio_from_telegram ?? $user->name),
            $user->phone
        );

        Telegram::sendPhoto([
            'chat_id' => env("CHANNEL_ID"),
            'parse_mode' => 'Markdown',
            'caption' => $message,
            "photo" => \Telegram\Bot\FileUpload\InputFile::create($url),
            'disable_notification' => 'false',
            'reply_markup' => json_encode([
                'inline_keyboard' =>
                    $keyboard
            ])
        ]);

    }

    $bot->reply("Ваши скриншоты (" . count($images) . "шт) приняты в обработку!");
});


$botman->fallback(function ($bot) {
    $bot->loadDriver(TelegramInlineQueryDriver::DRIVER_NAME);


    $queryObject = json_decode($bot->getDriver()->getEvent());

    if ($queryObject) {

        $id = $queryObject->from->id;

        $query = $queryObject->query;

        $button_list = [];

        $users =
            User::where("is_admin", true)
                ->where("is_working", true)
                ->orderBy("id", "DESC")
                ->take(8)
                ->skip(0)
                ->get();

        if (!empty($users))
            foreach ($users as $user) {

                $tmp_user_id = (string)$user->telegram_chat_id;
                while (strlen($tmp_user_id) < 10)
                    $tmp_user_id = "0" . $tmp_user_id;

                $code = base64_encode("005" . $tmp_user_id);
                $url_link = "https://t.me/" . env("APP_BOT_NAME") . "?start=$code";

                $tmp_button = [
                    'type' => 'article',
                    'id' => uniqid(),
                    'title' => "Запрос на CashBack",
                    'input_message_content' => [
                        'message_text' => sprintf("Администратор #%s %s (%s)", $user->id, ($user->fio_from_telegram ?? $user->name), ($user->phone ?? 'Без телефона')),
                    ],
                    'reply_markup' => [
                        'inline_keyboard' => [
                            [
                                ['text' => "\xF0\x9F\x91\x89Запросить CashBack у администратора", "url" => "$url_link"],
                            ],

                        ]
                    ],
                    'thumb_url' => "https://sun2.48276.userapi.com/c855720/v855720573/191059/_kdC1Xs6xCA.jpg?ava=1",
                    'url' => env("APP_URL"),
                    'description' => sprintf("Администратор #%s %s (%s)", $user->id, ($user->fio_from_telegram ?? $user->name), ($user->phone ?? 'Без телефона')),
                    'hide_url' => true
                ];

                array_push($button_list, $tmp_button);


            }

        return $bot->sendRequest("answerInlineQuery",
            [
                'cache_time' => 0,
                "inline_query_id" => json_decode($bot->getEvent())->id,
                "results" => json_encode($button_list)
            ]);
    }
});

/*$botman->hears('Сбросить фильтр', function ($bot) {
    $bot->userStorage()->delete();
    filterMenu($bot, "Вы сбросили собранный ролл");
});

$botman->hears('Форма ролла.*', function ($bot) {

    $telegramUser = $bot->getUser();
    $id = $telegramUser->getId();

    $form = $bot->userStorage()->get("form") ?? -1;

    $keyboard = [
        [
            ['text' => "Круглая" . ($form == 0 ? "\xE2\x9C\x85" : ""), 'callback_data' => "/filter form 0"],
            ['text' => "Квадратная" . ($form == 1 ? "\xE2\x9C\x85" : ""), 'callback_data' => "/filter form 1"],
            ['text' => "Треугольная" . ($form == 2 ? "\xE2\x9C\x85" : ""), 'callback_data' => "/filter form 2"],
        ],

    ];


    $res = $bot->sendRequest("sendMessage",
        [
            "chat_id" => "$id",
            "text" => "Форма ролла",
            'reply_markup' => json_encode([
                'inline_keyboard' =>
                    $keyboard
            ])
        ]);

    // $bot->reply(print_r($res,true));
});

$botman->hears('Количество.*', function ($bot) {
    $telegramUser = $bot->getUser();
    $id = $telegramUser->getId();

    $keyboard = [];

    $count = $bot->userStorage()->get("count") ?? -1;

    $tmp_keyboard_row = [];
    for ($i = 1; $i <= 40; $i++) {

        array_push($tmp_keyboard_row, ['text' => "$i шт." . ($i == $count ? "\xE2\x9C\x85" : ""), 'callback_data' => "/filter count $i"]);

        if ($i % 5 == 0) {
            array_push($keyboard, $tmp_keyboard_row);
            $tmp_keyboard_row = [];
        }
    }

    $bot->sendRequest("sendMessage",
        [
            "chat_id" => "$id",
            "text" => "Колличество роллов",
            'reply_markup' => json_encode([
                'inline_keyboard' =>
                    $keyboard
            ])
        ]);
});

$botman->hears('Покрытие.*', function ($bot) {
    $telegramUser = $bot->getUser();
    $id = $telegramUser->getId();

    $ingridients = \App\Ingredient::where("use_type", "=", "0")
        ->orWhere("use_type", "=", "1")
        ->get();

    $keyboard = [];

    $upper = $bot->userStorage()->get("upper") ?? -1;

    $tmp_keyboard_row = [];
    foreach ($ingridients as $key => $value) {

        array_push($tmp_keyboard_row, ['text' => $value->title . ($upper == $key ? "\xE2\x9C\x85" : ""), 'callback_data' => "/filter upper " . $key]);

        $index = $key + 1;
        if ($index % 2 == 0 || count($ingridients) == $index) {
            array_push($keyboard, $tmp_keyboard_row);
            $tmp_keyboard_row = [];
        }
    }

    $bot->sendRequest("sendMessage",
        [
            "chat_id" => "$id",
            "text" => "Форма ролла",
            'reply_markup' => json_encode([
                'inline_keyboard' =>
                    $keyboard
            ])
        ]);
});

$botman->hears('Начинка.*', function ($bot) {
    $telegramUser = $bot->getUser();
    $id = $telegramUser->getId();


    $ingridients = \App\Ingredient::where("use_type", "=", "0")
        ->orWhere("use_type", "=", "2")
        ->get();

    $keyboard = [];

    $inner = json_decode($bot->userStorage()->get("inner"), true) ?? [];

    $tmp_keyboard_row = [];
    foreach ($ingridients as $key => $value) {


        array_push($tmp_keyboard_row, ['text' => $value->title . (in_array($key, $inner) ? "\xE2\x9C\x85" : ""), 'callback_data' => "/filter inner " . $key]);

        $index = $key + 1;
        if ($index % 2 == 0 || count($ingridients) == $index) {
            array_push($keyboard, $tmp_keyboard_row);
            $tmp_keyboard_row = [];
        }
    }

    $bot->sendRequest("sendMessage",
        [
            "chat_id" => "$id",
            "text" => "Форма ролла",
            'reply_markup' => json_encode([
                'inline_keyboard' =>
                    $keyboard
            ])
        ]);
});

$botman->hears('.*Корзина.*', function ($bot) {

    $telegramUser = $bot->getUser();
    $id = $telegramUser->getId();

    $basket = json_decode($bot->userStorage()->get("basket")) ?? [];

    if (count($basket) == 0) {
        $bot->reply("Корзина пуста:(");
        return;
    }

    basketMenu($bot, "Cодержимое корзины");


    foreach ($basket as $key => $product) {
        $keybord = [
            [
                ['text' => "\xF0\x9F\x91\x89Детальнее", 'callback_data' => "/product_info " . $product->id],
                ['text' => "Убрать (" . $product->price . "₽)", 'callback_data' => "/remove_from_basket " . $product->id]
            ],
        ];

        $bot->sendRequest("sendPhoto",
            [
                "chat_id" => "$id",
                "photo" => $product->image_url,
                'reply_markup' => json_encode([
                    'inline_keyboard' =>
                        $keybord
                ])
            ]);


    }


});

$botman->hears('.*Собрать ролл.*', function ($bot) {
    filterMenu($bot, "Собери свой ролл сам!");
});*//*$botman->hears('/category ([0-9]+) ([0-9]+)', function ($bot, $page, $catIndex) {

    $telegramUser = $bot->getUser();
    $id = $telegramUser->getId();

    $categories = Product::all()->unique('category');

    $products = \App\Product::where("category", $categories[$catIndex]->category)
        ->where("is_active", 1)
        ->take(10)
        ->skip($page * 10)
        ->get();

    if (count($products) == 0) {
        $bot->reply("Товары в категории не найдены");
        return;
    }

    foreach ($products as $key => $product) {
        $keybord = [
            [
                 ['text' => "\xF0\x9F\x91\x89Детальнее", 'callback_data' => "/product_info " . $product->id],
                ['text' => "\xE2\x86\xAAВ корзину(" . $product->price . "₽)", 'callback_data' => "/add_to_basket " . $product->id]
            ],

        ];

        if (count($products) - 1 == $key && $page == 0 && count($products) == 10)
            array_push($keybord, [
                ['text' => "\xE2\x8F\xA9Далее", 'callback_data' => "/category  " . ($page + 1) . " " . $catIndex]
            ]);

        if (count($products) - 1 == $key && $page != 0 && count($products) == 10)
            array_push($keybord, [
                ['text' => "\xE2\x8F\xAAНазад", 'callback_data' => "/category  " . ($page - 1) . " " . $catIndex],
                ['text' => "\xE2\x8F\xA9Далее", 'callback_data' => "/category  " . ($page + 1) . " " . $catIndex]
            ]);

        if (count($products) - 1 == $key && $page != 0 && count($products) < 10)
            array_push($keybord, [
                ['text' => "\xE2\x8F\xAAНазад", 'callback_data' => "/category  " . ($page - 1) . " " . $catIndex],
            ]);

        $message = "*" . $product->title . "*\n"
            . "_" . $product->description . "_\n"
            . "*Вес*:" . $product->mass . "гр.\n"
            . "*Цена*:" . $product->price . "₽\n"
            . "*Порция*:" . $product->portion_count . "шт.\n";

        $bot->sendRequest("sendPhoto",
            [
                "chat_id" => "$id",
                "photo" => $product->image_url,
                "caption" => $message,
                'reply_markup' => json_encode([
                    'inline_keyboard' =>
                        $keybord
                ])
            ]);
    }
})*/;
/*
$botman->hears('/product_info ([0-9]+)', function ($bot, $productId) {

    $telegramUser = $bot->getUser();
    $id = $telegramUser->getId();

    $product = \App\Product::find($productId);

    $message = "*" . $product->title . "*\n"
        . "_" . $product->description . "_\n"
        . "*Вес*:" . $product->mass . "гр.\n"
        . "*Цена*:" . $product->price . "₽\n"
        . "*Порция*:" . $product->portion_count . "шт.\n";


    $keyboard = [

        [
            ['text' => "\xE2\x86\xAAВ корзину(" . $product->price . "₽)", 'callback_data' => "/add_to_basket " . $product->id]
        ]
    ];

    $bot->sendRequest("sendPhoto",
        [
            "chat_id" => "$id",
            "photo" => $product->image_url,
            "caption" => $message,
            "parse_mode" => "Markdown",
            'reply_markup' => json_encode([
                'inline_keyboard' =>
                    $keyboard
            ])
        ]);

});
$botman->hears('/add_to_basket ([0-9]+)', function ($bot, $prodId) {
    $basket = json_decode($bot->userStorage()->get("basket"), true) ?? [];

    $product = Product::find($prodId);

    array_push($basket, $product);

    $bot->userStorage()->save([
        'basket' => json_encode($basket)
    ]);

    mainMenu($bot, "Товар добавлен в корзину");

});
$botman->hears('Заказать свой ролл', function ($bot) {

    $custom_roll_complete = $bot->userStorage()->get("upper")
        && $bot->userStorage()->get("inner")
        && $bot->userStorage()->get("count")
        && $bot->userStorage()->get("form");

    if (!$custom_roll_complete) {
        $bot->reply("Вы еще не закончили собирать свой ролл!");
        return;
    }
    $telegramUser = $bot->getUser();
    $id = $telegramUser->getId();

    $form = ["Круглая", "Квадратная", "Треугоьная"];
    $ingridients_upper = \App\Ingredient::where("use_type", "=", "0")
        ->orWhere("use_type", "=", "1")
        ->get();
    $ingridients_inner = \App\Ingredient::where("use_type", "=", "0")
        ->orWhere("use_type", "=", "2")
        ->get();


    $inner_ingredients = json_decode($bot->userStorage()->get("inner")) ?? [];

    $ingredient_title_inner = $ingredient_title_upper = "";
    $price_ingredient = 0;
    foreach ($inner_ingredients as $i) {

        $price_ingredient += $ingridients_inner[$i]->price;
        $ingredient_title_inner .= "\n_" . $ingridients_inner[$i]->title . "_ " . $ingridients_inner[$i]->mass . " гр. " . $ingridients_inner[$i]->price . "₽";

    }

    $price_ingredient += $ingridients_upper[$bot->userStorage()->get("upper")]->price;
    $ingredient_title_upper .= "\n_" . $ingridients_upper[$bot->userStorage()->get("upper")]->title . "_ "
        . $ingridients_upper[$bot->userStorage()->get("upper")]->mass . " гр. "
        . $ingridients_upper[$bot->userStorage()->get("upper")]->price . "₽\n";

    $count = $bot->userStorage()->get("count") ?? 0;

    $bot->userStorage()->save([
        "order" => json_encode([
            "form" => $form[$bot->userStorage()->get("form")] ?? "Не выбрана",
            "upper" => $ingredient_title_upper,
            "inner" => $ingredient_title_inner,
            "count" => $count,
            "price" => $price_ingredient * $count
        ])
    ]);

    $custom_order_price = $bot->userStorage()->get("order") != null ? json_decode($bot->userStorage()->get("order"))->price + 50 : 0;


    $keyboard = [
        [
            ['text' => "Перейти в корзину (" . $custom_order_price . "₽)", 'callback_data' => "/do_order"],
        ],
    ];

    $bot->sendRequest("sendMessage",
        [
            "chat_id" => "$id",
            "text" => "Спасибо за ваше исскуство!",
            'reply_markup' => json_encode([
                'inline_keyboard' =>
                    $keyboard
            ])
        ]);

});
$botman->hears("\xF0\x9F\x8D\xB1Меню", function ($bot) {
    $categories = \App\Product::all()->unique('category');

    $telegramUser = $bot->getUser();
    $id = $telegramUser->getId();


    $bot->sendRequest("sendPhoto",
        [
            "chat_id" => "$id",
            "photo" => "https://sun9-35.userapi.com/c205328/v205328682/56913/w8tBXIcG91E.jpg",
            "parse_mode" => "Markdown",
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [
                        ["text" => "Акци и скидки", "url" => "https://t.me/skidki_dn_bot"]
                    ]
                ],
            ])
        ]);

    foreach ($categories as $key => $category) {
        //array_push($inline_keyboard, [["text" => $category->category, "callback_data" => "/category 0 $key"]]);

        $bot->sendRequest("sendPhoto",
            [
                "chat_id" => "$id",
                "caption" => $category->category,
                "photo" => $category->image_url,
                "parse_mode" => "Markdown",
                'reply_markup' => json_encode([
                    'inline_keyboard' => [
                        [
                            ["text" => "\xF0\x9F\x91\x89Детальнее", "callback_data" => "/category 0 $key"]
                        ]
                    ],
                ])
            ]);
    }

});
$botman->hears('/remove_from_basket ([0-9]+)', function ($bot, $prodId) {
    $basket = json_decode($bot->userStorage()->get("basket")) ?? [];

    $basket_tmp = [];

    foreach ($basket as $product) {
        if ($product->id != $prodId)
            array_push($basket_tmp, $product);
    }

    $bot->userStorage()->save([
        'basket' => json_encode($basket_tmp)
    ]);

    if (count($basket_tmp) == 0)
        mainMenu($bot, "Товар удален из корзины");
    else
        basketMenu($bot, "Товар удален из корзины");

});
$botman->hears('/do_order|Оформить заказ.*', BotManController::class . "@orderConversation");
$botman->hears('/do_new_order|Оформить заказ.*', BotManController::class . "@newOrderConversation");
$botman->hears('/filter ([a-zA-Z0-9]+) ([0-9]+)', function ($bot, $name, $value) {


    if ($name != "inner")
        $bot->userStorage()->save([
            "$name" => $value
        ]);
    else {
        $inner_layer = json_decode($bot->userStorage()->get("inner"), true) ?? [];

        if (in_array($value, $inner_layer)) {
            $tmp_array = [];

            foreach ($inner_layer as $inner_item)
                if ($inner_item != $value)
                    array_push($tmp_array, $inner_item);

            $bot->userStorage()->save([
                "$name" => json_encode($tmp_array)
            ]);
        }

        if (count($inner_layer) == 4) {
            $bot->reply("Максимум 4 элемента начинки!");
        }

        Log::info(count($inner_layer));

        if (count($inner_layer) < 4 && !in_array($value, $inner_layer)) {

            Log::info("TEST");

            array_push($inner_layer, $value);


            Log::info(print_r($inner_layer, true));
            Log::info("value=$value");

            $bot->userStorage()->save([
                "$name" => json_encode($inner_layer)
            ]);
        }


    }


    filterMenu($bot, "Так держать, твой рол всё лучше и лучше!");
});*/
