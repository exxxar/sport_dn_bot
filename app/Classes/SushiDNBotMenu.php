<?php


namespace App\Classes;


use App\Models\SkidkaServiceModels\Category;
use App\Enums\AchievementTriggers;
use App\Events\AchievementEvent;
use App\User;
use BotMan\Drivers\Telegram\TelegramDriver;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Facades\Image;
use Telegram\Bot\FileUpload\InputFile;
use Telegram\Bot\Laravel\Facades\Telegram;

trait SushiDNBotMenu
{


    protected $keyboard_main;



    public function initKeyboards()
    {

        $this->keyboard_main = [
            [
                "\xF0\x9F\x8F\xACАкции по компаниям", "\xF0\x9F\x8E\xADАкции по категориям",
            ],
            [
                "\xF0\x9F\x8E\xAAНаши мероприятия", "\xF0\x9F\x8E\xB4Акции в Instagram",
            ],
            [
                "\xF0\x9F\x8E\xAFСистема достижений", "\xF0\x9F\x8E\xB0Розыгрыши"
            ],
            [
                "\xF0\x9F\x8C\x8FСистема Гео-квестов"
            ],
            [
                "\xF0\x9F\x94\x99Главное меню"
            ]
        ];


    }


    public function questMenu($message)
    {
        $this->initKeyboards();
        $this->sendMenu($message, $this->keyboard_geo_quest);
    }


    public function faqMenu($message)
    {
        $this->initKeyboards();
        $this->sendMenu($message, $this->keyboard_faq);
    }

    public function lotteryMenu($message)
    {
        $this->initKeyboards();
        $this->sendMenu($message, $this->keyboard_lottery);
    }

    public function achievementsMenu($message)
    {
        $this->initKeyboards();
        $this->sendMenu($message, $this->keyboard_achievements);
    }

    public function paymentMenu($message)
    {
        $this->initKeyboards();
        $this->sendMenu($message, $this->keyboard_payments);
    }

    public function friendsMenu($message)
    {
        $this->initKeyboards();
        $this->sendMenu($message, $this->keyboard_friends);
    }

    public function promotionsMenu($message)
    {
        $this->initKeyboards();
        $this->sendMenu($message, $this->keyboard_promotions);
    }


}
