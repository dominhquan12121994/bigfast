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
                <form method="POST" action="{{ route('admin.mail.store') }}">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between">
                            <h4>Tạo mới mẫu mail</h4>
                            <div>
                                <button class="btn btn-success" type="submit">Thêm mới</button>
                                <a href="{{ route('admin.mail.index') }}" class="btn btn-primary">Quay lại</a>
                            </div>
                        </div>
                        <div class="card-body">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                                <div class="form-group row">
                                    <label>Tên</label>
                                    <input class="form-control" type="text" placeholder="Tên" name="name" required autofocus/>
                                </div>
                                <div class="form-group row">
                                    <label>Tiêu đề</label>
                                    <input class="form-control" type="text" placeholder="Tiêu đề" name="subject" required/>
                                </div>
                                <div class="form-group row">
                                    <label>Nội dung</label>
                                    <div class="codes">
                                        @foreach ( \App\Modules\Systems\Constants\MailConstant::codes as $key => $item )
                                            <p>{{$key}}: {{$item}}</p>
                                        @endforeach
                                    </div>
                                    <textarea class="form-control" name="content" rows="20" placeholder="Content" required>
                                    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="x-apple-disable-message-reformatting">
        <title>Example</title>
        <style>
            body {
                background-color:#fff;
                color:#222222;
                margin: 0px auto;
                padding: 0px;
                height: 100%;
                width: 100%;
                font-weight: 400;
                font-size: 15px;
                line-height: 1.8;
            }
            .continer{
                width:400px;
                margin-left:auto;
                margin-right:auto;
                background-color:#efefef;
                padding:30px;
            }
            .btn{
                padding: 5px 15px;
                display: inline-block;
            }
            .btn-primary{
                border-radius: 3px;
                background: #0b3c7c;
                color: #fff;
                text-decoration: none;
            }
            .btn-primary:hover{
                border-radius: 3px;
                background: #4673ad;
                color: #fff;
                text-decoration: none;
            }
        </style>
    </head>
    <body>
        <div class="continer">
            <h1>Lorem ipsum dolor</h1>
            <h4>Ipsum dolor cet emit amet</h4>
            <p>
                 Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea <strong>commodo consequat</strong>.
                 Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.
                 Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
            </p>
            <h4>Ipsum dolor cet emit amet</h4>
            <p>
                Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod <a href="#">tempor incididunt ut labore</a> et dolore magna aliqua.
                 Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
            </p>
            <h4>Ipsum dolor cet emit amet</h4>
            <p>
                Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.
                 Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
            </p>
            <a href="#" class="btn btn-primary">Lorem ipsum dolor</a>
            <h4>Ipsum dolor cet emit amet</h4>
            <p>
                Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.
                 Ut enim ad minim veniam, quis nostrud exercitation <a href="#">ullamco</a> laboris nisi ut aliquip ex ea commodo consequat.
                 Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.
                 Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
            </p>
        </div>
    </body>
    </html>
                                    </textarea>
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
