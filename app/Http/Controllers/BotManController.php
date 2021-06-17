<?php

namespace App\Http\Controllers;

use App\Conversations\NewOrderConversation;
use App\Conversations\OrderConversation;
use App\Conversations\StartDataConversation;
use App\Conversations\VipConversation;
use BotMan\BotMan\BotMan;
use Illuminate\Http\Request;
use App\Conversations\LotteryConversation;

class BotManController extends Controller
{
    /**
     * Place your BotMan logic here.
     */
    public function handle()
    {
        $botman = app('botman');

        $botman->listen();
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function tinker()
    {
        return view('tinker');
    }

    /**
     * Loaded through routes/botman.php
     * @param BotMan $bot
     */
    public function lotteryConversation(BotMan $bot)
    {
        $bot->startConversation(new LotteryConversation($bot));
    }

    public function orderConversation(BotMan $bot)
    {
        $bot->startConversation(new OrderConversation($bot));
    }

    public function newOrderConversation(BotMan $bot)
    {
        $bot->startConversation(new NewOrderConversation($bot));
    }

    public function vipConversation(BotMan $bot)
    {
        $bot->startConversation(new VipConversation($bot));
    }

    public function startDataConversation(BotMan $bot,$data)
    {
        $bot->startConversation(new StartDataConversation($bot,$data));
    }
}
