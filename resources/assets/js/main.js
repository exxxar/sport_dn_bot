(function ($) {
    'use strict';

    /*---------------------------------
      Preloader JS
  -----------------------------------*/
    var prealoaderOption = $(window);
    prealoaderOption.on("load", function () {
        var preloader = jQuery('.spinner');
        var preloaderArea = jQuery('.preloader_area');
        preloader.fadeOut();
        preloaderArea.delay(350).fadeOut('slow');
    });
    /*---------------------------------
        Preloader JS
    -----------------------------------*/

    /*---------------------------------  
        sticky header JS
    -----------------------------------*/
    $(window).on('scroll', function () {
        var scroll = $(window).scrollTop();
        if (scroll < 100) {
            $(".header_area").removeClass("sticky");
        } else {
            $(".header_area").addClass("sticky");
        }
    });
    /*---------------------------------  
        Search JS
    -----------------------------------*/
    $(".search_btn").on('click', function (e) {
        e.preventDefault();
        $(".search_wrapper").addClass("active");
    });
    $(".close_link").on('click', function (e) {
        e.preventDefault();
        $(".search_wrapper").removeClass("active");
    });
    /*---------------------
        Sidebar-menu js
    -----------------------*/
    $(".menu_icon").on('click', function (e) {
        e.preventDefault();
        $(".menu_icon").toggleClass("active");
    });
    $(".menu_icon").on('click', function (e) {
        e.preventDefault();
        $(".sidenav_menu").toggleClass("active");
    });
    $.sidebarMenu($('.sidebar-menu'))
    /*---------------------- 
        Scroll top js
    ------------------------*/
    $(window).on('scroll', function () {
        if ($(this).scrollTop() > 100) {
            $('#scroll_top').fadeIn();
        } else {
            $('#scroll_top').fadeOut();
        }
    });
    $('#scroll_top').on('click', function () {
        $("html, body").animate({
            scrollTop: 0
        }, 600);
        return false;
    });
    /*---------------------- 
        slider js
    ------------------------*/
    $('.welcome_slider').slick({
        dots: false,
        infinite: true,
        arrows: true,
        autoplay: false,
        speed: 400,
        slidesToShow: 1,
        slidesToScroll: 1
    });
    $('.welcome_slider_2').slick({
        dots: true,
        vertical: true,
        infinite: true,
        arrows: false,
        autoplay: false,
        speed: 400,
        slidesToShow: 1,
        slidesToScroll: 1
    });
    $('.chef_slide_1').slick({
        dots: false,
        infinite: true,
        arrows: true,
        autoplay: false,
        speed: 400,
        slidesToShow: 3,
        slidesToScroll: 1,
        responsive: [{
            breakpoint: 1024,
            settings: {
                slidesToShow: 2,
                slidesToScroll: 2
            }
        },
            {
                breakpoint: 600,
                settings: {
                    slidesToShow: 1,
                }
            },
            {
                breakpoint: 480,
                settings: {
                    slidesToShow: 1,
                }
            }
        ]
    });
    $('.testimonial_slider_1').slick({
        dots: true,
        infinite: true,
        arrows: false,
        autoplay: false,
        speed: 400,
        slidesToShow: 1,
        slidesToScroll: 1
    });
    $('.food_slider_1').slick({
        dots: true,
        infinite: true,
        arrows: false,
        autoplay: false,
        speed: 400,
        slidesToShow: 3,
        slidesToScroll: 1,
        responsive: [{
            breakpoint: 768,
            settings: {
                arrows: false,
                slidesToShow: 1
            }
        },
            {
                breakpoint: 480,
                settings: {
                    arrows: false,
                    slidesToShow: 1
                }
            }
        ]
    });
    $('.dishes_slider_1').slick({
        dots: true,
        infinite: true,
        arrows: false,
        autoplay: false,
        speed: 400,
        slidesToShow: 4,
        slidesToScroll: 3,
        responsive: [{
            breakpoint: 992,
            settings: {
                arrows: false,
                slidesToShow: 2
            }
        },
            {
                breakpoint: 768,
                settings: {
                    arrows: false,
                    slidesToShow: 2
                }
            },
            {
                breakpoint: 480,
                settings: {
                    arrows: false,
                    slidesToShow: 1
                }
            }
        ]
    });
    $('.popular_item_slide').slick({
        dots: false,
        infinite: true,
        arrows: true,
        autoplay: false,
        speed: 400,
        slidesToShow: 4,
        slidesToScroll: 4,
        responsive: [{
            breakpoint: 992,
            settings: {
                arrows: false,
                slidesToShow: 2
            }
        },
            {
                breakpoint: 768,
                settings: {
                    arrows: false,
                    slidesToShow: 2
                }
            },
            {
                breakpoint: 480,
                settings: {
                    arrows: false,
                    slidesToShow: 1
                }
            }
        ]
    });
    $('.gallery_big').slick({
        dots: true,
        infinite: true,
        arrows: false,
        autoplay: false,
        speed: 400,
        slidesToShow: 1,
        slidesToScroll: 1,
        asNavFor: '.gallery_small'
    });
    $('.gallery_small').slick({
        infinite: true,
        slidesToShow: 4,
        slidesToScroll: 1,
        asNavFor: '.gallery_big',
        dots: false,
        arrows: false,
        focusOnSelect: true
    });
    $('.sponser_slider').slick({
        autoplay: true,
        infinite: true,
        slidesToShow: 5,
        slidesToScroll: 2,
        speed: 400,
        dots: false,
        arrows: false,
        responsive: [{
            breakpoint: 992,
            settings: {
                arrows: false,
                slidesToShow: 3
            }
        },
            {
                breakpoint: 768,
                settings: {
                    arrows: false,
                    slidesToShow: 2
                }
            },
            {
                breakpoint: 480,
                settings: {
                    arrows: false,
                    slidesToShow: 2
                }
            }
        ]
    });
    /*---------------------- 
        magnific-Popup js
    ----------------------*/
    $('.play_btn').magnificPopup({
        type: 'iframe',
        removalDelay: 300,
        mainClass: 'mfp-fade'
    });
    $('.zoom_img').magnificPopup({
        type: 'image',
        gallery: {
            enabled: true
        }
    });
    /*---------------------- 
        Isotope js
    ------------------------*/
    $('#menu_grid').imagesLoaded(function () {
        var $grid = $('.grid').isotope({
            itemSelector: '.menu_item',
            layoutMode: 'fitRows'
        })
        $('.chopcafe_filter').on('click', 'button', function () {
            var filterValue = $(this).attr('data-filter');
            $grid.isotope({
                filter: filterValue
            });
        });


        $('.chopcafe_filter').each(function (i, buttonGroup) {
            var $buttonGroup = $(buttonGroup);
            $buttonGroup.on('click', 'button', function () {
                $buttonGroup.find('.active_btn').removeClass('active_btn');
                $(this).addClass('active_btn');
            });
        });
    });
    $('#chopcafe_gallery').imagesLoaded(function () {
        var $grid = $('.grid_row').isotope({
            itemSelector: '.gallery_item',
            layoutMode: 'fitRows'
        })
        $('.chopcafe_filter').on('click', 'button', function () {
            var filterValue = $(this).attr('data-filter');
            $grid.isotope({
                filter: filterValue
            });
        });
        $('.chopcafe_filter').each(function (i, buttonGroup) {
            var $buttonGroup = $(buttonGroup);
            $buttonGroup.on('click', 'button', function () {
                $buttonGroup.find('.active_btn').removeClass('active_btn');
                $(this).addClass('active_btn');
            });
        });
    });
    $('#chopcafe_masonry').imagesLoaded(function () {
        var $grid = $('.masonry_grid').isotope({
            itemSelector: '.gallery_item',
            masonry: {
                columnWidth: 1
            }
        })
        $('.chopcafe_filter').on('click', 'button', function () {
            var filterValue = $(this).attr('data-filter');
            $grid.isotope({
                filter: filterValue
            });
        });
        $('.chopcafe_filter').each(function (i, buttonGroup) {
            var $buttonGroup = $(buttonGroup);
            $buttonGroup.on('click', 'button', function () {
                $buttonGroup.find('.active_btn').removeClass('active_btn');
                $(this).addClass('active_btn');
            });
        });
    });
    $('#festival_masonry').imagesLoaded(function () {
        var $grid = $('.masonry_grid').isotope({
            itemSelector: '.gallery_item',
            masonry: {
                columnWidth: 1
            }
        })
    });
    /*----------------------
        Counter js
    ------------------------*/
    $('.counter').counterUp({
        delay: 60,
        time: 5000
    });
    /*----------------------
        Countdown Timer js
    ----------------------*/
    if ($('.festival_countdown').length) {
        $('.festival_countdown').each(function () {
            var $this = $(this),
                finalDate = $(this).data('countdown');
            $this.countdown(finalDate, function (event) {
                var $this = $(this).html(event.strftime('' + '<div class="counter_column"><div class="inner"><span class="count">%D</span>Дней</div></div> ' + '<div class="counter_column"><div class="inner"><span class="count">%H</span>Часов</div></div>  ' + '<div class="counter_column"><div class="inner"><span class="count">%M</span>Минут</div></div>  ' + '<div class="counter_column"><div class="inner"><span class="count">%S</span>Секунд</div></div>'));
            });
        });
    }
    /*----------------------
        Datepicker js
    ----------------------*/
    $("#datepicker").datepicker();
    /*----------------------
        wow js
    ------------------------*/
    new WOW().init();
    /*----------------------
        priceslider js
    ----------------------*/
    $(function () {
        $("#price_slider").slider({
            range: true,
            min: 0,
            max: 3500,
            values: [0, 2500],
            slide: function (event, ui) {
                $("#amount").val("$" + ui.values[0] + " - $" + ui.values[1]);
            }
        });
        $("#amount").val("$" + $("#price_slider").slider("values", 0) + " - $" + $("#price_slider").slider("values", 1));
    });
    /*----------------------
        nicenumbr js
    ------------------------*/
  /*  $('input[type="number"]').niceNumber();*/




    $('.phone').mask('(000) 000-00-00');

    $(".open_cart").on('click', function (e) {
        e.preventDefault();
        $(".sidenav_cart").toggleClass("active");
    });
    /*----------------------
        nice-select js
    ------------------------*/
    $('.selectoption').niceSelect();
    // wow js
    new WOW().init();
})(window.jQuery);


$(document).ready(function () {
    setTimeout(function () {
        $(".chopcafe_filter .active_btn").trigger( "click" );
    },2000);

});
