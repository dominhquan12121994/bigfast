@extends('layouts.base')

@section('content')

        <div class="container-fluid">
          <div class="animated fadeIn">
            <div class="row">
              <div class="col-sm-12 col-md-10 col-lg-8 col-xl-6">
                <div class="card">
                    <div class="card-header">
                      <i class="fa fa-align-justify"></i> Quận huyện: {{ $district->name }}</div>
                    <div class="card-body">
                        <h4>Mã:</h4>
                        <p> {{ $district->code }}</p>
                        <h4>Loại:</h4>
                        <p> {{ $district->type }}</p>
                        <h4>Tỉnh thành:</h4>
                        <p> {{ $district->provinces->name }}</p>
                        <a href="{{ route('admin.districts.index') }}" class="btn btn-block btn-primary">Trở về</a>
                    </div>
                </div>
              </div>
            </div>
          </div>
        </div>

@endsection


@section('javascript')

@endsection