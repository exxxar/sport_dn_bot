<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Sushi</title>

    <link href="https://fonts.googleapis.com/css?family=Russo+One&display=swap&subset=cyrillic" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.11.2/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="http://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css"/>
    <link rel="stylesheet" href="css/style.css">
</head>

<style>

</style>
<body>

<header>

    <nav>

        <ul>
            <li><a href="">Главная</a></li>
            <li><a href="">Наше меню</a></li>
            <li><a href=""><img src="logo.jpg" alt=""></a></li>
            <li><a href="">Собери рол</a></li>
            <li><a href="">Доставка</a></li>
        </ul>
    </nav>
</header>

<div class="full-site" id="app">
    <div>
        <section class="s1">
            <video autoplay muted loop id="myVideo">
                <source src="z1.mp4" type="video/mp4">
                Your browser does not support HTML5 video.
            </video>

            <div class="center-content">
                <div class="content-menu">
                    <nav>
                        <ul>
                            <li><a href="">Роллы</a></li>
                            <li><a href="">Хардроллы</a></li>
                            <li><a href="">Суши</a></li>
                            <li><a href="">Салаты</a></li>
                            <li><a href="">Десерт</a></li>
                        </ul>
                    </nav>
                </div>

                <div class="text-block">
                    <h1>!МЫ РАБОТАЕМ!</h1>

                    <h2>Откройте новые грани кулинарии вместе с нами</h2>

                    <p>Мы молодое звено в области японской кухни. Наши цели и задачи построены исключительно на
                        удовлетворение потребностей, интересов и желаний клиента.
                        Для этого мы даем свободу выбора. Каждый может найти здесь для себя что-то свое.
                        Кто-то предпочитает неизменную классику и довольствуется уже закрепившимися вкусами и образами,
                        а кто-то напротив, желает экспериментировать, удивляться, пробовать и открывать что-то новое или
                        просто по индивидуальным причинам хочет видеть в
                        своем блюде те или иные ингредиенты. Мы не собираемся заниматься самохвальством, наша оценка и
                        репутация это ваше мнение и отзывы.
                    </p>

                    <p>
                        Выбор — привилегия свободного человека. Ваш выбор в ваших руках. Приятного аппетита!
                    </p>
                </div>
            </div>


            <div class="content">
                <div class="work-time">
                    <p><i class="far fa-clock"></i>Время доставки</p>
                    <p>с <span>10:00</span> до <span>22:00</span></p>
                </div>
                <div class="contacts">
                    <ul>
                        <li><a href=""><i class="fas fa-mobile-alt"></i>+38 (071) 333-46-26</a></li>
                        <li><a href=""><i class="fas fa-mobile-alt"></i>+38 (099) 361-28-78</a></li>
                        <li><a href=""><i class="fab fa-viber"></i>+38 (071) 333-46-26</a></li>
                        <li><a href=""><i class="far fa-envelope-open"></i>info@rollx-don.ru</a></li>

                    </ul>
                </div>
                <div class="buy">
                    <button>Быстрый заказ</button>
                </div>
            </div>

        </section>
    </div>
    <div>
        <section class="s2">
            <div class="scroll-area">
                <form class="calc">
                    <div class="left">
                        <h1>Соберите свой ролл</h1>
                        <p>Соберите свой ролл — вы можете самостоятельно сконструировать свой ролл исходя из своих
                            вкусов и желаний!Создавайте,творите и экспериментируйте вместе с нами.</p>
                        <p>
                            В основу ролла входит пол листа нори и рис 50р. (130 гр.)
                        </p>
                        <p>
                            Выберите поверхность ролла,начинку и форму
                        </p>

                        <div class="form-group">
                            <label for=""> Верхнее покрытие ролла:</label>
                            <select name="" id="">
                                <option value="">Лосось</option>
                                <option value="">Угорь</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <input type="text">
                        </div>

                        <h3>Начинка внутри ролла</h3>

                        <table>
                            <tbody>
                            <tr>
                                <td>
                                    <label class="container">Лосось
                                        <input type="checkbox" checked="checked">
                                        <span class="checkmark"></span>
                                    </label>
                                </td>
                                <td>
                                    <label class="container">Сыр тостерный
                                        <input type="checkbox" checked="checked">
                                        <span class="checkmark"></span>
                                    </label>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label class="container">Тунец
                                        <input type="checkbox" checked="checked">
                                        <span class="checkmark"></span>
                                    </label>
                                </td>
                                <td>
                                    <label class="container">Спайси соус (острый)
                                        <input type="checkbox" checked="checked">
                                        <span class="checkmark"></span>
                                    </label>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label class="container">Угорь
                                        <input type="checkbox" checked="checked">
                                        <span class="checkmark"></span>
                                    </label>
                                </td>
                                <td>
                                    <label class="container">Майонез
                                        <input type="checkbox" checked="checked">
                                        <span class="checkmark"></span>
                                    </label>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label class="container">Креветка ХОТ (жаренная)
                                        <input type="checkbox" checked="checked">
                                        <span class="checkmark"></span>
                                    </label>
                                </td>
                                <td>
                                    <label class="container">Томаго (омлет)
                                        <input type="checkbox" checked="checked">
                                        <span class="checkmark"></span>
                                    </label>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label class="container">Креветки
                                        <input type="checkbox" checked="checked">
                                        <span class="checkmark"></span>
                                    </label>
                                </td>
                                <td>
                                    <label class="container">Огурец
                                        <input type="checkbox" checked="checked">
                                        <span class="checkmark"></span>
                                    </label>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label class="container">Мидии
                                        <input type="checkbox" checked="checked">
                                        <span class="checkmark"></span>
                                    </label>
                                </td>
                                <td>
                                    <label class="container">Чука
                                        <input type="checkbox" checked="checked">
                                        <span class="checkmark"></span>
                                    </label>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label class="container">Снежный краб
                                        <input type="checkbox" checked="checked">
                                        <span class="checkmark"></span>
                                    </label>
                                </td>
                                <td>
                                    <label class="container">Помидор
                                        <input type="checkbox" checked="checked">
                                        <span class="checkmark"></span>
                                    </label>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label class="container">Лосось Жареный
                                        <input type="checkbox" checked="checked">
                                        <span class="checkmark"></span>
                                    </label>
                                </td>
                                <td>
                                    <label class="container">Сладкий Перец
                                        <input type="checkbox" checked="checked">
                                        <span class="checkmark"></span>
                                    </label>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label class="container">Курица (жареная)
                                        <input type="checkbox" checked="checked">
                                        <span class="checkmark"></span>
                                    </label>
                                </td>
                                <td>
                                    <label class="container">Лист салата
                                        <input type="checkbox" checked="checked">
                                        <span class="checkmark"></span>
                                    </label>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label class="container">Окунь
                                        <input type="checkbox" checked="checked">
                                        <span class="checkmark"></span>
                                    </label>
                                </td>
                                <td>
                                    <label class="container">Лук зеленый
                                        <input type="checkbox" checked="checked">
                                        <span class="checkmark"></span>
                                    </label>
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <label class="container">Икра тобико
                                        <input type="checkbox" checked="checked">
                                        <span class="checkmark"></span>
                                    </label>
                                </td>
                                <td>
                                    <label class="container">Дайкон (редька маринованная)
                                        <input type="checkbox" checked="checked">
                                        <span class="checkmark"></span>
                                    </label>
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <label class="container">Сливочный сыр
                                        <input type="checkbox" checked="checked">
                                        <span class="checkmark"></span>
                                    </label>
                                </td>
                                <td>

                                </td>
                            </tr>
                            </tbody>
                        </table>


                    </div>
                    <div class="right">
                        <h3>Выбери форму ролла</h3>

                        <table>
                            <tbody>
                            <tr>
                                <td>
                                    <p>Квадратная<br>форма</p>
                                    <label>
                                        <input type="radio" name="test" value="big">
                                        <img src="square.jpg">
                                    </label>
                                </td>
                                <td>
                                    <p>Круглая<br>форма</p>
                                    <label>
                                        <input type="radio" name="test" value="big">
                                        <img src="circle.jpg">
                                    </label>
                                </td>
                                <td>
                                    <p>Треугольная<br>форма</p>
                                    <label>
                                        <input type="radio" name="test" value="big">
                                        <img src="triangle.jpg">
                                    </label>
                                </td>
                            </tr>

                            </tbody>
                        </table>

                        <h3>Цена ролла</h3>
                        <h2>190 ₽</h2>

                        <div class="counter">
                            <button>+</button>
                            <input type="number" value="1" min="1">
                            <button>-</button>
                        </div>
                        <div class="form-group">
                            <button class="send-btn">В корзину</button>
                        </div>
                        <div class="labels">
                            <h5>Категория: <a href="">Собрать ролл</a></h5>
                            <h5>Метки: <a href="">снежный краб</a>, <a href="">огурец</a>, <a href="">авокадо</a>, <a
                                        href="">майонез</a>,
                                <a href="">Лосось</a>, <a href="">Тунец</a>, <a href="">Угорь</a>, <a
                                        href="">Креветка</a>, <a href="">Мидии</a>,
                                <a href="">Икра Тобико</a>, <a href="">Красная Икра</a>, <a href="">Спайси</a>, <a
                                        href="">сливочный сыр</a>,
                                <a href="">твердый сыр</a>, <a href="">тостерный сыр</a>, <a href="">чука</a>, <a
                                        href="">креветка жареная</a>,
                                <a href="">лист салата</a>, <a href="">жареный лосось</a>, <a href="">стружка тунца</a>,
                                <a href="">кунжут</a>, <a href="">зеленый лук</a>,
                                <a href="">укроп</a>, <a href="">помидор</a>, <a href="">жареная курица</a>, <a href="">томаго</a>,
                                <a href="">окунь морской</a>, <a href="">сладкий перец</a>
                            </h5>
                        </div>

                    </div>
                </form>
            </div>
        </section>
    </div>
    <div>

        <section class="s3">
            <ul class="lottery">
                <li class="lottery-item-wrapper">
                    <div class="lottery-item">
                        <img src="https://sun9-35.userapi.com/c858036/v858036636/102217/wYzvw31u87k.jpg"
                             alt="">
                    </div>
                </li>

                <li class="lottery-item-wrapper">
                    <div class="lottery-item">
                        <img src="https://sun9-35.userapi.com/c858036/v858036636/102217/wYzvw31u87k.jpg"
                             alt="">
                    </div>
                </li>

                <li class="lottery-item-wrapper">
                    <div class="lottery-item">
                        <img src="https://sun9-35.userapi.com/c858036/v858036636/102217/wYzvw31u87k.jpg"
                             alt="">
                    </div>
                </li>

                <li class="lottery-item-wrapper">
                    <div class="lottery-item">
                        <img src="https://sun9-35.userapi.com/c858036/v858036636/102217/wYzvw31u87k.jpg"
                             alt="">
                    </div>
                </li>


                <li class="lottery-item-wrapper">
                    <div class="lottery-item">
                        <img src="https://sun9-35.userapi.com/c858036/v858036636/102217/wYzvw31u87k.jpg"
                             alt="">
                    </div>
                </li>

                <li class="lottery-item-wrapper">
                    <div class="lottery-item">
                        <img src="https://sun9-35.userapi.com/c858036/v858036636/102217/wYzvw31u87k.jpg"
                             alt="">
                    </div>
                </li>

                <li class="lottery-item-wrapper">
                    <div class="lottery-item">
                        <img src="https://sun9-35.userapi.com/c858036/v858036636/102217/wYzvw31u87k.jpg"
                             alt="">
                    </div>
                </li>


                <li class="lottery-item-wrapper">
                    <div class="lottery-item">
                        <img src="https://sun9-35.userapi.com/c858036/v858036636/102217/wYzvw31u87k.jpg"
                             alt="">
                    </div>
                </li>

                <li class="lottery-item-wrapper">
                    <div class="lottery-item">
                        <img src="https://sun9-35.userapi.com/c858036/v858036636/102217/wYzvw31u87k.jpg"
                             alt="">
                    </div>
                </li>

                <li class="lottery-item-wrapper">
                    <div class="lottery-item">
                        <img src="https://sun9-35.userapi.com/c858036/v858036636/102217/wYzvw31u87k.jpg"
                             alt="">
                    </div>
                </li>

                <li class="lottery-item-wrapper">
                    <div class="lottery-item">
                        <img src="https://sun9-35.userapi.com/c858036/v858036636/102217/wYzvw31u87k.jpg"
                             alt="">
                    </div>
                </li>

                <li class="lottery-item-wrapper">
                    <div class="lottery-item">
                        <img src="https://sun9-35.userapi.com/c858036/v858036636/102217/wYzvw31u87k.jpg"
                             alt="">
                    </div>
                </li>

                <li class="lottery-item-wrapper">
                    <div class="lottery-item">
                        <img src="https://sun9-35.userapi.com/c858036/v858036636/102217/wYzvw31u87k.jpg"
                             alt="">
                    </div>
                </li>


                <li class="lottery-item-wrapper">
                    <div class="lottery-item">
                        <img src="https://sun9-35.userapi.com/c858036/v858036636/102217/wYzvw31u87k.jpg"
                             alt="">
                    </div>
                </li>


                <li class="lottery-item-wrapper">
                    <div class="lottery-item">
                        <img src="https://sun9-35.userapi.com/c858036/v858036636/102217/wYzvw31u87k.jpg"
                             alt="">
                    </div>
                </li>


                <li class="lottery-item-wrapper">
                    <div class="lottery-item">
                        <img src="https://sun9-35.userapi.com/c858036/v858036636/102217/wYzvw31u87k.jpg"
                             alt="">
                    </div>
                </li>


                <li class="lottery-item-wrapper">
                    <div class="lottery-item">
                        <img src="https://sun9-35.userapi.com/c858036/v858036636/102217/wYzvw31u87k.jpg"
                             alt="">
                    </div>
                </li>


            </ul>
        </section>
    </div>

