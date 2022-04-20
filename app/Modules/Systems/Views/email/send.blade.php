@extends('layouts.base')

@section('content')

<div class="container-fluid">
    <div class="animated fadeIn">
        <div class="row">
            <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                <div class="card">
                    <div class="card-header">
                        <h4>Gửi mail: {{ $template->name }}</h4>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.mailSend', ['id' => $template->id]) }}">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                            <div class="form-group row">
                                <label>Địa chỉ nhận mail</label>
                                <input class="form-control" type="text" placeholder="bigfast@gmail.com" name="email" required autofocus/>
                            </div>
                            <button class="btn btn-success" type="submit">Gửi</button>
                            <a href="{{ route('admin.mail.index') }}" class="btn btn-primary">Quay lại</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection


@section('javascript')

@endsection
