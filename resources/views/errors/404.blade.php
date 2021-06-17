@extends('layouts.main')

@section('title', 'ISUSHI')

@section('content')
    @include("main.partials.search")
    @include("main.partials.header_top_bar")
    @include("main.partials.header_area")
    <!-- Start chopcafe_breadcrumb section -->
    <section class="chopcafe_breadcrumb bg_image" style="background-image: url(assets/images/bg.jpg);">
        <div class="bg_overlay"></div>
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="breadcrumb_content text-center">
                        <h2>Error 404</h2>
                        <ul>
                            <li><a href="index.html">Home</a></li>
                            <li class="active">404</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- End chopcafe_breadcrumb section -->
    <!-- Start chopcafe_404 section -->
    <section class="chopcafe_404 section_padding">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="error_content wow slideInUp">
                        <div class="chopcafe_img">
                            <img src="assets/images/404.png" class="img-fluid" alt="">
                        </div>
                        <p>Упс... Страничка не найдена! Попробуй вернуться на главную:)</p>
                        <a href="{{url('/')}}" class="chopcafe_btn">Вернуться</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- End chopcafe_404 section -->

    @include("main.partials.chopcafe_footer")
@endsection
