@extends('layouts.app')


@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-lg-12 margin-tb">
                        <div class="pull-left">
                            <h2>Просмотр информации о пользователе</h2>
                        </div>
                        <div class="pull-right">
                            <a class="btn btn-primary" href="{{ route('users.index') }}"> Назад</a>

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
                        <td>Имя из телеграм</td>
                        <td>
                            <h6>{{$user->name}}</h6>
                            <h1>{{$user->fio_from_telegram}}</h1>

                        </td>
                    </tr>

                    <tr>
                        <td>День рождения</td>
                        <td>
                            <p>{{$user->birthday}}</p>
                        </td>
                    </tr>



                    <tr>
                        <td>Телефон</td>
                        <td>
                            <p>{{$user->phone}}</p>
                        </td>
                    </tr>

                    <tr>
                        <td>Чат telegram id</td>
                        <td>
                            <p>{{$user->telegram_chat_id}}</p>
                        </td>
                    </tr>


                    <tr>
                        <td>Роль пользователя</td>
                        <td>
                            <p>{{$user->is_admin?"Администратор":"Пользователь"}}</p>
                        </td>
                    </tr>

                    <tr>
                        <td></td>
                        <td>
                            <a class="btn btn-primary" href="{{ route('users.edit',$user->id) }}">
                                Редактировать <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('users.destroy', $user->id)}}" method="post">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-link" type="submit">Удалить <i class="fas fa-times"></i></button>
                            </form>
                        </td>
                    </tr>
                    </tbody>
                </table>


            </div>
        </div>
    </div>
@endsection