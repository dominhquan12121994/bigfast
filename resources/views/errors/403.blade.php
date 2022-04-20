@extends('layouts.errorBase')

@section('content')

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="clearfix">
                    <h1 class="float-left display-3 mr-4">403</h1>
                    <h4 class="pt-3">Oops! Không có quyền truy cập.</h4>
                    <p class="text-muted">Bạn không có quyền truy cập nội dung đang tìm kiếm.</p>
                </div>
                <div class="input-prepend input-group">
                    <form action="{{ route('logout') }}" method="POST">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                        <button type="submit" class="btn btn-block btn-danger">Thoát</button>
                    </form>&nbsp;
                    <a href="{{ url()->previous() }}" class="btn btn-primary">Quay lại</a>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('javascript')

@endsection