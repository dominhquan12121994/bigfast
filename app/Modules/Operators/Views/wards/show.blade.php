@extends('layouts.base')

@section('content')

        <div class="container-fluid">
          <div class="animated fadeIn">
            <div class="row">
              <div class="col-sm-12 col-md-10 col-lg-8 col-xl-6">
                <div class="card">
                    <div class="card-header">
                      <i class="fa fa-align-justify"></i> Phường xã: {{ $ward->name }}</div>
                    <div class="card-body">
                        <h4>Mã:</h4>
                        <p> {{ $ward->code }}</p>
                        <h4>Quận huyện:</h4>
                        <p> {{ $ward->districts->name }}</p>
                        <h4>Tỉnh thành:</h4>
                        <p> {{ $ward->provinces->name }}</p>
                        <a href="{{ route('admin.wards.index') }}" class="btn btn-block btn-primary">Trở về</a>
                    </div>
                </div>
              </div>
            </div>
          </div>
        </div>

@endsection


@section('javascript')

@endsection