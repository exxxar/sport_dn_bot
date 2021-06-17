<!DOCTYPE html>
<html lang="en">

<head>
    <!-- all meta -->
    <meta charset="UTF-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Page Title -->
    <title>Сервис доставки суши ISUSHI & PIZZA</title>
    <!-- Favicon Icon -->
    <link rel="shortcut icon" href="{{asset('assets/images/logo_dark.png')}}" type="image/png">
    <!-- All css here -->
    <!-- Bootstrap css -->
    <link rel="stylesheet" href="{{asset('assets/plugin/bootstrap/css/bootstrap.min.css')}}">
    <!-- fontaweosme css -->
    <link rel="stylesheet" href="{{asset('assets/fonts/fontawesome/css/all.css')}}">
    <!-- flaticon css -->
    <link rel="stylesheet" href="{{asset('assets/fonts/flaticon/flaticon.css')}}">
    <!-- slick slider css -->
    <link rel="stylesheet" href="{{asset('assets/plugin/slick/slick.css')}}">
    <link rel="stylesheet" href="{{asset('assets/plugin/slick/slick-theme.css')}}">
    <!-- sidebarmenu css -->
    <link rel="stylesheet" href="{{asset('assets/plugin/sidebar-menu/sidebar-menu.css')}}">
    <!-- jquery_ui css -->
    <link rel="stylesheet" href="{{asset('assets/plugin/jquery_ui/jquery-ui.min.css')}}">
    <!-- magnific css -->
    <link rel="stylesheet" href="{{asset('assets/plugin/magnific/magnific-popup.css')}}">
    <!-- magnific css -->
{{--    <link rel="stylesheet" href="{{asset('assets/plugin/nicenumber/jquery.nice-number.css')}}">--}}
    <!-- magnific css -->
    <link rel="stylesheet" href="{{asset('assets/plugin/niceselect/nice-select.css')}}">
    <!-- animate css -->
    <link rel="stylesheet" href="{{asset('assets/css/animate.css')}}?ver={{config("app.version")}}">
    <!-- Style css -->
    <link rel="stylesheet" href="{{asset('assets/css/style.css')}}?ver={{config("app.version")}}">
    <!-- Responsive css -->
    <link rel="stylesheet" href="{{asset('assets/css/responsive.css')}}?ver={{config("app.version")}}">

    <!— Google Tag Manager —>
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
                new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
            j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
            'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.paren..;
        })(window,document,'script','dataLayer','GTM-NPLGVC6');</script>
    <!— End Google Tag Manager —>



</head>

<body>
<!— Google Tag Manager (noscript) —>
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-NPLGVC6"
                  height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!— End Google Tag Manager (noscript) —>
<div id="app">
    <!-- Start preloader area -->
    <div class="preloader_area">
        <div class="spinner">
            <div class="line1"></div>
            <div class="line2"></div>
            <div class="line3"></div>
            <div class="line4"></div>
            <div class="line5"></div>
            <div class="line6"></div>
            <div class="line7"></div>
        </div>
    </div>


@yield('content')


<!-- callbackModal -->
    <div class="modal fade" id="cartModal" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel"><span>Корзина заказов</span></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <cart></cart>
                </div>
            </div>
        </div>
    </div>

<!-- End preloader area -->

    <div class="modal fade" id="deliveryModal">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel"><span>Доставка</span></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Не знаете где быстро заказать аппетитные и качественные роллы?!</p>

                    <p> Стоимость наших блюд вас приятно удивит и позволит насладится изящным вкусом настоящей японской
                        кухни!</p>
                    <p>Мы любим своих клиентов и создаём исключительно наилучшую продукцию для вас!</p>

                    <p>Скидка именинникам 10%!!</p>
                    <p>День до,в день рождения и день после (3 дня)</p>
                    <p>Работаем 10:00 - 21:00</p>

                    <p>↓ Связаться с нами ↓</p>
                    <p><a href="tel:+380713830741">+38(071) 383-07-41</a></p>

                    <p> Подписывайтесь на наш телеграмм бот <a href="">👉 t.me/isushibot</a></p>
                </div>
            </div>
        </div>
    </div>
    <!-- callbackModal -->
    <div class="modal fade" id="callbackModal">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel"><span>Мы тебе перезвоним</span></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="reservation_form">
                                <callback-form></callback-form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <notifications group="info" :position="'top left'" :max="5"/>
</div>
<!-- scroll_top -->
<a id="scroll_top"><i class="fas fa-angle-up"></i></a>
<!-- jquery  -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js" defer></script>
<!-- bootstrap js -->
<script src="{{asset('assets/plugin/bootstrap/js/bootstrap.min.js')}}" defer></script>
<!-- Poper js -->
<script src="{{asset('assets/plugin/bootstrap/js/popper.min.js')}}" defer></script>
<!-- slick slider js -->
<script src="{{asset('assets/plugin/slick/slick.min.js')}}" defer></script>
<!-- magnific popup js -->
<script src="{{asset('assets/plugin/magnific/jquery.magnific-popup.min.js')}}" defer></script>
<!-- isotope js -->
<script src="{{asset('assets/plugin/isotope/isotope.min.js')}}" defer></script>
<!-- imagesloaded js -->
<script src="{{asset('assets/plugin/imagesloaded/imagesloaded.min.js')}}" defer></script>
<!-- couterup js -->
<script src="{{asset('assets/plugin/counterup/jquery.counterup.min.js')}}" defer></script>
<script src="{{asset('assets/plugin/counterup/jquery.waypoints.min.js')}}" defer></script>
<!-- countdown js -->
<script src="{{asset('assets/plugin/countdown/jquery.countdown.min.js')}}" defer></script>
<!-- jquery_ui js -->
<script src="{{asset('assets/plugin/jquery_ui/jquery-ui.min.js')}}" defer></script>
<!-- nice number js -->
<script src="{{asset('assets/plugin/niceselect/jquery.nice-select.min.js')}}" defer></script>
<!-- nice number js -->
{{--<script src="{{asset('assets/plugin/nicenumber/jquery.nice-number.min.js')}}"></script>--}}
<!-- sidebarmenu js -->
<script src="{{asset('assets/plugin/sidebar-menu/sidebar-menu.js')}}" defer></script>
<!-- jquery_ui js -->
<script src="{{asset('assets/js/wow.min.js')}}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js" defer></script>
<script src="{{asset('js/app.js')}}" defer></script>
<!-- main js -->
<script src="{{asset('assets/js/main.js')}}" defer></script>

</body>

</html>
