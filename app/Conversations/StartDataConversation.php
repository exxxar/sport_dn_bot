<?php

namespace App\Conversations;

use App\CashBackHistory;
use App\Classes\BaseBot;
use App\User;

use BotMan\BotMan\Messages\Attachments\Image;
use BotMan\BotMan\Messages\Attachments\Location;
use BotMan\BotMan\Messages\Outgoing\OutgoingMessage;
use Carbon\Carbon;
use DateTime;
use Illuminate\Foundation\Inspiring;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Conversations\Conversation;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Laravel\Facades\Telegram;

class StartDataConversation extends Conversation
{

    protected $data;
    protected $bot;
    protected $code;
    protected $request_user_id;
    protected $user;
    protected $check_info;
    protected $money_in_check;
    protected $request_buyer_id;
    protected $cashback_percent;

    public function createUser()
    {
        $telegramUser = $this->bot->getUser();
        $id = $telegramUser->getId();
        $username = $telegramUser->getUsername();
        $lastName = $telegramUser->getLastName();
        $firstName = $telegramUser->getFirstName();

        $user = User::where("telegram_chat_id", $id)->first();
        $parent = User::where("telegram_chat_id", intval($this->request_user_id))->first();

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
                'parent_id' => $parent->id ?? null,
                'birthday' => '',
            ]);
        return $user;
    }

    public function mainMenu($message)
    {
        $telegramUser = $this->bot->getUser();
        $id = $telegramUser->getId();

        $user = User::where("telegram_chat_id", $id)->first();

        if (is_null($user))
            $user = $this->createUser();


        $keyboard = [];

        array_push($keyboard, ["\xE2\x9A\xA1Акции и мероприятия", "\xE2\x98\x95Услуги"]);
        if (!$user->is_vip)
            array_push($keyboard, ["\xE2\x9A\xA1Анкета VIP-пользователя"]);
        else
            array_push($keyboard, ["\xE2\x9A\xA1Special CashBack system"]);

        /*    array_push($keyboard,["\xF0\x9F\x8E\xB0Розыгрыш"]);*/
        array_push($keyboard, ["\xF0\x9F\x92\xADО Нас"]);

        if ($user->is_admin)
            array_push($keyboard, ["\xE2\x9A\xA0Админ. статистика"]);

        $this->bot->sendRequest("sendMessage",
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

    public function __construct($bot, $data)
    {

        $this->bot = $bot;
        $this->data = $data;
        $this->code = null;
        $this->request_user_id = null;
        $this->user = null;
        $this->check_info = '';
        $this->money_in_check = 0;
        $this->request_buyer_id = null;
        $this->cashback_percent = 10;
    }

    /**
     * Start the conversation
     */
    public function run()
    {
        try {
            $this->startWithData();
        } catch (\Exception $e) {
            Log::error(get_class($this) . " " . $e->getMessage() . " " . $e->getLine());
        }
    }

    /**
     * First question
     */
    public function startWithData()
    {
        $pattern = "/([0-9]{3})([0-9]{10})/";

        $string = base64_decode($this->data);

        $is_valid = preg_match_all($pattern, $string, $matches);


        if (!$is_valid) {
            $this->mainMenu("Главное меню");
            return;
        }

        $this->code = $matches[1][0];
        $this->request_user_id = $matches[2][0];

        $telegramUser = $this->bot->getUser();
        $id = $telegramUser->getId();

        $this->user = User::where("telegram_chat_id", $id)->first();


        if (is_null($this->user)) {
            $this->user = $this->createUser();

            Telegram::sendMessage([
                'chat_id' => $this->request_user_id,
                'parse_mode' => 'Markdown',
                'text' => "По вашей реферальной ссылке перешел пользователь " . (
                        $this->user->fio_from_telegram ??
                        $this->user->phone ??
                        $this->user->name ??
                        $this->user->email
                    ),
            ]);

            $this->mainMenu("Главное меню: _спасибо что пешерешли по реферальной ссылке!_");
            return;

        }

        if ($this->code == "005") {
            $tmp_user_id = (string)$this->user->telegram_chat_id;
            while (strlen($tmp_user_id) < 10)
                $tmp_user_id = "0" . $tmp_user_id;

            $code = base64_encode("001" . $tmp_user_id);
            $url_link = "https://t.me/" . env("APP_BOT_NAME") . "?start=$code";

            $keyboard = [
                [
                    ['text' => "Запустить систему CashBack", 'url' => "$url_link"],

                ]
            ];

            Telegram::sendMessage([
                'chat_id' => $this->request_user_id,
                'parse_mode' => 'Markdown',
                'text' => sprintf("Пользователь %s (%s) хочет воспользоваться услугой CashBack",
                    ($this->user->name ?? $this->user->fio_from_telegram ?? $this->user->telegram_chat_id),
                    ($this->user->phone ?? "У пользователя нет телефонного номера")),
                'reply_markup' => json_encode([
                    'inline_keyboard' =>
                        $keyboard
                ])
            ]);
            $this->mainMenu("Главное меню");
            return;
        }


        if (!$this->user->is_admin) {
            $this->mainMenu("Вы не можете переходить по собственной ссылке:) Делитесь ею с друзьями!");
            return;
        }

        switch ($this->code) {
            case "001":
                $this->askForAction();
                break;
            case "002":
                $this->acceptImage();
                break;
            case "003":
                $this->declineImage();
                break;
            default:
                $this->mainMenu("Неопознанный код операции");
                break;

        }


    }


    public function askForAction()
    {
        $question = Question::create("Какое действие выполнить?")
            ->addButtons([
                Button::create("Списать CashBack")->value('askforpay'),
                Button::create("Начислить CashBack 10%")->value('addcashback_ten'),
                Button::create("Начислить CashBack 5%")->value('addcashback_five'),
                Button::create("Добавить администратора")->value('addadmin'),
                Button::create("Убрать администратора")->value('removeadmin'),
                Button::create("Завершить работу")->value('stopcashback'),
            ]);

        $this->ask($question, function (Answer $answer) {
            if ($answer->isInteractiveMessageReply()) {
                $selectedValue = $answer->getValue();

                if ($selectedValue == "askforpay") {
                    $this->askForPay();
                }

                if ($selectedValue == "addcashback_ten") {
                    $this->cashback_percent = 10;
                    $this->askForCashback();
                }

                if ($selectedValue == "addcashback_five") {
                    $this->cashback_percent = 5;
                    $this->askForCashback();
                }

                if ($selectedValue == "addadmin") {
                    $this->askForAddAdmin(true);
                }

                if ($selectedValue == "removeadmin") {
                    $this->askForAddAdmin(false);
                }


                if ($selectedValue == "stopcashback")
                    $this->bot->reply("Работа с административной панелью завершена!");
                    return;

            }
        });
    }


    public function askForAddAdmin($flag = true)
    {
        $recipient_user = User::where("telegram_chat_id", intval($this->request_user_id))->first();
        if (!$recipient_user) {
            $this->mainMenu("Пользователь не найден!");
            return;
        }

        $recipient_user->is_admin = $flag;
        $recipient_user->save();


        $this->bot->reply(sprintf(($flag?"Пользователь %s успшено назанчен администратором!":"Пользователь %s снят с должности администратора!"), ($recipient_user->phone ?? $recipient_user->fio_from_telegram ?? $recipient_user->name)));

        Telegram::sendMessage([
            'chat_id' => $recipient_user->telegram_chat_id,
            'parse_mode' => 'Markdown',
            'text' => ($flag?"Вы назначены администратором!":"Вас сняли с должности администратора!"),
        ]);
    }

    public function acceptImage()
    {
        $recipient_user = User::where("telegram_chat_id", intval($this->request_user_id))->first();
        if (!$recipient_user) {
            $this->mainMenu("Пользователь не найден!");
            return;
        }

        $this->bot->reply(sprintf("Скриншот пользователя %s успешно подтвержден!", ($recipient_user->fio_from_telegram ?? $recipient_user->name)));

        Telegram::sendMessage([
            'chat_id' => $recipient_user->telegram_chat_id,
            'parse_mode' => 'Markdown',
            'text' => "Спасибо за Ваш сторис, хорошего дня!, мы с радостью предоставляем Вам скидку в размере 20%. Покажите данное сообщение администратору!",
        ]);
    }

    public function declineImage()
    {
        $recipient_user = User::where("telegram_chat_id", intval($this->request_user_id))->first();
        if (!$recipient_user) {
            $this->mainMenu("Пользователь не найден!");
            return;
        }

        $this->bot->reply(sprintf("Скриншот пользователя %s отклонен!", ($recipient_user->fio_from_telegram ?? $recipient_user->name)));

        Telegram::sendMessage([
            'chat_id' => $recipient_user->telegram_chat_id,
            'parse_mode' => 'Markdown',
            'text' => "Данный скриншот отклонен администратором, т.к. не соответствует условиям акции! Попробуйте еще раз!",
        ]);
    }


    public function askForPay()
    {
        $recipient_user = User::where("telegram_chat_id", intval($this->request_user_id))->first();
        if (!$recipient_user) {
            $this->mainMenu("Пользователь не найден!");
            return;
        }

        $this->bot->reply("У пользователя " . $recipient_user->cashback_money . " руб. CashBack-а");

        $question = Question::create("Введите сумму для списания:")
            ->fallback(__("messages.ask_fallback"));

        $this->ask($question, function (Answer $answer) use ($recipient_user) {
            $nedded_bonus = $answer->getText();

            if (strlen(trim($nedded_bonus)) == 0 || !is_numeric($nedded_bonus)) {
                $this->askForPay();
                return;
            }

            $canPay = $recipient_user->cashback_money >= intval($nedded_bonus);

            if (!$canPay) {
                $this->mainMenu("У пользователя недостаточно CashBack-а!");

                Telegram::sendMessage([
                    'chat_id' => $recipient_user->telegram_chat_id,
                    'parse_mode' => 'Markdown',
                    'text' => "Требуется списать $nedded_bonus руб. У вас в наличии: " . $recipient_user->cashback_money . " руб.",
                ]);
                return;
            }

            CashBackHistory::create([
                'amount' => $nedded_bonus,
                'bill_number' => "Списание средств",
                'money_in_bill' => $nedded_bonus,
                'employee_id' => $this->user->id,
                'user_id' => $recipient_user->id,
                'type' => 1,
            ]);

            $recipient_user->cashback_money -= $nedded_bonus;
            $recipient_user->save();

            Telegram::sendMessage([
                'chat_id' => $recipient_user->telegram_chat_id,
                'parse_mode' => 'Markdown',
                'text' => "С вашего бонусного счета произведено списание $nedded_bonus руб.",
            ]);

            $this->mainMenu("Спасибо! Успешно списалось $nedded_bonus руб. Остаток:" . $recipient_user->cashback_money . " руб.");

            $this->askForAction();
            return;


        });
    }


    public function askForCashback()
    {
        $question = Question::create("Введите сумму из чека")
            ->fallback(__("messages.ask_fallback"));

        $this->ask($question, function (Answer $answer) {
            $this->money_in_check = $answer->getText();
            if (strlen(trim($this->money_in_check)) == 0 || !is_numeric($this->money_in_check)) {
                $this->askForCashback();
                return;
            }

            $this->askForCheckInfo();
        });
    }

    public function askForCheckInfo()
    {
        $question = Question::create("Введите номер чека")
            ->fallback(__("messages.ask_fallback"));

        $this->ask($question, function (Answer $answer) {
            $this->check_info = $answer->getText();
            if (strlen(trim($this->check_info)) == 0) {
                $this->askForCheckInfo();
                return;
            }
            $this->saveCashBack();
        });
    }

    public function saveCashBack()
    {

        $recipient_user = User::with(["parent"])->where("telegram_chat_id", intval($this->request_user_id))->first();

        if (!$recipient_user) {
            $this->mainMenu("Пользователь не найден!");
            return;
        }

        $cashback = ((intval($this->money_in_check) ?? 0) * ($this->cashback_percent??env("CAHSBAK_PROCENT")) / 100);
        $parent_cashback = ((intval($this->money_in_check) ?? 0) * env("NETWORK_CAHSBAK_PROCENT") / 100);

        $recipient_user->cashback_money += $cashback;
        $recipient_user->save();

        if (!is_null($recipient_user->parent_id)) {

            $parent = $recipient_user->parent;
            $parent->cashback_money += $parent_cashback;
            $parent->save();


            CashBackHistory::create([
                'amount' => $parent_cashback,
                'bill_number' => "CashBack от друга",
                'money_in_bill' => $parent_cashback,
                'employee_id' => $this->user->id,
                'user_id' => $parent->id,
                'type' => 0,
            ]);


            Telegram::sendMessage([
                'chat_id' => $parent->telegram_chat_id,
                'parse_mode' => 'Markdown',
                'text' => "Ваш друг " . (
                        $recipient_user->fio_from_telegram ??
                        $recipient_user->phone ??
                        $recipient_user->name ??
                        $recipient_user->email
                    ) . " принес Вам $parent_cashback руб. CashBack-а",
            ]);


        }


        CashBackHistory::create([
            'amount' => $cashback,
            'bill_number' => $this->check_info,
            'money_in_bill' => $this->money_in_check,
            'employee_id' => $this->user->id,
            'user_id' => $recipient_user->id,
            'type' => 0,
        ]);

        $this->mainMenu("Отлично! CashBack $cashback руб. начислен пользователю " . (
                $recipient_user->fio_from_telegram ??
                $recipient_user->phone ??
                $recipient_user->name ??
                $recipient_user->email
            )
        );

        Telegram::sendMessage([
            'chat_id' => $recipient_user->telegram_chat_id,
            'parse_mode' => 'Markdown',
            'text' => "На ваш бонусный счет начислено $cashback руб.",
        ]);


    }

}