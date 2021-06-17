<?php

namespace App\Conversations;

use App\User;
use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Laravel\Facades\Telegram;

class OrderConversation extends Conversation
{
    protected $bot;

    private function mainMenu($message)
    {
        $telegramUser = $this->bot->getUser();
        $id = $telegramUser->getId();

        $basket = json_decode($this->bot->userStorage()->get("basket")) ?? [];

        $count = count($basket) ?? null;

        foreach ($basket as $product) {
            $count += $product->price;
        }


        $custom_order_price = $this->bot->userStorage()->get("order") != null ? json_decode($this->bot->userStorage()->get("order"))->price +50 : 0;
        $count += $custom_order_price;


        $keyboard = [
            ["\xF0\x9F\x8D\xB1Меню", "\xF0\x9F\x92\xB0Корзина" . ($count == null ? "(0₽)" : "(" . $count . "₽)")],
            ["\xF0\x9F\x8D\xA3Собрать ролл"],
            ["\xF0\x9F\x92\xADО Нас"],
        ];


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

        Log::info("telegram chat id=$telegramUser");
        $this->bot = $bot;


        $this->user = User::where("telegram_chat_id", $telegramUser)->first();


    }

    public function askPhone()
    {
        $question = Question::create('Скажие мне свой телефонный номер')
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
                if ($tmp_user == null) {
                    $this->user->phone = $tmp_phone;
                    $this->user->save();
                }
                else
                {
                    $tmp_user->phone = $tmp_phone;
                    $tmp_user->save();
                }

                $this->sendOrder();
            }

        });
    }

    public function sendOrder()
    {

        $basket = json_decode($this->bot->userStorage()->get("basket")) ?? [];

        $order_tmp = "Новая заявка:\n"
            . "*Имя*:" . ($this->user->fio_from_telegram ?? $this->user->name ). "\n"
            . "*Телефон*:" . $this->user->phone . "\n"
            . "*Дата заказа*:" . (Carbon::now()) . "\n*Заказ*:\n";

        $summary = 0;

        foreach ($basket as $key => $product) {
            $summary += $product->price;
            $order_tmp .= ($key + 1) . ")" . $product->title . "_#" . $product->id . "_ " . $product->price . "₽ \n";
        }

        $custom_order = json_decode($this->bot->userStorage()->get("order")) ?? null;

        if ($custom_order) {
            $order_tmp .=
                 "*Форма*:" . ($custom_order->form??"Не установлено") . "\n"
                . "*Верхний слой*:" . ($custom_order->upper??"Не установлено") . "\n"
                . "*Начинка*:" . ($custom_order->inner??"Не установлено") . "\n"
                . "*Колличество*:" . ($custom_order->count??"Не установлено") . "\n"
                . "*Цена*:" . ($custom_order->price??"Не установлено") . "\n+50₽ Нори и рис\n";

            $summary += $custom_order->price + 50;
        }
        $order_tmp .= "*Сумма заказа*:" . $summary . "₽";

        try {
            Telegram::sendMessage([
                'chat_id' => env("CHANNEL_ID"),
                'parse_mode' => 'Markdown',
                'text' => $order_tmp,
                'disable_notification' => 'false'
            ]);
        } catch (\Exception $e) {
            Log::info("Ошибка отправки заказа в канал!");
        }

        $this->bot->userStorage()->save([
            'basket' => json_encode([])
        ]);

        $this->bot->userStorage()->delete();

        $this->mainMenu("Заказ отправлен!");

    }

    /**
     * Start the conversation.
     *
     * @return mixed
     */
    public function run()
    {
        //
        $this->askPhone();
    }
}
