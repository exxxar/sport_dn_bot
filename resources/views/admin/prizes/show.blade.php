@extends('layouts.app')


@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-lg-12 margin-tb">
                        <div class="pull-left">
                            <h2>Просмотр информации о призе</h2>
                        </div>
                        <div class="pull-right">
                            <a class="btn btn-primary" href="{{ route('prizes.index') }}"> Назад</a>
                            <a class="btn btn-link" href="{{ route('prizes.edit',$prize->id) }}">
                                <i class="fas fa-edit"></i>
                            </a>

                        </div>


                    </div>
                </div>


                <form method="post" action="{{ route('prizes.store') }}">
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
                              <p>{{$prize->title}}</p>
                            </td>
                        </tr>
                        <tr>
                            <td>Description</td>
                            <td>
                                <p>{{$prize->description}}</p>
                            </td>
                        </tr>

                        <tr>
                            <td>Image url</td>
                            <td>

                                <img src="{{$prize->image_url}}" class="img-thumbnail" alt="" style="width:150px;height: 150px;object-fit: cover;">


                            </td>
                        </tr>

                        <tr>
                            <td>Position</td>
                            <td>
                                <p>{{$prize->position}}</p>
                            </td>
                        </tr>

                        <tr>
                            <td>Default</td>
                            <td>
                                <p>{{$prize->as_default?"По умолчанию":"Не установлено"}}</p>
                            </td>
                        </tr>


                        <tr>
                            <td></td>
                            <td>
                                <a class="btn btn-primary" href="{{ route('prizes.edit',$prize->id) }}">
                                    Редактировать <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('prizes.destroy', $prize->id)}}" method="post">
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