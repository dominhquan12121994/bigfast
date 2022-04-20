@extends('layouts.base')

@section('content')

        <div class="container-fluid">
          <div class="animated fadeIn">
            <div class="row">
              <div class="col-sm-12 col-md-10 col-lg-8 col-xl-6">
                <div class="card">
                    <div class="card-header">
                      <i class="fa fa-align-justify"></i> Tỉnh thành: {{ $province->name }}</div>
                    <div class="card-body">
                        <h4>Mã:</h4>
                        <p> {{ $province->code }}</p>
                        <h4>Khu vực:</h4>
                        <p> {{ $province->zone }}</p>
                        <a href="{{ route('admin.provinces.index') }}" class="btn btn-block btn-primary">Trở về</a>
                    </div>
                </div>
              </div>
            </div>
          </div>
        </div>

@endsection


@section('javascript')

@endsection