@extends('layouts.app')


@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-lg-12 margin-tb">
                        <div class="pull-left">
                            <h2>Изменение продукта</h2>
                        </div>
                        <div class="pull-right">
                            <a class="btn btn-primary" href="{{ route('products.index') }}"> Назад</a>
                        </div>

                        @if (count($errors) > 0)
                            <div class="alert alert-danger mt-2">
                                <strong>Упс!</strong> Возникли ошибки при заполнении полей.<br><br>
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif


                    </div>
                </div>


                <form method="post" action="{{ route('products.update',$product->id) }}">
                    @csrf
                    <input name="_method" type="hidden" value="PUT">


                    <table class="table mt-2">
                            <thead class="thead-light ">
                            <th>Параметр</th>
                            <th>Значение</th>
                            </thead>
                            <tbody>
                            <tr>
                                <td>Заголовок
                                </td>
                                <td>
                                    <input type="text" name="title" class="form-control" value="{{$product->title}}" required>
                                </td>
                            </tr>
                            <tr>
                                <td>Активировать

                                </td>
                                <td>
                                    <input type="checkbox" id="is_active" class="form-control" name="is_active" {{$product->is_active?"checked":""}} required>
                                </td>
                            </tr>
                            <tr>
                                <td>Описание</td>
                                <td>
                                    <textarea class="form-control" name="description" required>{{$product->description}}</textarea>
                                </td>
                            </tr>

                            <tr>
                                <td>Категория</td>
                                <td>
                                    <input type="text" name="category" class="form-control" value="{{$product->category}}" required>

                                </td>
                            </tr>

                            <tr>
                                <td>Масса,грамм</td>
                                <td>
                                    <input type="number" min="0" name="mass" class="form-control" value="{{$product->mass}}" required>
                                </td>
                            </tr>

                            <tr>
                                <td>Цена, руб.</td>
                                <td>
                                    <input type="number" min="0" name="price" class="form-control" value="{{$product->price}}" required>
                                </td>
                            </tr>

                            <tr>
                                <td>Порция, шт.</td>
                                <td>
                                    <input type="number" min="0" name="portion_count" class="form-control" value="{{$product->portion_count}}" required>

                                </td>
                            </tr>

                            <tr>
                                <td>Изображение товара</td>
                                <td>

                                    <div class="row">
                                        <div class="col-3">
                                            <img src="{{$product->image_url}}" alt="" class="img-thumbnail" style="object-fit: cover; height:150px;width: 150px;">
                                        </div>
                                        <div class="col-9">
                                            <input type="url" name="image_url" class="form-control" value="{{$product->image_url}}" required>

                                        </div>
                                    </div>
                                </td>
                            </tr>

                            <tr>
                                <td>Ссылка на товар на сайте</td>
                                <td>
                                    <input type="url"  name="site_url" class="form-control" value="{{$product->site_url}}" required>
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

                </form>


            </div>
        </div>
    </div>
@endsection
