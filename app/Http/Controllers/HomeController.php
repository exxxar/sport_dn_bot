<?php

namespace App\Http\Controllers;

use App\Classes\Utilits;
use App\Product;
use App\User;
use ATehnix\VkClient\Auth;
use ATehnix\VkClient\Client;
use Illuminate\Http\Request;

class HomeController extends Controller
{

    use Utilits;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth')->except(["getRange"]);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        $auth = new Auth('6803875', 'l1rMo05qkLGM8BSh5KbQ', 'http://isushi-dn.ru/admin', 'market');


        $token = null;

        if ($request->has("code")) {
            $token = $auth->getToken($request->get('code'));

            $api = new Client;
            $api->setDefaultToken($token);

            $response = $api->request('market.getAlbums', [
                'owner_id' => -142695628,
                'count' => 50
            ]);


            Product::truncate();
            //работает
            foreach ($response["response"]["items"] as $item) {
                //echo $item["id"].$item["title"]." ".$item["photo"]["photo_807"]."<br>";

                if (intval($item["id"])<0)
                    continue;

                $response2 = $api->request('market.get', [
                    'owner_id' => -142695628,
                    'album_id' => abs($item["id"]),
                    'count' => 200
                ]);

                foreach ($response2["response"]["items"] as $item2) {
                    //echo $item2["description"]." ".$item2["price"]["text"]." ".$item2["thumb_photo"]." ".$item2["title"]."<br>";


                    if (mb_strlen(trim($item["title"]))==0)
                        continue;

                    preg_match_all('|\d+|', $item2["description"], $matches);

                    $count = $matches[0][0] ?? 0;
                    $weight = $matches[0][1] ?? 0;

                    preg_match_all('|\d+|', $item2["price"]["text"], $matches);

                    $price = intval($item2["price"]["amount"]) / 100;

                    Product::create([
                        'title' => $item2["title"],
                        'description' => $item2["description"],
                        'category' => $item["title"],
                        'mass' => "$weight",
                        'price' => $price,
                        'portion_count' => "$count",
                        'image_url' => $item2["thumb_photo"],
                        'site_url' => '',
                        'is_active' => true
                    ]);
                }


                sleep(2);

            }
            //dd($response["items"]);

        }

        return view('home', compact("auth", "token"));
    }

    public function searchAjax(Request $request)
    {
        $vowels = array("(", ")", "-", " ");
        $tmp_phone = $request->get("query");
        $tmp_phone = str_replace($vowels, "", $tmp_phone);
        return User::where('phone', 'like', '%' . $tmp_phone . '%')->get();
    }

    public function search(Request $request)
    {
        $vowels = array("(", ")", "-", " ");
        $tmp_phone = $request->get("phone");
        $tmp_phone = str_replace($vowels, "", $tmp_phone);
        $user = User::where("phone", $tmp_phone)->first();
        if ($user)
            return redirect()
                ->route("users.show", $user->id);
        return back()
            ->with("success", "Пользователь не найден!");
    }

    public function getRange(Request $request)
    {
        $request->validate([
            'address' => 'required',
        ]);


        $point = $request->get("address") ?? '';

        $coords = (object)$this->getCoordsByAddress($point);

        $range = ($this->calculateTheDistance(
            48.012478,
            37.821319,
            $coords->latitude,
            $coords->longitude));

        $price = ceil(($range + 1) * 10);


        return response()
            ->json([
                "range" => floatval(sprintf("%.2f", $range)),
                "price" => $price
            ]);
    }
}
