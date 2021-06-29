<?php

namespace App\Conversations;

use App\CashBackHistory;
use App\User;
use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Laravel\Facades\Telegram;

class VipConversation extends Conversation
{
    protected $bot;
    protected $user;

    public function createUser()
    {
        $telegramUser = $this->bot->getUser();
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
                'cashback_money' => 0,
                'phone' => '',
                'birthday' => '',
            ]);
        return $user;
    }

    function mainMenu($message)
    {
        $telegramUser = $this->bot->getUser();
        $id = $telegramUser->getId();

        $user = User::where("telegram_chat_id", $id)->first();

        if (is_null($user))
            $user = $this->createUser();


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

    public function __construct($bot)
    {
        $telegramUser = $bot->getUser()->getId();
        $this->bot = $bot;
        $this->user = User::where("telegram_chat_id", $telegramUser)->first();
    }


    public function askName()
    {
        $question = Question::create('Как вас зовут?')
            ->fallback('Спасибо что пообщался со мной:)!');

        $this->ask($question, function (Answer $answer) {
            $tmp_name = $answer->getText();

            $this->user->fio_from_telegram = $tmp_name ?? $this->user->fio_from_telegram;
            $this->user->save();

            $this->askBirthday();
        });
    }


    public function askBirthday()
    {
        $question = Question::create('Введите дату совего рожедения в формате ДД.ММ.ГГГГ (например, 09.09.1991) и получайте бонусы!')
            ->fallback('Спасибо что пообщался со мной:)!');

        $this->ask($question, function (Answer $answer) {
            $tmp_birth = $answer->getText();

            $this->user->birthday = $tmp_birth ?? '01.01.1900';
            $this->user->save();

            $this->askPhone();
        });

    }

    public function askPhone()
    {
        $question = Question::create('Скажие мне свой телефонный номер в формате 071XXXXXXX')
            ->fallback('Спасибо что пообщался со мной:)!');

        $this->ask($question, function (Answer $answer) {
            $vowels = array("(", ")", "-", " ");
            $tmp_phone = $answer->getText();
            $tmp_phone = str_replace($vowels, "", $tmp_phone);
            if (strpos($tmp_phone, "+38") === false)
                $tmp_phone = "+38" . $tmp_phone;

            Log::info("phone=$tmp_phone");

            $pattern = "/^\+380\d{3}\d{2}\d{2}\d{2}$/";
            if (preg_match($pattern, $tmp_phone) == 0) {
                $this->bot->reply("Номер введен не верно...\n");
                $this->askPhone();
                return;
            } else {
                $tmp_user = User::where("phone", $tmp_phone)->first();

                if (!is_null($tmp_user)) {
                    if (!is_null($tmp_user->telegram_chat_id)) {
                        $this->bot->reply("Данный номер уже связан с учетной записью телеграм!\n");
                        $this->askPhone();
                        return;
                    }
                    $telegramUser = $this->bot->getUser();
                    $id = $telegramUser->getId();

                    $tmp_user->telegram_chat_id = $id;
                }
                $this->user->phone = $tmp_phone;
                $this->user->is_vip = true;
                // $this->user->cashback_money +=100 ;
                $this->user->save();

                /*   CashBackHistory::create([
                       'amount'=>100,
                       'bill_number'=>'Gift From Isushi',
                       'money_in_bill'=>0,
                       'employee_id'=>null,
                       'user_id'=>$this->user->id,
                       'type'=>0,
                   ]);

                   $this->bot->reply("Вам начислено 100 руб. CashBack");*/

                $this->mainMenu("Теперь Вы VIP-пользователь и у вас есть возможность накапливать CashBack!");

            }

        });
    }

    /**
     * Start the conversation.
     *
     * @return mixed
     */
    public function run()
    {
        //
        if (!$this->user->is_vip)
            $this->askName();

    }
}
