@extends('layouts.baseShop')

@section('content')

        <div class="container-fluid">
          <div class="animated fadeIn">
            <div class="row">
              <div class="col-12">
                <div class="card">
                    <div class="card-header">
                      <i class="fa fa-align-justify"></i> Shop: {!! $shop->name !!}</div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12 col-lg-6">
                                <h5>Số điện thoại:</h5>
                                <p> {{ $shop->phone }}</p>
                                <h5>Mail:</h5>
                                <p> {{ $shop->email }}</p>
                                <h5>Địa chỉ:</h5>
                                <p> {{ $shop->address }}</p>
                                <br>
                                <h4>Thông tin tài khoản</h4>
                                <p> Ngân hàng: <strong>{{ $shop->bank->bank_name }}</strong></p>
                                <p> Chi nhánh: <strong>{{ $shop->bank->bank_branch }}</strong></p>
                                <p> Chủ tài khoản: <strong>{{ $shop->bank->stk_name }}</strong></p>
                                <p> Số toàn khoản: <strong>{{ $shop->bank->stk }}</strong></p>
                            </div>
                            <div class="col-md-12 col-lg-6">
                                @foreach($shop->getAddress as $address)
                                    <div class="user_data">
                                        <h4>Địa chỉ lấy hàng</h4>
                                        <div class="row">
                                            <div class="col-4">
                                                <p>Tên người liên hệ: {{ $address->name }}</p>
                                            </div>
                                            <div class="col-4">
                                                <p>Số điện thoại: {{ $address->phone }}</p>
                                            </div>
                                            <div class="col-4">
                                                <p>Địa chỉ lấy hàng: {{ $address->address }}</p>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-4">
                                                <p>Tỉnh/thành: {{ $address->provinces->name }}</p>
                                            </div>
                                            <div class="col-4">
                                                <p>Quận/huyện: {{ $address->districts->name }}</p>
                                            </div>
                                            <div class="col-4">
                                                <p>Phường/xã: {{ $address->wards->name }}</p>
                                            </div>
                                        </div>
                                    </div><br>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <a href="{{ url()->previous() }}" class="btn btn-primary">Quay lại</a>
                    </div>
                </div>
              </div>
            </div>
          </div>
        </div>

@endsection


@section('javascript')

@endsection
