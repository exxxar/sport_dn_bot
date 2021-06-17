@extends('layouts.app')


@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-lg-12 margin-tb">
                        <div class="pull-left">
                            <h2>Просмотр информации по продукту</h2>
                        </div>
                        <div class="pull-right">
                            <a class="btn btn-primary" href="{{ route('products.index') }}"> Назад</a>
                            <a class="btn btn-link" href="{{ route('products.edit',$product->id) }}">
                                <i class="fas fa-edit"></i>
                            </a>

                        </div>


                    </div>
                </div>


                    <table class="table mt-2">
                        <thead class="thead-light ">
                        <th>Параметр</th>
                        <th>Значение</th>
                        </thead>
                        <tbody>
                        <tr>
                            <td>Заголовок <span
                                        class="badge badge-secondary">{{$product->is_active?"Активный":"Не активный"}}</span></td>
                            <td>
                                <p>{{$product->url}}</p>
                            </td>
                        </tr>
                        <tr>
                            <td>Описание</td>
                            <td>
                                <p>{{$product->description}}</p>
                            </td>
                        </tr>

                        <tr>
                            <td>Категория</td>
                            <td>
                                <p>{{$product->category}}</p>
                            </td>
                        </tr>

                        <tr>
                            <td>Масса</td>
                            <td>
                                <p>{{$product->mass}} грамм</p>
                            </td>
                        </tr>

                        <tr>
                            <td>Цена</td>
                            <td>
                                <p>{{$product->price}} руб.</p>
                            </td>
                        </tr>

                        <tr>
                            <td>Порция</td>
                            <td>
                                <p>{{$product->portion_count}} шт.</p>
                            </td>
                        </tr>

                        <tr>
                            <td>Изображение товара</td>
                            <td>
                                <img src="{{$product->image_url}}" alt="" class="img-thumbnail" style="object-fit: cover; height:150px;width: 150px;">
                            </td>
                        </tr>

                        <tr>
                            <td>Ссылка на товар на сайте</td>
                            <td>
                                <a href="{{$product->site_url}}" target="_blank">{{$product->site_url}}</a>
                            </td>
                        </tr>

                        <tr>
                            <td></td>
                            <td>
                                <a class="btn btn-primary" href="{{ route('products.edit',$product->id) }}">
                                    Редактировать <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('products.destroy', $product->id)}}" method="post">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-link" type="submit">Удалить <i class="fas fa-times"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>

                        </tbody>
                    </table>

            </div>
        </div>
    </div>
@endsection
