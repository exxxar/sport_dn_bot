<?php

namespace App\Http\Controllers;

use App\Enums\UseIngredientType;
use App\Ingredient;
use App\Prize;
use App\Product;
use App\Promocode;
use App\User;
use Illuminate\Http\Request;
use Telegram\Bot\Laravel\Facades\Telegram;

class WelcomeController extends Controller
{

    public function phone(Request $request)
    {
        $user = User::where("telegram_chat_id", $request->get("chat_id"))->first();

        return response()
            ->json([
                "hasPhone" => !is_null($user->phone)
            ]);
    }

    public function getList()
    {
        $prizes = Prize::all();
        return response()
            ->json([
                "card_list" => $prizes
            ]);
    }

    public function promoValidate(Request $request)
    {
        $request->validate([
            'promocode' => "required",
            "phone" => 'required'
        ]);

        $promocode = $request->get("promocode") ?? null;
        $phone = $request->get("phone") ?? null;

        $user = User::where("phone", $phone)
            ->first();

        if (is_null($user)) {
            User::create([
                'name' => $phone,
                'email' => "$phone@isushi-dn.ru",
                'phone' => $phone,
                'password' => bcrypt($phone),
            ]);
        }

        $promocode = Promocode::where("code", $promocode)
            ->first();

        if (is_null($promocode))
            return response()
                ->json([
                    "message" => "Такой промокод не существует",
                    "is_valid" => false,
                ]);

        if ($promocode->activated == true)
            return response()
                ->json([
                    "message" => "Такой промокод уже был активирован",
                    "is_valid" => false,
                ]);


        return response()
            ->json([
                "code_id" => $promocode->id,
                "is_valid" => true,
            ]);
    }

    public function check(Request $request)
    {
        $request->validate([
            'promocode' => "required",
            "phone" => 'required'
        ]);

        $promocode = $request->get("promocode");
        $phone = $request->get("phone");

        $prizes = Prize::all();
        $prizes->shuffle();

        $user = User::where("phone", $phone)->first();


        $promocode = Promocode::where("code", $promocode)->first();
        $promocode->activated = true;
        $promocode->user_id = $user->id;
        $promocode->save();

        $prize = $prizes->random(1);


            Telegram::sendMessage([
                'chat_id' => env("CHANNEL_ID"),
                'parse_mode' => 'Markdown',
                'text' => sprintf(($prize->type === 0?"Заявка на получение приза":"*Пользователь получил виртуальный приз*")."\nНомер телефона:_%s_\nПриз: [#%s] \"%s\"",
                    $user->phone,
                    $prize[0]->id,
                    $prize[0]->title),
                'disable_notification' => 'false'
            ]);


        return response()
            ->json([
                "results" => $prize
            ]);
    }

    public function sendRequest(Request $request)
    {
        $name = $request->get("name") ?? '';
        $phone = $request->get("phone") ?? '';
        $message = $request->get("message") ?? '';
        $summary_price = $request->get("summary_price") ?? 0;



        $promo = null;

        $vowels = array("(", ")", "-", " ");
        $phone = str_replace($vowels, "", $phone);

        $user = User::where("email", "$phone@isushi-dn.ru")->first();

        if (is_null($user))
            User::create([
                'name' => $name,
                'email' => "$phone@isushi-dn.ru",
                'phone' => $phone,
                'password' => bcrypt($phone),
            ]);

        if ($summary_price > 1500) {
            $promo = \App\Promocode::create([
                'code' => uniqid(),
                'activated' => 0,
                'user_id' => null,
            ]);
        }

        Telegram::sendMessage([
            'chat_id' => env("CHANNEL_ID"),
            'parse_mode' => 'Markdown',
            'text' => sprintf("*Заявка с сайта:*\n_%s_\n_%s_\n%s", $name, $phone, $message),
            'disable_notification' => 'false'
        ]);

        return response()
            ->json([
                "code" => is_null($promo) ? '' : $promo->code
            ]);
    }

    public function getIngredients(Request $request, $type)
    {
        return response()
            ->json([
                "ingredients" => Ingredient::where("use_type", $type)
                    ->orWhere("use_type", 0)
                    ->get()
            ]);
    }

    public function getProduct($id)
    {
        return response()
            ->json([
                "product" => Product::find($id)
            ]);
    }
}
