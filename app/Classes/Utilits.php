<?php


namespace App\Classes;


use Allanvb\LaravelSemysms\Exceptions\SmsNotSentException;
use Allanvb\LaravelSemysms\Facades\SemySMS;
use App\Enums\FoodStatusEnum;
use App\Parts\Models\Fastoran\Promocode;
use App\Parts\Models\Fastoran\RestMenu;
use App\User;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Exceptions\TelegramResponseException;
use Telegram\Bot\Laravel\Facades\Telegram;
use Yandex\Geocode\Facades\YandexGeocodeFacade;

/**
 * Trait Utilits
 * @package App\Classes
 */
trait Utilits
{
    /**
     * @var int
     */
    protected $earth_radius = 6372795;

    /**
     * @param $fA
     * @param $lA
     * @param $fB
     * @param $lB
     * @return float|int
     */
    public function mathDist($fA, $lA, $fB, $lB)
    {
        // перевести координаты в радианы
        $lat1 = $fA * M_PI / 180;
        $lat2 = $fB * M_PI / 180;
        $long1 = $lA * M_PI / 180;
        $long2 = $lB * M_PI / 180;

// косинусы и синусы широт и разницы долгот
        $cl1 = cos($lat1);
        $cl2 = cos($lat2);
        $sl1 = sin($lat1);
        $sl2 = sin($lat2);
        $delta = $long2 - $long1;
        $cdelta = cos($delta);
        $sdelta = sin($delta);

// вычисления длины большого круга
        $y = sqrt(pow($cl2 * $sdelta, 2) + pow($cl1 * $sl2 - $sl1 * $cl2 * $cdelta, 2));
        $x = $sl1 * $sl2 + $cl1 * $cl2 * $cdelta;

//
        $ad = atan2($y, $x);
        $dist = $ad * $this->earth_radius;

        return $dist;

    }

    /**
     * @param $fA
     * @param $lA
     * @param $fB
     * @param $lB
     * @return float
     */
    public function calculateTheDistance($fA, $lA, $fB, $lB)
    {
        try {
            $content = file_get_contents("http://www.yournavigation.org/api/1.0/gosmore.php?flat=$fA&flon=$lA&tlat=$fB&tlon=$lB&v=motorcar&fast=1&layer=mapnik&format=geojson");
        } catch (\Exception $e) {
            $content = json_encode([
                "properties" => [
                    "distance" => 0
                ]
            ]);
        }

        return floatval(json_decode($content)->properties->distance ?? 0);

    }

    /**
     * @param $phone
     * @return string|string[]
     */
    public function preparePhone($phone)
    {
        $vowels = array("(", ")", "-", " ");
        return str_replace($vowels, "", $phone ?? '');
    }


    /**
     * @param $text
     * @return false|string|null
     */
    public function prepareSub($text)
    {

        $text = str_replace(["\n"], "", $text);

        $text = str_replace(["/"], "\\", $text);

        $start = mb_strpos($text, "выбор:");
        $end = mb_strpos($text, "Цена:");

        if ($start == 0 || $end == 0)
            return null;

        $res = mb_substr($text, $start + 6, $end - ($start + 6));

        $res = explode("\\", $res);

        $food_sub = [];
        foreach ($res as $r)
            array_push($food_sub, ["name" => trim($r)]);

        return count($food_sub) == 0 ? null : json_encode($food_sub);
    }

    /**
     * @return |null
     */
    public function getUser()
    {

        $id = auth()->guard('api')->user()->id ?? auth()->user()->id ?? null;

        return !is_null($id) ? User::find($id) : null;

    }

    /**
     * @param $uri
     * @param array $params
     * @return \Illuminate\Http\JsonResponse
     */
    public function doHttpRequest($uri, $params = [])
    {
        try {

            $req = Request::create($uri, 'POST', $params);

            $resp = app()->handle($req);

            return response()
                ->json(json_decode($resp->getContent())
                );

        } catch (\Exception $e) {
            Log::error(sprintf("%s:%s %s",
                $e->getLine(),
                $e->getFile(),
                $e->getMessage()
            ));
        }
    }

