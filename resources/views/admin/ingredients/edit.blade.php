@extends('layouts.app')


@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-lg-12 margin-tb">
                        <div class="pull-left">
                            <h2>Изменение ингредиента</h2>
                        </div>
                        <div class="pull-right">
                            <a class="btn btn-primary" href="{{ route('ingredients.index') }}"> Назад</a>
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


                <form method="post" action="{{ route('ingredients.update',$ingredient->id) }}">
                    @csrf
                    <input name="_method" type="hidden" value="PUT">

                    <table class="table mt-2">
                        <thead class="thead-light ">
                        <th>Параметр</th>
                        <th>Значение</th>
                        </thead>
                        <tbody>
                        <tr>
                            <td>Заголовок</td>
                            <td>
                                <input type="text" name="title" value="{{$ingredient->title}}" class="form-control">
                            </td>
                        </tr>
                        <tr>
                            <td>Масса, грамм.</td>
                            <td>
                                <input type="text" name="mass" value="{{$ingredient->mass}}" class="form-control">
                            </td>
                        </tr>

                        <tr>
                            <td>Количество, шт.</td>
                            <td>
                                <input type="number" name="quantity" value="{{$ingredient->quantity}}"
                                       class="form-control">
                            </td>
                        </tr>

                        <tr>
                            <td>Цена, руб.</td>
                            <td>
                                <input type="number" name="price" value="{{$ingredient->price}}" class="form-control">
                            </td>
                        </tr>

                        <tr>
                            <td>Способ использования: начинка\покрытие\оба варианта</td>
                            <td>
                                <select name="use_type" id="use_type" class="form-control">
                                    @foreach(\App\Enums\UseIngredientType::getInstances() as $type)
                                        @if ($type->value==$ingredient->use_type->value)
                                            <option value="{{$type->value}}" selected>{{$type->key}}</option>
                                        @else
                                            <option value="{{$type->value}}">{{$type->key}}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </td>
                        </tr>


                        <tr>
                            <td></td>
                            <td>
                                <button class="btn btn-primary">Изменить</button>
                            </td>
                        </tr>


                        </tbody>
                    </table>
                </form>


            </div>
        </div>
    </div>
@endsection
