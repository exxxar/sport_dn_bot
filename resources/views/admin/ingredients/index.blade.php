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
                        <a class="btn btn-primary" href="{{route("ingredients.create")}}">Новый ингридиент</a>
                    </div>
                </div>

                    <h1>Ингридиенты</h1>
                @isset($ingredients)
                    <table class="table mt-2">

                        <thead class="thead-light">
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Название</th>
                            <th scope="col">Масса</th>
                            <th scope="col">Количество</th>
                            <th scope="col">Цена</th>
                            <th scope="col">Тип использования</th>
                            <th scope="col">Действие</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($ingredients as $key => $ingredient)
                            <tr>
                                <td>{{$key + 1}}</td>
                                <td><a href="{{ route('ingredients.show',$ingredient->id) }}">
                                        {{$ingredient->title}}</a>
                                    <a class="btn btn-link" href="{{ route('ingredients.edit',$ingredient->id) }}">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                </td>
                                <td>
                                    {{$ingredient->mass}}
                                </td>
                                <td>
                                    {{$ingredient->quantity}}
                                </td>
                                <td>
                                    {{$ingredient->price}}
                                </td>
                                <td>
                                    {{$ingredient->use_type->key}}
                                </td>
                                <td>
                                    <form action="{{ route('ingredients.destroy', $ingredient->id)}}" method="post">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-link" type="submit"><i class="fas fa-times"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach

                        </tbody>
                    </table>

                    {{ $ingredients->links() }}
                @endisset
            </div>
        </div>
    </div>
@endsection