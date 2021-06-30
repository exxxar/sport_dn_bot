<?php

use App\Drivers\TelegramInlineQueryDriver;
use App\Http\Controllers\BotManController;
use App\Prize;
use App\Product;
use App\User;
use BotMan\BotMan\BotMan;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Mpdf\Mpdf;
use Telegram\Bot\FileUpload\InputFile;
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
        array_push($keyboard, ["\xE2\x9A\xA1CASH BACK system"]);

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
        ],
        [
            ['text' => "Пользователи в PDF", 'callback_data' => "/get_users_pdf"]
        ],
        [
            ['text' => "Списания и начисления в PDF", 'callback_data' => "/get_statistic_pdf"]
        ],

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

$botman->hears('/get_users_pdf', function ($bot) {
    $telegramUser = $bot->getUser();
    $id = $telegramUser->getId();

    $user = User::where("telegram_chat_id", $id)->first();

    if (is_null($user))
        return;

    if (!$user->is_admin)
        return;

    $mpdf = new Mpdf();

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

    $current_date = Carbon::now("+3:00");

    $users = User::all();

    $tmp = "";

    foreach ($users as $user) {
        $tmp_parent_user = is_null($user->parent_id) ? "-" : ($user->parent->fio_from_telegram ?? $user->parent->telegram_chat_id)."[#".$user->parent_id."]";

        $tmp .= sprintf("<tr><td>#%s</td> <td>%s<br>Пригласил %s <br>Зарегестрирован <strong>%s</strong> <br>Последнее действие <strong>%s</strong>  </td>  <td>%s</td> <td>%s </td> <td> %s руб.</td></tr>",
            $user->id,
            ("<strong>" . ($user->fio_from_telegram ?? $user->telegram_chat_id) . "</strong> "),
            $tmp_parent_user,
            $user->created_at,
            $user->updated_at,
            ($user->phone ?? "Без номера"),
            ($user->birthday ?? "Без даты рождения"),
            ($user->cashback_money ?? 0)
        );
    }

    $number = Str::uuid();
    $mpdf->WriteHTML("<h1>Стастика пользователей</h1>");
    $mpdf->WriteHTML("<h6>Уникальный идентификатор документа <strong style='color:darkred'>$number</strong></h6>");
    $mpdf->WriteHTML('<h3>Сервис "Body Master"</h3>');
    $mpdf->WriteHTML('<hr>');
    $mpdf->WriteHTML("<ul>
 <li>Всего пользователей в БД <strong>$users_in_bd</strong></li>
 <li>VIP-пользователй в БД <strong>$vip_in_bd</strong></li>
 <li>Пользователей в БД за день <strong>$users_in_bd_day</strong></li>
 <li>VIP-пользователй в БД за день <strong>$vip_in_bd_day</strong></li>

 <li>Дата и время <strong>$current_date!</strong></li>
</ul>
<hr>
<h3>Пользователи в БД:</h3>
<style>
th:nth-child(1),
td:nth-child(1) {
width: 50px;
}
th:nth-child(2),
td:nth-child(2) {
width: 400px;
}
th:nth-child(3),
td:nth-child(3) {
width: 100px;
}
th:nth-child(4),
td:nth-child(4) {
width: 100px;
}
th:nth-child(5),
td:nth-child(5) {
width: 100px;
}
</style>
");


    $mpdf->WriteHTML("<table>
<tr>
<td><strong>№</strong></td>
<td><strong>Пользователь</strong></td>
<td><strong>Номер телефона</strong></td>
<td><strong>Дата рождения</strong></td>
<td><strong>CashBack, руб</strong></td>
</tr>
$tmp
</table>
<hr>

<h4>Команда <span style='color:red'>BodyMaster</span> благодарит Вас за использование нашего сервиса! Мы стараемся быть лучше для Вас!</h4>
");
    $file = $mpdf->Output("users-statistic.pdf", \Mpdf\Output\Destination::STRING_RETURN);

    Storage::put("users-statistic.pdf", $file);


    /*  Mail::to()
          ->send(new \App\Mail\CheckMail(storage_path('app\public')."\\codes.pdf"));*/


    Telegram::sendDocument([
        'chat_id' => $id,
        'document' => InputFile::create(storage_path('app') . "/users-statistic.pdf"),
        'parse_mode' => "Markdown",
        'caption' => "Статистика пользователей"
    ]);

    Storage::delete("users-statistic.pdf");

});

$botman->hears('/get_statistic_pdf', function ($bot) {
    $telegramUser = $bot->getUser();
    $id = $telegramUser->getId();

    $user = User::where("telegram_chat_id", $id)->first();

    if (is_null($user))
        return;

    if (!$user->is_admin)
        return;

    $mpdf = new Mpdf();

    $cashbackHistories = \App\CashBackHistory::all();

    $current_date = Carbon::now("+3:00");

    $tmp = "";

    foreach ($cashbackHistories as $history) {
        $tmp_user = ($history->user->fio_from_telegram ??$history->user->telegram_chat_id)."[#".$history->user->id."]";

        $tmp_employee =($history->employee->fio_from_telegram ??$history->employee->telegram_chat_id)."[#".$history->employee->id."]";

        $tmp .= sprintf("<tr><td>#%s</td> <td>%s руб.</td>  <td>%s</td> <td>%s руб.</td> <td>%s </td> <td> %s </td> <td> %s </td> <td> %s </td></tr>",
            $history->id,
            $history->amount,
            $history->bill_number,
            $history->money_in_bill,
            $tmp_employee,
            $tmp_user,
            ($user->type==0?"Начисление":"Списание"),
           $history->created_at
        );
    }

    $number = Str::uuid();
    $mpdf->WriteHTML("<h1>Стастика по начислениям</h1>");
    $mpdf->WriteHTML("<h6>Уникальный идентификатор документа <strong style='color:darkred'>$number</strong></h6>");
    $mpdf->WriteHTML('<h3>Сервис "Body Master"</h3>');
    $mpdf->WriteHTML('<hr>');
    $mpdf->WriteHTML("<ul>

 <li>Дата и время <strong>$current_date!</strong></li>
</ul>
<hr>
<h3>История начисления и списания CashBack:</h3>
<style>
th:nth-child(1),
td:nth-child(1) {
width: 50px;
}
th:nth-child(2),
td:nth-child(2) {
width: 100px;
}
th:nth-child(3),
td:nth-child(3) {
width: 100px;
}
th:nth-child(4),
td:nth-child(4) {
width: 100px;
}
th:nth-child(5),
td:nth-child(5) {
width: 100px;
}
th:nth-child(6),
td:nth-child(6) {
width: 100px;
}
th:nth-child(7),
td:nth-child(7) {
width: 100px;
}
th:nth-child(8),
td:nth-child(8) {
width: 100px;
}
</style>
");


    $mpdf->WriteHTML("<table>
<tr>
<td><strong>№</strong></td>
<td><strong>Сумма CashBack</strong></td>
<td><strong>Причина</strong></td>
<td><strong>Сумма в чеке</strong></td>
<td><strong>Сотрудник</strong></td>
<td><strong>Пользователь</strong></td>
<td><strong>Тип операции</strong></td>
<td><strong>Дата операции</strong></td>
</tr>
$tmp
</table>
<hr>

<h4>Команда <span style='color:red'>BodyMaster</span> благодарит Вас за использование нашего сервиса! Мы стараемся быть лучше для Вас!</h4>
");
    $file = $mpdf->Output("users-cashback-statistic.pdf", \Mpdf\Output\Destination::STRING_RETURN);

    Storage::put("users-cashback-statistic.pdf", $file);


    /*  Mail::to()
          ->send(new \App\Mail\CheckMail(storage_path('app\public')."\\codes.pdf"));*/


    Telegram::sendDocument([
        'chat_id' => $id,
        'document' => InputFile::create(storage_path('app') . "/users-cashback-statistic.pdf"),
        'parse_mode' => "Markdown",
        'caption' => "Статистика списания и начисления CashBack"
    ]);

    Storage::delete("users-cashback-statistic.pdf");
});

$botman->hears(".*Анкета VIP-пользователя|/do_vip", BotManController::class . "@vipConversation")->stopsConversation();
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
    $bot->reply("https://telegra.ph/BODY-MASTER---territoriya-krasivogo-i-zdorovogo-tela-06-29-2");
})->stopsConversation();

$botman->hears('.*Акции и мероприятия', function ($bot) {
    $bot->reply("https://telegra.ph/AKCII-I-MEROPRIYATIYA-06-29");
})->stopsConversation();


$botman->hears("/start ([0-9a-zA-Z=]+)", BotManController::class . '@startDataConversation');

$botman->hears('/start', function ($bot) {
    createUser($bot);
    mainMenu($bot, 'BODY MASTER - территория красивого и здорового тела, хорошего настроения и заряда энергией⚡');
})->stopsConversation();

$botman->hears('.*Услуги', function ($bot) {
    $bot->reply("https://telegra.ph/USLUGI--STOIMOST-06-29-2");
})->stopsConversation();

$botman->hears('.*CASH BACK system|.*Special CashBack system', function ($bot) {
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

    $message = sprintf("У вас *%s* руб.!\n_Для начисления CashBack при оплате за абонемент дайте отсканировать данный QR-код администратору_ *Body Master!*", $cashback);
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

    $bot->sendRequest("sendMessage",
        [
            "chat_id" => "$id",
            "text" => sprintf("%s\n%s", "Теперь ты можешь получать 10% CashBack от всех твоих покупок и 5% от покукпок друзей! Для этого подключи друзей к данной системе!\n_Дай отсканировать QR-код друзьям или делись ссылкой с друзьями и получай больше CashBack с каждой их покупки!_", "https://telegra.ph/CASH-BACK-SYSTEM-06-29"),
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

})->stopsConversation();
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


})->stopsConversation();

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
                    'thumb_url' => "https://sun9-56.userapi.com/impg/YVDBJLBg3mb3-4YtMq39ttw44YK6bJ5es82crQ/u-NDgH2nL7g.jpg?size=500x500&quality=96&sign=17ecd1cd62402db7d35842d45047c28c&type=album",
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
