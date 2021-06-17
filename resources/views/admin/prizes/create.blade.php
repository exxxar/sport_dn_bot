@extends('layouts.app')


@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-lg-12 margin-tb">
                        <div class="pull-left">
                            <h2>Добавление нового приза</h2>
                        </div>
                        <div class="pull-right">
                            <a class="btn btn-primary" href="{{ route('prizes.index') }}"> Назад</a>
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


                <form method="post" action="{{ route('prizes.store') }}">
                    @csrf
                    <table class="table mt-2">
                        <thead class="thead-light ">
                        <th>Параметр</th>
                        <th>Значение</th>
                        </thead>
                        <tbody>
                        <tr>
                            <td>Заголовок</td>
                            <td>
                                <input type="text" class="form-control" name="title" value="" required>
                            </td>
                        </tr>
                        <tr>
                            <td>Описание</td>
                            <td>
                                <textarea name="description" class="form-control" id="description" cols="30" rows="10"></textarea>
                            </td>
                        </tr>

                        <tr>
                            <td>Картинка приза (ссылка)</td>
                            <td>

                                <input type="url" class="form-control" name="image_url" value="" required>


                            </td>
                        </tr>

                        <tr>
                            <td>Позиция по умолчанию</td>
                            <td>
                                <input type="number" min="0" class="form-control" name="position" value="" required>
                            </td>
                        </tr>

                        <tr>
                            <td>Тип приза</td>
                            <td>
                                <select name="type" id="type" class="form-control" required>
                                    <option value="0">Реальный приз</option>
                                    <option value="1">Виртуальный приз</option>
                                </select>
                            </td>
                        </tr>

                        <tr>
                            <td>Величина виртуального приза</td>
                            <td>
                                <input type="number" min="0" max="1000" class="form-control" name="virtual_amount" value="">
                            </td>
                        </tr>


                        <tr>
                            <td></td>
                            <td>
                                <button class="btn btn-primary">Добавить</button>
                            </td>
                        </tr>



                        </tbody>
                    </table>
                </form>
            </div>
        </div>
    </div>
@endsection
