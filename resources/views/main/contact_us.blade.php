@extends('layouts.main')

@section('title', 'Контакты ISUSHI')

@section('content')
    @include("main.partials.search")
    @include("main.partials.header_top_bar")
    @include("main.partials.header_area")
    @include("main.partials.chopcafe_breadcrumb",['title' => "Наши контакты"])
    @include("main.partials.chopcafe_contact")
    @include("main.partials.chopcafe_footer")
@endsection
