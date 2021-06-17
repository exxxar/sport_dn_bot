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
                        <a class="btn btn-primary" href="{{route("prizes.create")}}">Новый приз</a>
                    </div>
                </div>

                <h1>Призы</h1>
                @isset($prizes)
                    <div class="row">


                        @foreach($prizes as $key => $prize)
                            <div class="col">
                                <div class="wrapper" style="padding: 10px">
                                    <div class="card" style="width:220px;">
                                        <!-- Изображение -->
                                        <img class="card-img-top" src="{{$prize->image_url}}" style="width:100%;">
                                        <!-- Текстовый контент -->
                                        <div class="card-body">
                                            <h5>{{$prize->title}}</h5>
                                            <p>{{$prize->description}}</p>
                                            @if ($prize->type===1)
                                                <p>Приз выдается виртуальной валютой</p>
                                                <p>{{$prize->virtual_amount}} руб.</p>
                                            @endif
                                            <a href="{{ route('prizes.show',$prize->id) }}" class="btn btn-primary"><i
                                                    class="fas fa-eye"></i></a>
                                            <a href="{{ route('prizes.edit',$prize->id) }}" class="btn btn-info"><i
                                                    class="fas fa-edit"></i></a>

                                            <form action="{{ route('prizes.destroy', $prize->id)}}" method="post">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-link" type="submit">Удалить</button>
                                            </form>
                                            <div class="row">
                                                <div class="col">
                                                        <span
                                                            class="badge badge-primary">Pos:{{$prize->position}}</span>
                                                </div>
                                                @if($prize->as_default)
                                                    <div class="col">
                                                            <span
                                                                class="badge badge-success">{{$prize->as_default?"Default":""}}</span>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                    </div>
                                </div>

                            </div>
                        @endforeach
                    </div>

                    {{ $prizes->links() }}
                @endisset
            </div>
        </div>
    </div>
@endsection
