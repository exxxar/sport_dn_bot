@extends('layouts.app')


@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-lg-12 margin-tb">
                        <div class="pull-left">
                            <h2>Просмотр информации по ингридиентам</h2>
                        </div>
                        <div class="pull-right">
                            <a class="btn btn-primary" href="{{ route('ingredients.index') }}"> Назад</a>
                            <a class="btn btn-link" href="{{ route('ingredients.edit',$ingredient->id) }}">
                                <i class="fas fa-edit"></i>
                            </a>

                        </div>


                    </div>
                </div>


                <form method="post" action="{{ route('ingredients.store') }}">
                    @csrf
                    <table class="table mt-2">
                        <thead class="thead-light ">
                        <th>Параметр</th>
                        <th>Значение</th>
                        </thead>
                        <tbody>
                        <tr>
                            <td>Title</td>
                            <td>
                               {{$ingredient->title}}
                            </td>
                        </tr>
                        <tr>
                            <td>Mass</td>
                            <td>
                                <p>{{$ingredient->mass}}</p>
                            </td>
                        </tr>

                        <tr>
                            <td>Quantity</td>
                            <td>
                                <p>{{$ingredient->quantity}}</p>
                            </td>
                        </tr>

                        <tr>
                            <td>Price</td>
                            <td>
                                <p>{{$ingredient->price}}</p>
                            </td>
                        </tr>

                        <tr>
                            <td>Use type</td>
                            <td>
                                <p>{{$ingredient->use_type->key}}</p>
                            </td>
                        </tr>

                        <tr>
                            <td></td>
                            <td>
                                <a class="btn btn-primary" href="{{ route('ingredients.edit',$ingredient->id) }}">
                                    Редактировать <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('ingredients.destroy', $ingredient->id)}}" method="post">
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