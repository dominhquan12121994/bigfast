@extends('layouts.base')

@section('content')

<div class="container-fluid">
    <div class="animated fadeIn">
        <div class="row">
            <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Mẫu mail: {{ $template->name }}</h4>
                    </div>
                    <div class="card-body">
                        <h4>Tên</h4>
                        <p>{{ $template->name }}</p>
                        <h4>Tiêu đề</h4>
                        <p>{{ $template->subject }}</p>
                        <h4>Nội dung</h4>
                        <p>{!! $template->content !!}</p>


                        <a href="{{ route('admin.mail.index') }}" class="btn btn-primary">Quay lại</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection


@section('javascript')

@endsection
