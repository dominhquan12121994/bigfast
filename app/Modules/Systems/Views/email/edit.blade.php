@extends('layouts.base')

@section('css')
    <style>
        .codes{
            width: 100%;
            border: solid 1px;
            padding: 5px;
            background: #fef4e8;
        }
    </style>
@endsection

@section('content')

<div class="container-fluid">
    <div class="animated fadeIn">
        <div class="row">
            <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                <form method="POST" action="{{ route('admin.mail.update', $template->id) }}">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between">
                            <h4>Cập nhật mẫu mail</h4>
                            <div>
                                <button class="btn btn-success" type="submit">Cập nhật</button>
                                <a href="{{ route('admin.mail.index') }}" class="btn btn-primary">Quay lại</a>
                            </div>
                        </div>
                        <div class="card-body">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                                @method('PUT')
                                <div class="form-group row">
                                    <label>Tên</label>
                                    <input class="form-control" type="text" placeholder="Tên" name="name" required autofocus value="{{ $template->name }}"/>
                                </div>
                                <div class="form-group row">
                                    <label>Tiêu đề</label>
                                    <input class="form-control" type="text" placeholder="Tiêu đề" name="subject" required value="{{ $template->subject }}"/>
                                </div>
                                <div class="form-group row">
                                    <label>Nội dung</label>
                                    <div class="codes">
                                        @foreach ( \App\Modules\Systems\Constants\MailConstant::codes as $key => $item )
                                            <p>{{$key}}: {{$item}}</p>
                                        @endforeach
                                    </div>
                                    <textarea class="form-control" name="content" rows="20" placeholder="Content" required>{{ $template->content }}</textarea>
                                </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection


@section('javascript')

@endsection
