@extends('layouts.app')

@section("content")

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <button type="button" class="close" data-dismiss="alert">×</button>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div><br/>
                @endif

                @if ($message = Session::get('success'))
                    <div class="alert alert-success alert-block">
                        <button type="button" class="close" data-dismiss="alert">×</button>
                        {{ $message }}
                    </div>
                @endif

                <div class="row">
                    <div class="col-sm-4">
                        <a class="btn btn-primary" href="{{route("products.create")}}">Новый товар</a>
                    </div>
                </div>

                <h1>Продукты</h1>
                @isset($products)

                    <div class="row">
                        @foreach($products as $key => $product)
                            <div class="col mb-2">
                                <div class="card" style="width: 18rem;">
                                    <img src="{{$product->image_url}}" class="card-img-top" alt="...">
                                    <div class="card-body">
                                        <h5 class="card-title">{{$product->title}}<span
                                                    class="badge badge-info">{{$product->is_active?"Активный":"Не активный"}}</span>
                                        </h5>
                                        <p class="card-text">{{$product->description}}</p>

                                        <p class="card-text"><span
                                                    class="badge badge-success">{{$product->category}}</span></p>
                                    </div>
                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item">Масса: {{$product->mass}} грамм</li>
                                        <li class="list-group-item">Цена: {{$product->mass}} руб.</li>
                                        <li class="list-group-item">Порция: {{$product->portion_count}} шт.</li>
                                    </ul>
                                    <div class="card-body">
                                        <a href="{{$product->site_url}}" class="card-link">На сайте</a>
                                        <a href="{{ route('products.show',$product->id) }}" class="card-link">
                                            Посмотреть</a>
                                        <a class="card-link" href="{{ route('products.edit',$product->id) }}">
                                            <i class="fas fa-edit"></i>

                                            <form action="{{ route('products.destroy', $product->id)}}" method="post">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-link" type="submit">Удалить</button>
                                            </form>
                                        </a>
                                    </div>
                                </div>
                            </div>

                        @endforeach
                    </div>


                    {{ $products->links() }}
                @endisset
            </div>
        </div>
    </div>
@endsection