    /**
     * @param $phone
     * @param $message
     */
    public function sendSms($phone, $message)
    {
        try {
            SemySMS::sendOne([
                'to' => $phone,
                'text' => $message,
                'device_id' => 'active'
            ]);
        } catch (SmsNotSentException $e) {
            Log::error(sprintf("%s:%s %s",
                $e->getLine(),
                $e->getFile(),
                $e->getMessage()
            ));
        }

    }

    /**
     * @param $id
     * @param $message
     * @param array $keyboard
     */
    public function sendToTelegram($id, $message, $keyboard = [])
    {
        try {
            $this->sendMessageToTelegramChannel($id, $message, $keyboard);
            $this->sendMessageToTelegramChannel(env("TELEGRAM_FASTORAN_ADMIN_CHANNEL"), $message);
        } catch (TelegramResponseException $e) {
            Log::error(sprintf("%s:%s %s",
                $e->getLine(),
                $e->getFile(),
                $e->getMessage()
            ));
        }

    }

    /**
     * @param int $number
     * @param int $length
     * @return string
     */
    public function prepareNumber($number = 0, $length = 10)
    {
        $tmp = "" . $number;
        while (strlen($tmp) < $length)
            $tmp = "0" . $tmp;

        return $tmp;
    }

    /**
     * @param $address
     * @return array
     */
    public function getCoordsByAddress($address)
    {
        $data = YandexGeocodeFacade::setQuery($address ?? '')->load();

        $data = $data->getResponse();

        return [
            "latitude" => !is_null($data) ? $data->getLatitude() : 0,
            "longitude" => !is_null($data) ? $data->getLongitude() : 0
        ];
    }

    /**
     * @param $id
     * @param $message
     * @param array $keyboard
     */
    protected function sendMessageToTelegramChannel($id, $message, $keyboard = [])
    {
        Telegram::sendMessage([
            'chat_id' => $id,
            'parse_mode' => 'Markdown',
            'text' => $message,
            'reply_markup' => json_encode([
                'inline_keyboard' => $keyboard
            ])
        ]);

    }

    public function prepareChannelId($channelName)
    {
        try {
            $content = file_get_contents("https://api.telegram.org/bot" . env("TELEGRAM_BOT_TOKEN") . "/getChat?chat_id=@$channelName");
        } catch (\Exception $e) {
            $content = [];
        }

        return json_decode($content)->result->id ?? null;
    }

    public function prepareTelegramText(array $text = [])
    {
        $tmp = "";

        foreach ($text as $key => $value) {
            $tmp .= sprintf($key, $value);
        }

        return $tmp;
    }

    public function getPriceWithDiscountByCode($code, $productId, $userId = null)
    {

        $product = RestMenu::find($productId);

        if (is_null($product))
            return 0;

        if (is_null($code) || $product->food_status !== FoodStatusEnum::Promotion)
            return $product->food_price;

        $promocode = Promocode::with(["promotion"])
            ->where("code", $code)
            ->whereNull("user_id")
            ->first();

        if (is_null($promocode))
            return $product->food_price;

        if (is_null($promocode->promotion->product))
            return $product->food_price;

        if (!$promocode->promotion->active)
            return $product->food_price;

        if ($promocode->promotion->product->food_name != $product->food_name ||
            $promocode->promotion->product->rest_id != $product->rest_id)
            return $product->food_price;


        if (!is_null($userId)) {
            $promocode->user_id = $userId;
            $promocode->save();

            $this->sendMessageToTelegramChannel(env("TELEGRAM_FASTORAN_ADMIN_CHANNEL"),
                sprintf("Пользовател #%s успешно активировал промокод _%s_",
                    $userId,
                    $code
                )
            );
        }

        return min($product->food_price * (100 - $promocode->promotion->discount), 1);

    }
}
