@extends('layouts.errorBase')

@section('content')

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="clearfix">
                    <h1 class="float-left display-3 mr-4">404</h1>
                    <h4 class="pt-3">Oops! Không tồn tại.</h4>
                    <p class="text-muted">Nội dung bạn đang tìm kiếm không tồn tại!.</p>
                </div>
                <div class="input-prepend input-group">
                    <a href="/" class="btn btn-info">Trang chủ</a>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('javascript')

@endsection