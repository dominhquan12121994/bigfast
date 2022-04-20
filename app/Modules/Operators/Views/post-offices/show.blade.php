@extends('layouts.base')

@section('content')

        <div class="container-fluid">
          <div class="animated fadeIn">
            <div class="row">
              <div class="col-sm-12 col-md-10 col-lg-8 col-xl-6">
                <div class="card">
                    <div class="card-header">
                      <i class="fa fa-align-justify"></i> Bưu cục: {{ $postOffices->name }}</div>
                    <div class="card-body">
                        <h4>Phường xã:</h4>
                        <p> {{ $postOffices->wards->name }}</p>
                        <h4>Quận huyện:</h4>
                        <p> {{ $postOffices->districts->name }}</p>
                        <h4>Tỉnh thành:</h4>
                        <p> {{ $postOffices->provinces->name }}</p>
                        <a href="{{ route('admin.post-offices.index') }}" class="btn btn-block btn-primary">Trở về</a>
                    </div>
                </div>
              </div>
            </div>
          </div>
        </div>

@endsection


@section('javascript')

@endsection