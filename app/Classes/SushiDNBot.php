<?php


namespace App\Classes;


use App\Models\SkidkaServiceModels\Achievement;
use App\Models\SkidkaServiceModels\Article;
use App\Models\SkidkaServiceModels\CashBackInfo;
use App\Models\SkidkaServiceModels\Category;
use App\Models\SkidkaServiceModels\Charity;
use App\Models\SkidkaServiceModels\CharityHistory;
use App\Models\SkidkaServiceModels\Company;
use App\Drivers\TelegramInlineQueryDriver;
use App\Enums\AchievementTriggers;
use App\Enums\Parts;
use App\Models\SkidkaServiceModels\Event;
use App\Events\AchievementEvent;
use App\Events\AddCashBackEvent;
use App\Models\SkidkaServiceModels\GeoPosition;
use App\Models\SkidkaServiceModels\GeoQuest;
use App\Models\SkidkaServiceModels\InstaPromotion;
use App\Models\SkidkaServiceModels\Prize;
use App\Models\SkidkaServiceModels\Promocode;
use App\Models\SkidkaServiceModels\Promotion;
use App\Models\SkidkaServiceModels\RefferalsPaymentHistory;
use App\Models\SkidkaServiceModels\UplodedPhoto;
use App\User;
use BotMan\BotMan\BotMan;
use BotMan\BotMan\Messages\Attachments\Image;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class SushiDNBot implements iSushiDNBot
{
    use BaseBot;

    public function getEventsAll($page)
    {
        $events = Event::with(['company'])
            ->skip($page * config("bot.results_per_page"))
            ->take(config("bot.results_per_page"))
            ->orderBy('position', 'DESC')
            ->get();

        $found = false;

        if (count($events) > 0) {

            foreach ($events as $key => $event) {

                if (!$event->isActive() || !$event->company->is_active)
                    continue;

                $keyboard = [];

                if (!is_null($event->promo_id)) {
                    array_push($keyboard, [
                        ["text" => __('messages.get_all_events_btn'), "callback_data" => "/promotion " . $event->promo_id]
                    ]);
                }

                if ($event->need_qr&&is_null($event->promo_id)){
                    array_push($keyboard, [
                        ["text" => "\xE2\x98\x95???????????????????????? ??????????", "callback_data" => "/activate_event " . $event->id]
                    ]);
                }

                $found = true;
                $this->sendPhoto(
                    "*" . $event->title . "*\n" . $event->description,
                    $event->event_image_url, $keyboard);

            }

        }

        if (count($events) == 0 || !$found)
            $this->reply(__("messages.events_message_1"));

        $this->pagination("/events $page", $events, $page, __("messages.ask_action"));

    }

    public function getPaymentsAll($page)
    {
        if (!$this->getUser()->hasPhone()) {
            $keyboard = [
                [
                    ["text" => "??????????????????", "callback_data" => "/fillinfo"],
                ]
            ];

            $this->sendMessage(__("messages.payment_message_1"), $keyboard);
            return;
        }

        $refs = $this->getUser()->getPayments($page);

        $tmp = "";

        foreach ($refs as $key => $ref)
            $tmp .= sprintf(__("messages.payment_message_4"),
                $ref->created_at,
                ($ref->company->title ?? $ref->company->id),
                $ref->value
            );

        $this->reply(strlen($tmp) > 0 ? $tmp : __("messages.payment_message_2"));
        $this->pagination("/payments", $refs, $page, (strlen($tmp) > 0 ? $tmp : __("messages.payment_message_3")));
    }

    public function getCashBacksAll($page)
    {
        if (!$this->getUser()->hasPhone()) {
            $keyboard = [
                [
                    ["text" => "??????????????????", "callback_data" => "/fillinfo"],
                ]
            ];

            $this->sendMessage(__("messages.cashback_message_1"), $keyboard);
            return;
        }

        $cashbacks = $this->getUser()->getCashBacksByUserId($page);

        $tmp = "";

        foreach ($cashbacks as $key => $cash)
            $tmp .= sprintf(__("messages.cashback_message_2"),
                $cash->company->title,
                $cash->created_at,
                $cash->check_info,
                round(intval($cash->money_in_check) * env("CAHSBAK_PROCENT") / 100)
            );

        $this->reply(strlen($tmp) > 0 ? $tmp : __("messages.cashback_message_3"));
        $this->pagination("/cashbacks", $cashbacks, $page, __("messages.ask_action"));
    }

    public function getAchievements($page, $isAll = true)
    {
        if ($isAll)
            $achievements = Achievement::where("is_active", 1)
                    ->skip($page * config("bot.results_per_page"))
                    ->take(config("bot.results_per_page"))
                    ->orderBy('position', 'ASC')
                    ->get() ?? null;
        else
            $achievements = $this->getUser()->getAchievements($page);

        if (count($achievements) == 0 || $achievements == null) {
            $this->reply(__("messages.achievements_message_1"));
            return;
        }

        foreach ($achievements as $key => $achievement) {

            $keyboard = [
                [
                    ["text" => "??????????????????", "callback_data" => "/achievements_description " . $achievement->id]
                ]
            ];
            $message = sprintf("%s *%s*\n_%s_",
                ($achievement->activated == 0 ? "" : "\xE2\x9C\x85"),
                ($achievement->title ?? "?????? ???????????????? [#" . $achievement->id . "]"),
                $achievement->description
            );
            $this->sendPhoto($message, $achievement->ach_image_url, $keyboard);
        }


        $this->pagination($isAll ? "/achievements_all" : "/achievements_my", $achievements, $page, __("messages.ask_action"));

    }

    public function getAchievementsAll($page)
    {
        $this->getAchievements($page, true);
    }

    public function getAchievementsMy($page)
    {
        $this->getAchievements($page, false);
    }

    public function getAchievementsInfo($id)
    {

        $achievement = Achievement::find($id);

        $stat = $this->getUser(["stats"])
                ->stats()
                ->where("stat_type", "=", $achievement->trigger_type->value)
                ->first() ?? null;

        if ($achievement == null) {
            $this->reply(__("messages.achievements_message_2"));
            return;
        }


        $currentVal = ($stat == null ? 0 : $stat->stat_value);

        $progress = ($currentVal >= $achievement->trigger_value ?
            "\n*?????????????? ??????????????????!*" :
            "????????????????:*" . $currentVal . "* ???? *" . $achievement->trigger_value . "*");

        $message = sprintf("*%s*\n_%s_\n%s",
            $achievement->title,
            $achievement->description,
            $progress
        );

        $this->sendPhoto($message, $achievement->ach_image_url);

        $message = sprintf(__("messages.achievements_message_3"),
            $achievement->prize_description
        );

        $this->sendPhoto($message, $achievement->prize_image_url);


        $on_ach_activated = $this->getUser(["achievements"])
            ->achievements()
            ->where("achievement_id", $id)
            ->first();


        $keyboard = [];

        if ($on_ach_activated)
            if ($on_ach_activated->activated == false)
                array_push($keyboard, [
                    ["text" => __("messages.achievements_btn_1"), "callback_data" => "/achievements_get_prize $id"]
                ]);

        array_push($keyboard, [
            ["text" => __("messages.achievements_btn_2"), "callback_data" => "/achievements_panel"]
        ]);

        $this->sendMessage(__("messages.ask_action"), $keyboard);
    }

    public function getAchievementsPrize($id)
    {
        $achievement = $this->getUser(["achievements"])
            ->achievements()
            ->where("achievement_id", $id)
            ->first();


        if ($achievement->activated == true) {
            $this->reply(__("messages.achievements_message_4"));
            return;
        }

        $stat = $this->getUser(["stats"])
                ->stats()
                ->where("stat_type", "=", $achievement->trigger_type->value)
                ->first() ?? null;

        $currentVal = $stat == null ? 0 : $stat->stat_value;

        if ($currentVal <= $achievement->trigger_value) {
            $this->reply(__("messages.achievements_message_5"));
            return;
        }

        $tmp_id = (string)$this->getChatId();
        while (strlen($tmp_id) < 10)
            $tmp_id = "0" . $tmp_id;

        $tmp_achievement_id = (string)$achievement->id;
        while (strlen($tmp_achievement_id) < 10)
            $tmp_achievement_id = "0" . $tmp_achievement_id;

        $code = base64_encode("012" . $tmp_id . $tmp_achievement_id);

        $qr_url = env("QR_URL") . "https://t.me/" . env("APP_BOT_NAME") . "?start=$code";

        $this->sendPhoto(__("messages.achievements_message_6"), $qr_url);

    }

    public function getRefLink()
    {

        if (!$this->getUser()->hasPhone()) {
            $keyboard = [
                [
                    ["text" => "??????????????????", "callback_data" => "/fillinfo"]
                ]
            ];

            $this->sendMessage("?? ?????? ???? ?????????????????? ???????????? ????????????????????! ???????????????? ?? ???????????? ??????????????:)", $keyboard);
            return;
        }

        $tmp_id = (string)$this->getChatId();
        while (strlen($tmp_id) < 10)
            $tmp_id = "0" . $tmp_id;


        $code = base64_encode("001" . $tmp_id . "0000000000");
        $url_link = "https://t.me/" . env("APP_BOT_NAME") . "?start=$code";
        $href_url_link = "[?????????????????? ?????? ?????????????????? ?????????????? (?????? ???????????????? ????????????) ?? ?????????????? ???????????? ????????????!]($url_link)";
        $this->sendMessage("$href_url_link");

        $keyboard = [
            [
                ["text" => "?? VK\xE2\xA4\xB4", "url" => "https://vk.com/share.php?url=$url_link"],
                ["text" => " ?? Facebook\xE2\xA4\xB4", "url" => "http://www.facebook.com/sharer.php?u=$url_link"]
            ],
        ];

        $this->sendMessage("_?????? ???????????? ?? ???????????????? ?? ???????????? ??????. ??????????!_", $keyboard);
    }

    public function getMainMenu()
    {
        $this->mainMenu("*?????????????? ????????*:\n_?????????????????? ??????, ?????????????? ????????????, ?????????????? ????????????!_");
    }

    public function getFAQBottomMenu()
    {
        $keyboard = [
            [
                ['text' => "?????????????? ?? ??????????", 'url' => env("CHANNEL_LINK")],

            ],
            [
                ['text' => __("messages.promo_menu_btn_4"), 'url' => env("APP_PROMO_LINK")],
            ]
        ];

        $this->faqMenu(__("messages.faq_message_1"));
        $this->sendMessage("???????????? ???????????????????? ?? ?????????? ????????????!", $keyboard);

    }

    /**
     * @deprecated ???????????????? ?? ???????????? ???? ??????????????????
     */
    public function getFAQMenu()
    {

        $keyboard = [
            [
                ['text' => "?????????????????? ???? ????????", 'callback_data' => "/help"],
                ['text' => "?????? ????????????????????????", 'callback_data' => "/for_users"],
            ],
            [
                ['text' => "???????????????????? ??????????????", 'callback_data' => "/rules"],
            ],
            [
                ['text' => "????????????????????", 'callback_data' => "/promouter"],
                ['text' => "??????????????????????", 'callback_data' => "/suppliers"],
            ],
            [
                ['text' => "?? ??????", 'callback_data' => "/about"],
                ['text' => "????????????????????????", 'callback_data' => "/dev"],
            ],
            [
                ['text' => '????????????????', 'callback_data' => '/start_lottery_test']
            ]


        ];

        $this->sendMessage("*F.A.Q.*\n_???? ???????????? ?????? ???????????? ????????????????????????? - ?????????????????? ???????? ????????????????! ?????????????? ???????????? ?? ????????????????????, ???????????????? ?? ????????????????????????!_", $keyboard);

    }

    /**
     * @deprecated ???????????????? ?? ???????????? ???? ??????????????????
     */
    public function getFAQSimpleMenu()
    {

        $keyboard = [
            [
                ['text' => __("messages.faq_btn_1"), 'callback_data' => "/articles 0"],

            ],
            [
                ['text' => __("messages.faq_btn_2"), 'url' => env('APP_URL') . "#faq"],
            ],
            [
                ['text' => __("messages.promo_menu_btn_4"), 'url' => env("APP_PROMO_LINK")],
            ],
            [
                ['text' => __("messages.faq_btn_3"), 'url' => env("CHANNEL_LINK")],
            ],
        ];

        $this->sendMessage(__("messages.faq_message_1"), $keyboard);

    }


    public function getPromotionsMenu()
    {
        $this->promotionsMenu(__("messages.promo_menu_message_1"));
    }

    public function getFriends($page)
    {

        $refs = $this->getUser()->getFriends($page);

        $sender = $this->getUser(["parent"])
            ->parent;

        $tmp = "";

        if ($sender != null) {

            $userSenderName = $sender->fio_from_telegram ??
                $sender->fio_from_request ??
                $sender->telegram_chat_id ??
                '?????????????????????? ????????????????????????';

            $tmp = "?????? ?????????????????? - \xF0\x9F\x91\x91*$userSenderName*\n";

        }

        if ($refs != null)
            foreach ($refs as $key => $ref) {
                if ($ref->recipient != null) {
                    $userName = $ref->recipient->fio_from_telegram ??
                        $ref->recipient->fio_from_request ??
                        $ref->recipient->telegram_chat_id ??
                        '?????????????????????? ????????????????????????';
                    $tmp .= ($key + 1) . ". " . $userName . ($ref->activated == 0 ? "\xE2\x9D\x8E" : "\xE2\x9C\x85") . "\n";
                }
            }

        $this->reply(strlen($tmp) > 0 ? $tmp : "?? ?????? ?????? ?????? ????????????:(");
        $this->pagination('/friends', $refs, $page, "???????? ????????????????");
    }


    public function getStatisticMenu()
    {
        $keyboard = [
            [
                ["text" => __("messages.cashback_btn_1"), "callback_data" => "/cashbacks 0"],
                ["text" => __("messages.cashback_btn_2"), "callback_data" => "/payments 0"],
            ],
            /*  [
                  ["text" => "??????????????????????????????????????", "callback_data" => "/charity"]
              ]*/
        ];
        $this->sendMessage(__("messages.cashback_message_4"), $keyboard);
    }

    public function getAchievementsMenu()
    {
        $this->achievementsMenu(__("messages.achievements_message_7"));
    }

    public function getPromouterMenu()
    {
        // TODO: Implement getPromouterMenu() method.
    }

    public function getPromotionsByCompany($page)
    {
        $companies = Company::where("is_active", true)
            ->orderBy('position', 'DESC')
            ->take(config("bot.results_per_page"))
            ->skip($page * config("bot.results_per_page"))
            ->get();

        if (count($companies) == 0) {
            $this->reply(__("messages.company_message_1"));
            return;
        }

        foreach ($companies as $company) {
            $keyboard = [
                [
                    ["text" => __("messages.company_btn_1"), "callback_data" => "/company " . $company->id . " 0"]
                ]
            ];

            if (!is_null($company->menu_url)) {
                array_push($keyboard, [
                    ["text" => __("messages.company_btn_2"), "url" => $company->menu_url]
                ]);
            }

            $this->sendPhoto('*' . $company->title . "*\n", $company->logo_url, $keyboard);
        }

        $this->pagination("/promo_by_company", $companies, $page, __("messages.ask_action"));

    }

    public function getPromotionsByCategory($page)
    {

        $categories = Category::orderBy('position', 'DESC')
            ->take(config("bot.results_per_page"))
            ->skip($page * config("bot.results_per_page"))
            ->get();

        if (count($categories) == 0) {
            $this->reply(__("messages.category_message_1"));
            return;
        }

        foreach ($categories as $cat) {

            $keyboard = [
                [
                    ["text" => __("messages.category_btn_1"), "callback_data" => "/category " . $cat->id . " 0"]
                ]
            ];

            $this->sendPhoto("*$cat->title*", $cat->image_url, $keyboard);
        }

        $this->pagination("/promo_by_category", $categories, $page, "???????????????? ????????????????");
    }

    public function getCategoryById($id, $page)
    {

        $promotions = (Category::with(["promotions", "promotions.company"])
            ->where("id", $id)
            ->first())
            ->promotions()
            ->orderBy('position', 'DESC')
            ->take(config("bot.results_per_page"))
            ->skip($page * config("bot.results_per_page"))
            ->get();

        $isEmpty = true;
        foreach ($promotions as $promo) {

            $company = Company::find($promo->company_id);

            $on_promo = $promo->onPromo($this->getChatId());
            $isActive = $promo->isActive();

            if (!$on_promo && $isActive && $company->is_active) {

                $isEmpty = false;

                $emptyHandler = is_null($promo->handler) || strlen(trim($promo->handler)) == 0;

                $keyboard = [
                    [
                        ["text" => __("messages.promo_btn_1"), 'callback_data' => $emptyHandler ? "/promotion " . $promo->id : $promo->handler . " " . $promo->id],
                        ["text" => __("messages.promo_btn_2"), 'switch_inline_query' => $promo->title]
                    ]
                ];

                $this->sendPhoto("*" . $promo->title . "*", $promo->promo_image_url, $keyboard);
            }
        }

        if ($isEmpty)
            $this->reply(__("messages.category_message_2"));

        $this->pagination("/category $id", $promotions, $page, __("messages.ask_action"));
    }

    public function getCompanyById($id, $page)
    {

        $company = Company::with(["promotions", "promotions.users"])
            ->where("id", $id)
            ->orderBy('position', 'DESC')
            ->first();

        if (!$company->is_active) {
            $this->reply(__("messages.company_message_2"));
            return;
        }

        $keyboard = [];

        if (strlen(trim($company->telegram_bot_url)) > 0)
            array_push($keyboard, [
                ['text' => __("messages.company_btn_3"), 'url' => $company->telegram_bot_url],
            ]);

        $message = sprintf("%s\n_%s_",
            $company->title,
            $company->description
        );

        $this->sendPhoto($message, $company->logo_url, $keyboard);

        $promotions = $company->promotions()
            ->orderBy('position', 'DESC')
            ->take(config("bot.results_per_page"))
            ->skip($page * config("bot.results_per_page"))
            ->get();

        $isEmpty = true;

        foreach ($promotions as $promo) {

            $on_promo = $promo->onPromo($this->getChatId());
            $isActive = $promo->isActive();


            if (!$on_promo && $isActive) {

                $isEmpty = false;

                $emptyHandler = is_null($promo->handler) || strlen(trim($promo->handler)) == 0;

                $keyboard = [
                    [
                        ['text' => __("messages.promo_btn_1"), 'callback_data' => $emptyHandler ? "/promotion " . $promo->id : $promo->handler . " " . $promo->id],
                        ["text" => __("messages.promo_btn_2"), 'switch_inline_query' => $promo->title]
                    ],
                ];

                $this->sendPhoto("*" . $promo->title . "*", $promo->promo_image_url, $keyboard);

            }
        }

        if ($isEmpty)
            $this->reply(__("messages.company_message_3"));

        $this->pagination("/company $id", $promotions, $page, __("messages.ask_action"));
    }

    public function doPromotionEditData()
    {
        /* $keyboard = [
             [
                 ["text"=>"Test EDITED promotion btn(not click)","callback_data"=>"/promo_edit_data_test"]
             ]
         ];

         $messageId = $this->bot->getMessage()->getPayload()["message_id"];

         $this->editMessageKeyboard($keyboard,$messageId);*/
    }

    public function getArticlesByPartId($partId, $page = 0)
    {
        $articles = Article::where("part", $partId)
            ->where("is_visible", 1)
            ->skip($page * config("bot.results_per_page"))
            ->take(config("bot.results_per_page"))
            ->orderBy('position', 'DESC')
            ->get();

        if (count($articles) > 0) {
            foreach ($articles as $article)
                $this->reply($article->url);
        } else
            $this->reply(__("messages.articles_message_1"));

        $this->pagination("/articles $partId", $articles, $page, __("messages.ask_action"));
    }

    public function getLotterySlot($slotId, $codeId)
    {
        $code = Promocode::find($codeId);
        if ($code == null) {
            $this->reply(__("messages.lottery_message_1"));
            return;
        }
        if ($code->prize_id != null) {
            $this->reply(__("messages.lottery_message_2"));
            return;
        }
        $prize = Prize::with(["company"])
                ->where("id", $slotId)
                ->first() ?? null;

        if ($prize == null) {
            $this->reply(__("messages.lottery_message_3"));
            return;
        }
        if ($prize->current_activation_count == $prize->summary_activation_count) {
            $this->reply(__("messages.lottery_message_4"));
            return;
        }

        $message = "*" . $prize->title . "*\n"
            . "_" . $prize->description . "_\n";
        $prize->current_activation_count++;
        $prize->updated_at = Carbon::now();
        $prize->save();

        $code->prize_id = $prize->id;
        $code->updated_at = Carbon::now();
        $code->save();

        $this->sendPhoto($message, $prize->image_url);

        $companyTitle = $prize->company->title;
        $message = sprintf(__("messages.lottery_message_5"),
            $companyTitle,
            $message,
            Carbon::now()
        );

        $this->sendPhotoToChanel($message, $prize->image_url);
    }

    public function getCashBackByCompanies()
    {
        $statistic_info = '';

        $cbis = CashBackInfo::with(["company"])->where("user_id", $this->getUser()->id)->get();

        if (count($cbis) == 0) {
            $this->reply("?????? ???? ???????????????????? CashBack!");
            return;
        }
        $hasCashBack = false;
        foreach ($cbis as $cbi) {
            if ($cbi->company->is_active) {
                $statistic_info .= sprintf("\xF0\x9F\x94\xB8*%s* => *%s ??????.* CashBack\n",
                    $cbi->company->title,
                    $cbi->value);
                $hasCashBack = true;
            }
        }

        if (!$hasCashBack) {
            $this->reply("?????? ???? ???????????????????? CashBack!");
            return;
        }

        $this->reply($statistic_info);
    }

    public function getActivityInformation()
    {
        $stat_types = [
            __("messages.statistic_message_1"),
            __("messages.statistic_message_2"),
            __("messages.statistic_message_3"),
            __("messages.statistic_message_4"),
            __("messages.statistic_message_5"),
            __("messages.statistic_message_6"),
            __("messages.statistic_message_7"),
            __("messages.statistic_message_8"),
            __("messages.statistic_message_9"),
            __("messages.statistic_message_10"),
            __("messages.statistic_message_11"),
        ];

        $stats = $this->getUser()->getStats();

        $message = "";

        foreach ($stats as $stat)
            $message .= sprintf($stat_types[$stat->stat_type->value], $stat->stat_value);

        $this->reply(count($stats) > 0 ? $message : __("messages.statistic_message_10"));
    }

    public function getFriendsMenu()
    {
        $ref = $this->getUser()->referrals_count;

        $network = $this->getUser()->network_friends_count + $ref;

        $network_tmp = $this->getUser()->current_network_level > 0 ? "???????? ???????????? *$network* ??????.!\n" : "";

        $message = sprintf(__("messages.friends_message_1"),
            $ref,
            $network_tmp);

        $keyboard = [

            [
                ["text" => "???????????????????? ????????????", "switch_inline_query" => ""]
            ],
        ];

        $tmp_id = (string)$this->getChatId();
        while (strlen($tmp_id) < 10)
            $tmp_id = "0" . $tmp_id;

        $code = base64_encode("001" . $tmp_id . "0000000000");

        $qr_url = env("QR_URL") . "https://t.me/" . env("APP_BOT_NAME") . "?start=$code";

        $this->sendPhoto(__("messages.friends_message_2"), $qr_url, $keyboard);
        $this->friendsMenu("$message");
    }

    /**
     * @deprecated ???????????????? ?? ???????????? ???? ??????????????????
     */
    public function getMyFriends()
    {

        $ref = $this->getUser()->referrals_count;

        $network = $this->getUser()->network_friends_count + $ref;

        $network_tmp = $this->getUser()->current_network_level > 0 ? "???????? ???????????? *$network* ??????.!\n" : "";

        $message = sprintf(__("messages.friends_message_1"),
            $ref,
            $network_tmp);

        $keyboard = [

            [
                ["text" => __("messages.friends_btn_1"), "switch_inline_query" => ""]
            ],
            [
                ["text" => __("messages.friends_btn_2"), "url" => env("APP_URL") . "#contact"]
            ],

        ];

        if ($ref > 0)
            array_push($keyboard, [
                ["text" => __("messages.friends_btn_3"), "callback_data" => "/friends 0"]
            ]);


        $tmp_id = (string)$this->getChatId();
        while (strlen($tmp_id) < 10)
            $tmp_id = "0" . $tmp_id;

        $code = base64_encode("001" . $tmp_id . "0000000000");

        $qr_url = env("QR_URL") . "https://t.me/" . env("APP_BOT_NAME") . "?start=$code";

        $this->sendPhoto(__("messages.friends_message_2"), $qr_url);
        $this->sendMessage("$message", $keyboard);

    }

    public function getPaymentMenu()
    {
        $cashback_company_summary = 0;
        $cbis = CashBackInfo::with(["company"])
            ->where("user_id", $this->getUser()->id)
            ->get();

        if (!empty($cbis))
            foreach ($cbis as $cbi)
                if ($cbi->company->is_active)
                    $cashback_company_summary += $cbi->value;

        $cashback = (env("INDIVIDUAL_CASHBACK_MODE") ?
            $cashback_company_summary :
            $this->getUser()->cashback_bonus_count);

        $summary = $this->getUser()->referral_bonus_count +
            $cashback +
            $this->getUser()->network_cashback_bonus_count;


        $tmp_network = $this->getUser()->network_friends_count >= config("bot.step_one_friends") ?
            "?????????????? ?????????? *" . $this->getUser()->network_cashback_bonus_count . "*\n" : '';

        $message = sprintf(__("messages.money_message_1"),
            $summary,
            $cashback,
            $tmp_network
        );

        $keyboard = [];

        $cashback_history = $this->getUser()->getCashBacksByPhone(0);

        if (count($cashback_history) > 0) {
            $tmp_money = 0;
            foreach ($cashback_history as $ch)
                if ($ch->activated == 0)
                    $tmp_money += round(intval($ch->money_in_check) * (Company::find($ch->company_id))->cashback / 100);

            if ($tmp_money > 0)
                array_push($keyboard, [
                    ["text" => sprintf(__("messages.money_message_2"), $tmp_money), "callback_data" => "/cashback_get"]
                ]);

        }

        //$this->sendMessage($message,$keyboard);

        $tmp_id = (string)$this->getChatId();
        while (strlen($tmp_id) < 10)
            $tmp_id = "0" . $tmp_id;

        $code = base64_encode("002" . $tmp_id . "0000000000");

        $qr_url = env("QR_URL") . "https://t.me/" . env("APP_BOT_NAME") . "?start=$code";

        $this->sendPhoto(__("messages.money_message_3"), $qr_url, $keyboard);
        $this->paymentMenu($message);
    }

    /**
     * @deprecated ???????????????? ?? ???????????? ???? ??????????????????
     */
    public function getMyMoney()
    {

        $summary = $this->getUser()->referral_bonus_count +
            $this->getUser()->cashback_bonus_count +
            $this->getUser()->network_cashback_bonus_count;

        $cashback = $this->getUser()->cashback_bonus_count;

        $tmp_network = $this->getUser()->network_friends_count >= config("bot.step_one_friends") ?
            "?????????????? ?????????? *" . $this->getUser()->network_cashback_bonus_count . "*\n" : '';

        $message = sprintf(__("messages.money_message_1"),
            $summary,
            $cashback,
            $tmp_network
        );


        $keyboard = [
            [
                ["text" => __("messages.money_btn_1"), "callback_data" => "/statistic"]
            ]
        ];


        $cashback_history = $this->getUser()->getCashBacksByPhone(0);

        if (count($cashback_history) > 0) {
            $tmp_money = 0;
            foreach ($cashback_history as $ch)
                if ($ch->activated == 0)
                    $tmp_money += round(intval($ch->money_in_check) * env("CAHSBAK_PROCENT") / 100);

            if ($tmp_money > 0)
                array_push($keyboard, [
                    ["text" => sprintf(__("messages.money_message_2"), $tmp_money), "callback_data" => "/cashback_get"]
                ]);

        }

        //$this->sendMessage($message,$keyboard);

        $tmp_id = (string)$this->getChatId();
        while (strlen($tmp_id) < 10)
            $tmp_id = "0" . $tmp_id;

        $code = base64_encode("002" . $tmp_id . "0000000000");

        $qr_url = env("QR_URL") . "https://t.me/" . env("APP_BOT_NAME") . "?start=$code";

        $this->sendPhoto(sprintf(__("messages.money_message_3"), $message), $qr_url, $keyboard);
    }

    public function getLotteryMenu()
    {

        $rules = Article::where("part", Parts::Lottery)
            ->where("is_visible", 1)
            ->orderBy("position", "DESC")
            ->first();

        $keyboard = [

        ];

        if ($rules != null)
            array_push($keyboard, [['text' => __("messages.lottery_menu_btn_2"), 'url' => $rules->url]]);

        $this->sendMessage(__("messages.lottery_menu_btn_3"), $keyboard);

        $this->lotteryMenu("_???????????????????? ?? ?????????????????? ?? ?????????????????? ???????????? ????????????!_");
    }

    public function getLotteryGiftCompanies($giftType)
    {

        Log::info($giftType);
        $companies = Company::with(["prizes"])->get();
        $hasPrizes = false;

        foreach ($companies as $company) {
            if (!$company->hasPrizes() || !$company->is_active)
                continue;

            $keyboard = [];

            $hasPrizes = true;

            array_push($keyboard, [[
                "text" => ($giftType == "gift" ?
                        "???????????????? ???????????????? " :
                        "?????????????????????? ?? ?????????????????? "
                    ) . "(*" . $company->lottery_start_price . "???*)",
                "callback_data" => "/pay_lottery $giftType " . $company->id
            ]]);
            $this->sendPhoto($company->logo_url, $keyboard);
        }

        if (!$hasPrizes)
            $this->reply(__("messages.lottery_message_7"));
    }

    public function payForLottery($giftType, $companyId)
    {

        $company = Company::with(["prizes"])->where("id", $companyId)->first() ?? null;

        $user = $this->getUser();

        $nedded = $company->lottery_start_price;

        if ($company == null) {
            $this->reply(__("messages.company_message_4"));
            return;
        }

        if (!$company->hasPrizes() || !$company->is_active) {
            $this->reply(__("messages.lottery_message_6"));
            return;
        }

        if ($user->referral_bonus_count + $user->cashback_bonus_count < intval($nedded)) {
            $this->reply(__("messages.payment_message_7"));
            return;
        }


        if ($user->referral_bonus_count <= intval($nedded)) {
            $module = intval($nedded) - $user->referral_bonus_count;
            $user->referral_bonus_count = 0;
            $user->cashback_bonus_count -= $module;
        } else
            $user->referral_bonus_count -= intval($nedded);

        $user->save();

        $skidobot = User::where("email", "skidobot@gmail.com")->first();

        RefferalsPaymentHistory::create([
            'user_id' => $user->id,
            'company_id' => $company->id,
            'employee_id' => $skidobot->id,
            'value' => intval($nedded),
        ]);

        $code = md5(env("APP_BOT_NAME") . (Carbon::now()));
        $promocode = Promocode::create([
            'code' => $code,
            'company_id' => $company->id,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        $this->reply("?? ?????? ???????? ?????????????? *" . intval($nedded) . "???* CashBack");

        if ($giftType == "gift") {
            $this->reply(sprintf(__("messages.promocode_message_2"), $code));
            return;
        }

        $prizes = json_decode(Prize::where("is_active", 1)
            ->where("company_id", $company->id)
            ->get(), true);

        $prizes = array_filter($prizes, function ($item) {
            return $item["summary_activation_count"] > $item["current_activation_count"];

        });

        if (count($prizes) == 0) {
            $this->reply(__("messages.ask_promocode_error_3"));
            return;
        }

        shuffle($prizes);
        $inline_keyboard = [];
        $tmp_menu = [];
        foreach ($prizes as $key => $prize) {
            $index = $key + 1;
            array_push($tmp_menu, ["text" => "\xF0\x9F\x8E\xB4", "callback_data" => "/check_lottery_slot " . $prize["id"] . " " . $promocode->id]);
            if ($index % 5 == 0 || count($prizes) == $index) {
                array_push($inline_keyboard, $tmp_menu);
                $tmp_menu = [];
            }
        }

        $this->sendMessage(__("messages.ask_promocode_success_1"), $inline_keyboard);
    }

    public function getLatestCashBack()
    {
        if ($this->getUser()->hasPhone())
            return;

        $cashback_history = $this->getUser()->getLatestCashBack();

        if (count($cashback_history) > 0) {
            foreach ($cashback_history as $ch) {
                $ch->activated = 1;
                $ch->user_id = $this->getUser()->id;
                $ch->save();

                event(new AddCashBackEvent(
                    $this->getUser()->id,
                    $ch->company_id,
                    $ch->money_in_check
                ));
            }
            $this->reply(__("messages.cashback_message_5"));
        }

    }

    public function getFallback()
    {
        $this->bot->loadDriver(TelegramInlineQueryDriver::DRIVER_NAME);

        $queryObject = json_decode($this->bot->getDriver()->getEvent());

        if ($queryObject) {

            $id = $queryObject->from->id;

            $query = $queryObject->query;


            $button_list = [];

            if (strlen(trim($query)) > 0) {
                $promotions =
                    Promotion::where("title", "like", "%$query%")
                        ->orWhere("description", "like", "%$query%")
                        ->take(5)
                        ->skip(0)
                        ->orderBy("id", "DESC")
                        ->get();

                foreach ($promotions as $promo) {
                    $isActive = $promo->isActive();
                    if ($isActive) {

                        $tmp_id = (string)$id;
                        while (strlen($tmp_id) < 10)
                            $tmp_id = "0" . $tmp_id;

                        $tmp_promo_id = (string)$promo->id;
                        while (strlen($tmp_promo_id) < 10)
                            $tmp_promo_id = "0" . $tmp_promo_id;

                        $code = base64_encode("001" . $tmp_id . $tmp_promo_id);
                        $url_link = "https://t.me/" . env("APP_BOT_NAME") . "?start=$code";

                        $tmp_button = [
                            'type' => 'article',
                            'id' => uniqid(),
                            'title' => $promo->title,
                            'input_message_content' => [
                                'message_text' => $promo->description . "\n" . $promo->promo_image_url,
                            ],
                            'reply_markup' => [
                                'inline_keyboard' => [
                                    [
                                        ['text' => "\xF0\x9F\x91\x89?????????????? ?? ??????????", "url" => "$url_link"],
                                    ],

                                ]
                            ],
                            'thumb_url' => $promo->promo_image_url,
                            'url' => env("APP_URL"),
                            'description' => $promo->description,
                            'hide_url' => true
                        ];

                        array_push($button_list, $tmp_button);


                    }
                }
            } else {

                $tmp_id = (string)$id;
                while (strlen($tmp_id) < 10)
                    $tmp_id = "0" . $tmp_id;

                $code = base64_encode("001" . $tmp_id . "0000000000");
                $url_link = "https://t.me/" . env("APP_BOT_NAME") . "?start=$code";

                //todo ??????-???? ?????????????? ?? ???????? ??????????????... ??????????????????
                $tmp_button = [
                    'type' => 'article',
                    'id' => uniqid(),
                    'title' => "???????? ?????????????????????? ????????????",
                    'input_message_content' => [
                        'message_text' => __("messages.repost_message_2"),
                    ],
                    'reply_markup' => [
                        'inline_keyboard' => [
                            [
                                ['text' => __("messages.repost_btn_1"), "url" => "$url_link"],
                            ],

                        ]
                    ],
                    'thumb_url' => "https://sun9-35.userapi.com/c205328/v205328682/56913/w8tBXIcG91E.jpg",
                    'url' => env("APP_URL"),
                    'description' => __("messages.repost_message_3"),
                    'hide_url' => true
                ];

                array_push($button_list, $tmp_button);
            }
            return $this->bot->sendRequest("answerInlineQuery",
                [
                    'cache_time' => 0,
                    "inline_query_id" => json_decode($this->bot->getEvent())->id,
                    "results" => json_encode($button_list)
                ]);
        }

    }

    public function getPromotionsByInstagram($page)
    {
        $user = $this->getUser();
        if (is_null($user->instagram)) {
            $keyboard = [
                [
                    ["text" => "??????????????????", "callback_data" => "/fill_insta_info"]
                ]
            ];
            $this->sendMessage("\xF0\x9F\x91\x89???? ???? ???????????? ?????????????????????? ?? ?????????? ???????? ???? ?????????????? ???????? ?????????????? ?? *Instagram*", $keyboard);
            return;
        }

        $instapromos = InstaPromotion::orderBy('position', 'DESC')
            ->where("is_active", true)
            ->take(config("bot.results_per_page"))
            ->skip($page * config("bot.results_per_page"))
            ->get();

        $userId = ($this->getUser())->id;

        $instapromos = $instapromos->filter(function ($item) use ($userId) {
            return is_null(UplodedPhoto::where("insta_promotions_id", $item->id)
                ->where("user_id", $userId)->first());
        });

        if (count($instapromos) == 0) {
            $this->reply("?????????? ?? ???????????? ?????????? ?????? ?????? ?????? ?????????? ??????????????????");
            return;
        }

        foreach ($instapromos as $promo) {
            $this->sendPhoto("*$promo->title*\n$promo->description", $promo->photo_url);
        }

        $this->pagination("/promo_by_category", $instapromos, $page, "???????????????? ????????????????");


    }

    public function receivesLocations($location)
    {
        $lat = $location->getLatitude();
        $lng = $location->getLongitude();

        $tmp_text = "*?????????????????? ?????????? (~" . env("GEO_QUEST_GLOBAL_DISTANCE") . "????):*\n";

        $findLocation = false;
        foreach (GeoPosition::getNearestQuestPoints($lat, $lng) as $pos) {
            $tmp_text .= "\xF0\x9F\x94\xB6 " . $pos->title . "\n";

            $findLocation = true;
            if ($pos->inRange($lat, $lng)) {
                $tmp_text .= "	\xF0\x9F\x94\xB7?????????? " . $pos->title . " ?????????????????? ?? " . $pos->radius . "???? ???? ??????!\n";
            }
        }

        if (!$findLocation) {
            $tmp_text .= "???? ?????????????? ???? ?????????? ?????????????????? ?? ?????? ??????????:(";
            $this->reply($tmp_text);
            return;
        }

        $this->reply($tmp_text);


        // Log::info("test location: $lat $lng");
    }

    public function uploadImages($images)
    {
        $user = $this->getUser();
        if (is_null($user->instagram)) {
            $keyboard = [
                [
                    ["text" => "??????????????????", "callback_data" => "/fill_insta_info"]
                ]
            ];
            $this->sendMessage("\xF0\x9F\x91\x89???? ???? ???????????? ?????????????????????? ?? ?????????? ???????? ???? ?????????????? ???????? ?????????????? ?? *Instagram*", $keyboard);
            return;
        }

        foreach ($images as $image) {

            $url = $image->getUrl(); // The direct url

            $canAddPhoto = count(UplodedPhoto::where("activated", false)
                    ->get()) < env("USERS_INSTA_PROMOS_LIMIT");

            if (!$canAddPhoto) {
                $this->reply("???? ???????????????? ???????????? (" . env("USERS_INSTA_PROMOS_LIMIT") . ") ???????????????? ???????????????????? ???? 1 ????????");
                continue;
            }

            UplodedPhoto::create([
                'url' => $url,
                'activated' => false,
                'user_id' => $this->getUser()->id,
                'insta_promotions_id' => null,
            ]);

            $this->reply("??????????????, ???????? ???????????????????? ?????????????? ?????????????????? ?? ?????????? ???????????????????? ??????????????????????????????!");

        }

    }

    public function donateCharity($charityId, $value)
    {
        $cbis = CashBackInfo::with(["company"])
            ->where("user_id", $this->getUser()->id)
            ->orderBy("value", "DESC")
            ->get();

        if (count($cbis) == 0) {
            $this->reply("?? ?????? ?????? ???????????????????????? CashBack!");
            return;
        }

        $sum = 0;
        foreach ($cbis as $cbi)
            $sum += $cbi->value;

        if ($sum < $value) {
            $this->reply("?? ?????? ???????????????????????? CashBack!");
            return;
        }

        $tmp = $value;
        foreach ($cbis as $key => $cbi) {
            $module = $tmp;
            if ($module == 0)
                break;


            if ($cbi->value < $module) {
                $tmp -= $cbi->value;

                CharityHistory::create([
                    'user_id' => $this->getUser()->id,
                    'charity_id' => $charityId,
                    'company_id' => $cbi->company_id,
                    'donated_money' => $cbi->value
                ]);

                $cbi->value = 0;
                $cbi->save();
                continue;
            }

            if ($cbi->value >= $module) {
                $cbi->value -= $module;
                $cbi->save();

                CharityHistory::create([
                    'user_id' => $this->getUser()->id,
                    'charity_id' => $charityId,
                    'company_id' => $cbi->company_id,
                    'donated_money' => $module
                ]);

                break;
            }
        }

        $charity = Charity::find($charityId);

        event(new AchievementEvent(
            AchievementTriggers::MaxCashCharityDonated,
            $value,
            $this->getUser()
        ));

        $this->reply("??????????????! ???? ???????????????????????? *$value ???* ???? ?????????? *" . $charity->title . "*");

    }

    public function getCharity($charityId)
    {
        $charity = Charity::where("id", $charityId)
            ->where("is_active", true)
            ->first();

        if (is_null($charity)) {
            $this->reply("_???????????? ?????????????????????????????????? ?????????? ?????????????????????? ?????? ?? ???????????? ???????????? ????????????????????!_");
            return;
        }

        $cbis = CashBackInfo::with(["company"])
            ->where("user_id", $this->getUser()->id)
            ->where("value", ">", 0)
            ->get();

        if (count($cbis) == 0) {
            $this->reply("*?? ?????? ?????? ?????????????????? ?????????????? ?????? ??????????????????????????!*");
            return;
        }

        $sum = 0;
        foreach ($cbis as $cbi) {
            $sum += $cbi->value;
        }

        $keyboard = [
            [
                ["text" => "100???", "callback_data" => "/donate " . $charity->id . " 100"],
                ["text" => "250???", "callback_data" => "/donate " . $charity->id . " 250"],
                ["text" => "500???", "callback_data" => "/donate " . $charity->id . " 500"],
                ["text" => "1000???", "callback_data" => "/donate " . $charity->id . " 1000"],
            ],
        ];

        $this->sendPhoto("*" . $charity->title . "*\n"
            . $charity->description . "\n"
            . "???????????????? ?????? ???????????????? *$sum* ???\n"
            . "\n*???????????????? ?????????? ??????????????????????????*:", $charity->image_url, $keyboard);

    }

    public function getCharityList($page)
    {
        $charities = Charity::where("is_active", 1)
            ->skip($page * config("bot.results_per_page"))
            ->take(config("bot.results_per_page"))
            ->orderBy('position', 'DESC')
            ->get();

        $charityInfo = Article::where("part", Parts::Charity)
            ->where("is_visible", 1)
            ->orderBy("position", "DESC")
            ->first();


        if (!is_null($charityInfo))
            $this->sendMessage("*?????????? ???????????????????????????? ?????????????????????????? ???????????????????? ?????????????????? ??????????????!*", [
                [
                    ['text' => __("messages.charity_menu_btn_1"), 'url' => $charityInfo->url]
                ]
            ]);


        if (count($charities) == 0) {
            $this->reply("?? ?????????????????? ???? ???????????? ???????????? ?????? ?????????????????? ?????????????????????????????????? ??????????!");
            return;
        }

        foreach ($charities as $charity) {
            $this->sendPhoto("*" . $charity->title . "*", $charity->image_url, [
                [
                    ["text" => "??????????????????", "callback_data" => "/get_charity_item " . $charity->id]
                ]
            ]);
        }
        $this->pagination("/charity", $charities, $page, __("messages.ask_action"));
    }

    public function getGeoQuestList($page)
    {
        $this->questMenu("??????-????????????");

        Log::info("Test 1");

        $quests = GeoQuest::where("is_active", 1)
            ->skip($page * config("bot.results_per_page"))
            ->take(config("bot.results_per_page"))
            ->orderBy('position', 'DESC')
            ->get();

        $questInfo = Article::where("part", Parts::GeoQuest)
            ->where("is_visible", 1)
            ->orderBy("position", "DESC")
            ->first();


        if (!is_null($questInfo))
            $this->sendMessage("*?????????? ?????????????? ?????????????? ?? ?????????????? ???????????????????? ?????????????????? ??????????????!*", [
                [
                    ['text' => __("messages.charity_menu_btn_1"), 'url' => $questInfo->url]
                ]
            ]);


        if (count($quests) == 0) {
            $this->reply("?? ?????????????????? ???? ???????????? ???????????? ?????? ?????????????????? ??????-??????????????!");
            return;
        }

        $hasActiveQuests = false;
        foreach ($quests as $quest) {

            if (count($quest->quest_points_list) > 0) {
                $hasActiveQuests = true;
                $this->sendPhoto("*" . $quest->title . "*\n_" . $quest->description . "_", $quest->image_url, [
                    [
                        ["text" => "\xF0\x9F\x93\x8D???????????? ?????????????????? ?????????? (" . count($quest->quest_points_list) . ")", "callback_data" => "/geo_positions_list " . $quest->id]
                    ]
                ]);
            }
        }

        if (!$hasActiveQuests) {
            $this->reply("?????? ???? ???????????? ?????????????????? ???????????? ?????? ?????? ???????????? ??????????????????!");
            return;
        }
        $this->pagination("/geo_quest", $quests, $page, __("messages.ask_action"));
    }

    public function getGeoPositionsList($questId)
    {
        $quest = GeoQuest::find($questId);

        $this->sendMessage("?????????? ?????????? ?????????????????????? ???? ???????? ??????????????????????! ?????????? ?????????? - *" . count($quest->quest_points_list) . "*.", [
            [
                ['text' => "\xF0\x9F\x93\x8B?????? ???????????????????? ??????????????????????", 'callback_data' => "/geo_quest_completion " . $quest->id]
            ]
        ]);


        // TODO: Implement getGeoPositionsList() method.
        $this->reply("???????????? ??????-?????????????? ?? ???????????? ????????????????????");
    }

    public function getGeoPositionInfo($positionId)
    {
        // TODO: Implement getGeoPositionInfo() method.
        $this->reply("???????????? ??????-?????????????? ?? ???????????? ????????????????????");
    }

    public function getGeoQuestCompletion($questId)
    {
        $this->reply("???????????? ??????-?????????????? ?? ???????????? ????????????????????");
    }

    public function getEventQRCode($eventId){

        $event = Event::find($eventId);

        if (is_null($event))
        {
            $this->reply("?? ?????????????????? ?????????? ???? ??????????????:(");
            return;
        }

        $tmp_id = (string)$this->getChatId();

        while (strlen($tmp_id) < 10)
            $tmp_id = "0" . $tmp_id;

        $tmp_event_id = (string)$eventId;

        while (strlen($tmp_event_id) < 10)
            $tmp_event_id = "0" . $tmp_event_id;



        $code = base64_encode("200" . $tmp_id . $tmp_event_id);

        $qr_url = env("QR_URL") . "https://t.me/" . env("APP_BOT_NAME") . "?start=$code";

        $this->sendPhoto("*?????? ?????????????????? ?????????? ???????????????? QR-?????? ????????????????????!*", $qr_url);
    }
}