</div>

<a href="" class="basket" data-count="10"><i class="fas fa-shopping-basket"></i>
    <p>Оформить заказ</p>
</a>

<div class="preloader hidden">
    <div class="center">
        <img src="logo.jpg" alt="this slowpoke moves" width="250" alt="404 image"/>
    </div>
</div>

<script src="{{asset('js/app.js')}}"></script>
<script src="https://code.jquery.com/jquery-2.2.4.min.js"></script>
<script type="text/javascript" src="http://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>
<script>
    var video = document.getElementById("myVideo");
    var btn = document.getElementById("myBtn");

    var resizeTimeout = null;

    function myFunction() {
        if (video.paused) {
            video.play();
            btn.innerHTML = "Pause";
        } else {
            video.pause();
            btn.innerHTML = "Play";
        }
    }

    $(document).ready(function () {
        $('.same-products').slick();
        $('.full-site').slick({
            vertical: true,
            verticalSwiping: true,
            arrows: false,
            //variableWidth: true,
            //rtl: true,

        });


        $(window).resize(function () {
            clearTimeout(resizeTimeout);
            $(".preloader").removeClass("hidden");
            resizeTimeout = setTimeout(() => {
                $(".preloader").addClass("hidden");
            }, 400);
        });
    });
</script>
</body>
</html>
