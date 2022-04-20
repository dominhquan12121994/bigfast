@extends('layouts.baseShop')

@section('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
@endsection

@section('content')

    <div class="container-fluid">
        <div class="animated fadeIn">
            <div class="row">
                {{--<div class="col-sm-12 col-md-10 col-lg-8 col-xl-6">--}}
                <div class="col-12">
                    <div class="card">
                        <form method="POST" id="frmOrderStore" action="{{ route('shop.orders.store') }}">
                            <div class="card-header justify-content-between d-flex">
                                <div>
                                    <i class="fa fa-align-justify"></i> <strong>{{ __('Tạo mới đơn hàng') }}</strong>
                                </div>
                                <div>
                                    <button class="btn btn-success init-disable" type="submit">Thêm mới</button>
                                    <a href="{{ url()->previous() }}" class="btn btn-primary">Quay lại</a>
                                </div>
                            </div>
                            <div class="card-body">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                                <div class="row">
                                    <div class="col-md-12 col-lg-6">
                                        <input type="hidden" name="keyRedisDraft" value="{{ $keyRedisDraft }}">
                                        <input type="hidden" name="shopId" value="{{ $shop->id }}">
                                        <h4>Bên gửi
                                            <span style="font-size: 12px;">(<a href="{{ route('shop.profile.edit') . '?create-order' }}">
                                                <i style="cursor: pointer">Cập nhật <i class="c-icon cil-pencil c-icon-sm"></i></i>
                                            </a>)</span>
                                        </h4>
                                        @if($shopAddress)
                                            <div class="mb-1" style="line-height: 25px">
                                                Người gửi: <b id="txtSender1">{{ $shopAddress->name }} - {{ $shopAddress->phone }}</b>&nbsp;
                                                <div class="btn-group" style="margin-top: -5px">
                                                    <button class="btn btn-sm dropdown-toggle p-0" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Thay đổi</button>
                                                    <div class="dropdown-menu">
                                                        @foreach ($shopAddressAll as $address)
                                                            <a class="dropdown-item"
                                                               onclick="changeSender(`{{ 'a' . $address->id }}`, `{{ $address->name }} - {{ $address->phone }}`, `{{ $address->address }}`)">{{ $address->name . ' - ' . $address->phone . ' - ' . $address->address }}</a>
                                                        @endforeach
                                                    </div>
                                                </div>
                                                <br>
                                                Địa chỉ: <b id="txtSender2">{{ $shopAddress->address }}</b>
                                            </div>
                                            <input type="hidden" class="change_to_calculator_fee" name="senderId" id="senderId" value="{{ $shopAddress->id }}">
                                            <div class="form-check checkbox showHide1">
                                                <input class="form-check-input chk_address_refund" id="check1" type="checkbox" value="" onchange="addressRefund()">
                                                <label class="form-check-label" for="check1">Thêm địa chỉ trả hàng chuyển hoàn</label>
                                                hoặc chọn từ danh sách <b style="cursor: pointer" onclick="show_showHide2()">đã lưu</b>
                                            </div>

                                            <div class="showHide2">
                                                <div class="btn-group" style="margin-top: -5px">
                                                    <button class="btn dropdown-toggle p-0" type="button"
                                                            data-toggle="dropdown" aria-haspopup="true"
                                                            aria-expanded="false">Chọn địa chỉ chuyển hoàn từ địa chỉ đã lưu
                                                    </button>
                                                    <span style="line-height: 35px">&nbsp;&nbsp;hoặc&nbsp;<b style="cursor: pointer" onclick="show_showHide1()">thêm mới</b></span>
                                                    <div class="dropdown-menu">
                                                        @foreach ($shopAddressAll as $address)
                                                            <a class="dropdown-item address_refund_list"
                                                               onclick="selectRefund(`{{ $address->id }}`, `{{ $address->name }} - {{ $address->phone }}<br>{{ $address->address }}`)">{{ $address->name . ' - ' . $address->phone . ' - ' . $address->address }}</a>
                                                        @endforeach
                                                    </div>
                                                </div>
                                                <input type="hidden" name="address_refund" id="address_refund" value="0">
                                            </div>

                                            <div id="box_address_refund_selected" class="showHide2 mt-3 ml-3">
                                                <h6>Địa chỉ chuyển hoàn</h6>
                                                <p class="mb-1" id="txt_box_address_refund_selected"></p>
                                            </div>

                                            <div class="col-12 mt-3 showHide1" id="box_address_refund"></div>
                                        @else
                                            <p>
                                                Shop: <b>{!! $shop->name !!}</b><br>
                                                Hãy thêm địa chỉ gửi hàng trước

                                                <a href="{{ route('shop.profile.edit') . '?create-order' }}">
                                                    <i class="c-icon cil-pencil"></i>
                                                </a>
                                            </p>
                                        @endif
                                    </div>
                                    <div class="col-md-12 col-lg-6">
                                        <h4>Bên nhận</h4>
                                        <div class="form-group row mt-3">
                                            <div class="col-4">
                                                <input name="receiverName" class="form-control form-control-sm" type="text" value="{{ old('receiverName', $draftData['receiverName']) }}"
                                                       required placeholder="Tên người liên hệ">
                                            </div>
                                            <div class="col-4">
                                                <input name="receiverPhone" class="form-control form-control-sm" type="text" value="{{ old('receiverPhone', $draftData['receiverPhone']) }}"
                                                       required placeholder="Số điện thoại">
                                            </div>
                                            <div class="col-4">
                                                <input name="receiverAddress" class="form-control form-control-sm" type="text" value="{{ old('receiverAddress', $draftData['receiverAddress']) }}"
                                                       required placeholder="Địa chỉ lấy hàng">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-4">
                                                <select class="form-control frm-select2 change_to_calculator_fee" id="add1-select1" name="receiverProvinces" onchange="changeProvinces('add1')">
                                                    @foreach ($provinces as $province)
                                                        <option value="{{ $province->id }}" {{ (old('receiverProvinces', $draftData['receiverProvinces']) == $province->id || $inputView['receiverProvince'] == $province->id) ? 'selected="selected"' : '' }}>{{ $province->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-4">
                                                <select class="form-control frm-select2 change_to_calculator_fee" id="add1-select2" name="receiverDistricts" onchange="changeDistricts('add1')">
                                                    @foreach ($districts as $district)
                                                        <option value="{{ $district->id }}" {{ (old('receiverDistricts', $draftData['receiverDistricts']) == $district->id || $inputView['receiverDistrict'] == $district->id) ? 'selected="selected"' : '' }}>{{ $district->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-4">
                                                <select class="form-control frm-select2" id="add1-select3" name="receiverWards">
                                                    @foreach ($wards as $ward)
                                                        <option value="{{ $ward->id }}" {{ (old('receiverWards', $draftData['receiverWards']) == $ward->id) ? 'selected="selected"' : '' }}>{{ $ward->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <br>
                                        <div class="row">
                                            <div class="col-md-12 col-lg-6">
                                                <div class="form-group row">
                                                    <div class="col">
                                                        <b style="font-size: 14px">Hàng hóa - <span id="lb_quantity_products">{{ (old('quantity_products', $draftData['quantity_products']) ?: 1) }}</span> sản phẩm (tên, giá trị, số lượng, mã)</b>
                                                        <span class="float-right">
                                                            <button class="btn btn-sm btn-block btn-outline-dark active init-disable" type="button" aria-pressed="true" onclick="plus()">
                                                                Thêm sản phẩm
                                                            </button>
                                                            <input type="hidden" id="quantity_products" name="quantity_products" value="{{ old('quantity_products', $draftData['quantity_products']) }}">
                                                        </span>
                                                    </div>
                                                </div>
                                                <div id="product_arr">
                                                    @for ($i = 0; $i < (old('quantity_products', $draftData['quantity_products']) ?: 1); $i++)
                                                    <div class="product_item form-group row">
                                                        <div class="col-4 pr-1">
                                                            <input name="addProductName[]" class="form-control init-disable" type="text" value="{{ (old('addProductName')) ? old('addProductName')[$i] : (isset($draftData['addProductName'][$i]) ? $draftData['addProductName'][$i] : '') }}" required placeholder="Tên sản phẩm">
                                                        </div>
                                                        <div class="col-3 pl-1 pr-1">
                                                            <div class="input-group">
                                                                <input name="addProductPrice[]" class="form-control init-disable input_calculator_money" type="number" value="{{ (old('addProductPrice')) ? old('addProductPrice')[$i] : (isset($draftData['addProductPrice'][$i]) ? $draftData['addProductPrice'][$i] : '') }}" required placeholder="Giá trị sản phẩm" min="0" step="1000">
                                                                <div class="input-group-append"><span class="input-group-text">vnđ</span></div>
                                                            </div>
                                                        </div>
                                                        <div class="col-2 pl-1 pr-1">
                                                            <div class="input-group">
                                                                <input name="addProductSlg[]" class="form-control init-disable input_calculator_money" type="number" value="{{ (old('addProductSlg')) ? old('addProductSlg')[$i] : (isset($draftData['addProductSlg'][$i]) ? $draftData['addProductSlg'][$i] : 1) }}" required placeholder="Số lượng" min="1" step="1">
                                                                <div class="input-group-append"><span class="input-group-text">cái</span></div>
                                                            </div>
                                                        </div>
                                                        <div class="col-2 pl-1 pr-1">
                                                            <input name="addProductCode[]" class="form-control init-disable" type="text" value="{{ (old('addProductCode')) ? old('addProductCode')[$i] : (isset($draftData['addProductCode'][$i]) ? $draftData['addProductCode'][$i] : '') }}" placeholder="Nhập mã tự quản lý">
                                                        </div>
                                                        <div class="col-1 pl-1">
                                                            <button class="btn btn-sm btn-dark active remove-btn-product m-1 init-disable" type="button" aria-pressed="true" style="padding: 0px 2px">
                                                                <i class="c-icon cil-trash"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                    @endfor
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-md-3 col-form-label">Khối lượng</label>
                                                    <div class="col-md-9 input-group">
                                                        <input class="form-control init-disable change_to_calculator_fee" type="number" name="weight" value="{{ old('weight', $draftData['weight']) }}" step="1" min="1" required>
                                                        <div class="input-group-append"><span class="input-group-text">gram</span></div>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-md-3 col-form-label">Kích thước</label>
                                                    <div class="col-md-9">
                                                        <div class="row">
                                                            <div class="col-4 input-group">
                                                                <input class="form-control init-disable" type="number" name="length" value="{{ old('length', $draftData['length']) }}" step="1" min="0" required>
                                                                <div class="input-group-append"><span class="input-group-text">cm</span></div>
                                                            </div>
                                                            <div class="col-4 pl-0 pr-0 input-group">
                                                                <input class="form-control init-disable" type="number" name="width" value="{{ old('width', $draftData['width']) }}" step="1" min="0" required>
                                                                <div class="input-group-append"><span class="input-group-text">cm</span></div>
                                                            </div>
                                                            <div class="col-4 input-group">
                                                                <input class="form-control init-disable" type="number" name="height" value="{{ old('height', $draftData['height']) }}" step="1" min="0" required>
                                                                <div class="input-group-append"><span class="input-group-text">cm</span></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-md-3 col-form-label">Tiền thu hộ</label>
                                                    <div class="col-md-9 input-group">
                                                        <input class="form-control init-disable" type="number" id="input_cod" name="cod" value="{{ old('cod', $draftData['cod']) }}" step="1000" min="0" required>
                                                        <div class="input-group-append"><span class="input-group-text">vnđ</span></div>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-md-3 col-form-label">Khai giá</label>
                                                    <div class="col-md-9 input-group">
                                                        <input class="form-control init-disable" type="number" id="input_insurance_value" name="insurance_value" value="{{ old('insurance_value', $draftData['insurance_value']) }}" step="1000" min="0" required>
                                                        <div class="input-group-append"><span class="input-group-text">vnđ</span></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12 col-lg-6">
                                                <h4>Gói cước</h4>
                                                <div class="form-group pl-2">
                                                    <div class="pl-4">
                                                        <div class="row">
                                                            @foreach($orderServices as $key => $service)
                                                                <div class="col-4 p-2">
                                                                    <input class="form-check-input init-disable" id="inline-radio{{ $key + 1 }}" type="radio" value="{{ $service->alias }}" name="service_type" {{ (old('service_type', $draftData['service_type']) == $service->alias) ? 'checked="checked"' : '' }}>
                                                                    <label class="form-check-label" for="inline-radio{{ $key + 1 }}">
                                                                        <b>{{ $service->name }}</b><br>
                                                                        <span id="calculator_fee_{{ $service->alias }}">{{ number_format($arrFeeExpertPick[$key]->fee) . ' vnđ' }}</span><br>
                                                                        <span id="expert_pick_{{ $service->alias }}">Giao dự kiến {{ $arrFeeExpertPick[$key]->timePick }}</span>
                                                                    </label>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>

                                                <h4>Tuỳ chọn</h4>
                                                <div class="form-group row">
                                                    <div class="col-6">
                                                        <div class="form-group">
                                                            <label>Ngày lấy hàng dự kiến</label>
                                                            <select class="form-control change_to_calculator_fee" name="expect_pick">
                                                                @foreach($arrExpectPick as $key => $value)
                                                                <option value="{{ $key }}">{{ $value }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="form-group">
                                                            <label>Bên trả phí</label>
                                                            <select class="form-control" name="payfee">
                                                                @foreach($arrPayfee as $key => $value)
                                                                    <option value="{{ $key }}" {{ old('payfee', $draftData['note1']) == $key ? 'selected' : '' }}>{{ $value }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>

                                                <h4>Lưu ý - Ghi chú</h4>
                                                <div class="form-group row">
                                                    <div class="col-6">
                                                        <div class="form-group">
                                                            <label>Lưu ý giao hàng</label>
                                                            <select class="form-control" name="note1">
                                                                @foreach($arrNote1 as $key => $value)
                                                                    <option value="{{ $key }}" {{ old('note1', $draftData['note1']) == $key ? 'selected' : '' }}>{{ $value }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="form-group">
                                                            <label>Mã đơn khách hàng tự quản</label>
                                                            <input class="form-control init-disable" type="text" name="client_code" value="{{ old('client_code', $draftData['client_code']) }}" placeholder="Nhập mã đơn khách hàng">
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <label>Ghi chú</label>
                                                        <textarea class="form-control init-disable" name="note2" rows="5" placeholder="Ví dụ: Lấy sản phẩm A 2 cái">{{ old('note2', $draftData['note2']) }}</textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>

                                @if ($errors->any())
                                    <br>
                                    <div class="alert alert-danger">
                                        <ul>
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('javascript')
    <script type="text/javascript" src="{{ asset('libs/moment/moment.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
    <script type="application/javascript">
        let i = 1; //  set your counter to 1
        let keyRedisDraft = '{{ $keyRedisDraft }}';

        $(document).ready(function() {
            draftLoop(); //  start the loop

            $(".showHide2").css('display', 'none');

            $(".add_details").click(function () {
                let rand = makeid(5);
                $(".user-details").append(`<div class="user_data">
                                            <br>
                                            <h4>
                                                Địa chỉ lấy hàng
                                                <button class="btn btn-sm btn-dark active remove-btn" type="button" aria-pressed="true" style="padding: 0px 2px">
                                                    <i class="c-icon cil-trash"></i>
                                                </button>
                                            </h4>
                                            <div class="form-group row">
                                                <div class="col-4">
                                                    <label class="required">Tên người liên hệ</label>
                                                    <input name="addName[]" class="form-control" type="text" required>
                                                </div>
                                                <div class="col-4">
                                                    <label class="required">Số điện thoại</label>
                                                    <input name="addPhone[]" class="form-control" type="text" required>
                                                </div>
                                                <div class="col-4">
                                                    <label class="required">Địa chỉ lấy hàng</label>
                                                    <input name="addAddress[]" class="form-control" type="text" required>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-4">
                                                    <label for="` + rand + `-select1">Tỉnh/thành</label>
                                                    <select class="form-control" id="` + rand + `-select1" name="addProvinces[]" onchange="changeProvinces('` + rand + `')">
                                                        @foreach ($provinces as $province)
                                                            <option value="{{ $province->id }}">{{ $province->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-4">
                                                    <label for="` + rand + `-select2">Quận/huyện</label>
                                                    <select class="form-control" id="` + rand + `-select2" name="addDistricts[]" onchange="changeDistricts('` + rand + `')">
                                                        @foreach ($districts as $district)
                                                            <option value="{{ $district->id }}">{{ $district->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-4">
                                                    <label for="` + rand + `-select3">Phường/xã</label>
                                                    <select class="form-control" id="` + rand + `-select3" name="addWards[]">
                                                        @foreach ($wards as $ward)
                                                            <option value="{{ $ward->id }}">{{ $ward->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>`);
            });

            $("body").on("click", ".remove-btn", function (e) {
                $(this).parents('.user_data').remove();
            });

            $("body").on("click", ".remove-btn-product", function (e) {
                let countEl = document.getElementById("quantity_products");
                let count = countEl.value;

                if (count > 1) {
                    count--;
                    countEl.value = count;
                    document.getElementById('lb_quantity_products').innerHTML = count;
                    $(this).parents('.product_item').remove();
                }
            });

            $("body").on("change", ".change_to_calculator_fee", function (e) {
                change_to_calculator_fee();
            });

            $("body").on("change", ".input_calculator_money", function (e) {
                let productPrice = document.getElementsByName("addProductPrice[]");
                let productSlg = document.getElementsByName("addProductSlg[]");
                if (productPrice.length > 0) {
                    let total = 0;
                    let price = 0;
                    let slg = 0;
                    for (i = 0; i < productPrice.length; i++) {
                        slg = parseInt(productSlg[i].value);
                        price = parseInt(productPrice[i].value);

                        if (price > 0 && slg > 0) {
                            total += price * slg;

                            document.getElementById('input_cod').value = total;
                            document.getElementById('input_insurance_value').value = total;
                        }
                    }
                }
            });
        });

        function change_to_calculator_fee() {
            let inputChanges = document.querySelectorAll(".change_to_calculator_fee");

            let receiverProvinces;
            let receiverDistricts;
            let expect_pick;
            let senderId;
            let weight;

            for (const input of inputChanges) {
                let inputName = input.getAttribute('name');

                if (inputName == 'receiverProvinces') {
                    receiverProvinces = input.value;
                }
                if (inputName == 'receiverDistricts') {
                    receiverDistricts = input.value;
                }
                if (inputName == 'weight') {
                    weight = input.value;
                }
                if (inputName == 'expect_pick') {
                    expect_pick = input.value;
                }
                if (inputName == 'senderId') {
                    senderId = input.value;
                }
            }

            let routeApi = '{{ route('api.orders.calculator-fee') }}';
            @foreach($orderServices as $key => $service)
            $.ajax({
                beforeSend: function (xhr) {
                    xhr.setRequestHeader ("Authorization", "Bearer {{ $currentUser->passport_token }}");
                },
                url: routeApi,
                data: {
                    p_id_send: 1,
                    d_id_send: 1,
                    senderId: senderId,
                    p_id_receive: receiverProvinces,
                    d_id_receive: receiverDistricts,
                    service: '{{ $service->alias }}',
                    weight: weight
                },
                success: function(response){
                    if (response.status_code === 200) {
                        document.getElementById('calculator_fee_{{ $service->alias }}').innerHTML = formatNumber(response.data.result) + ' vnđ';
                        document.getElementById('expert_pick_{{ $service->alias }}').innerHTML = 'Giao dự kiến ' + moment(expect_pick, 'DD-MM-YYYY HH:mm:ss').add(response.data.timePick.to, 'days').format('DD/MM/YYYY HH:mm');
                    }
                }
            });
            @endforeach
        }

        function draftLoop() {         //  create a loop function
            setTimeout(function() {   //  call a 1s setTimeout when the loop is called
                let draftData = {
                    addProductName: [],
                    addProductPrice: [],
                    addProductSlg: [],
                    addProductCode: []
                };
                $('#frmOrderStore input, #frmOrderStore select, #frmOrderStore textarea').each(
                    function(index){
                        var input = $(this);
                        // if (input.attr('type') == 'hidden') return;
                        // if (input.val() == '') return;
                        if (input.attr('name') === 'addProductName[]') {
                            draftData['addProductName'].push(input.val());
                        } else {
                            if (input.attr('name') === 'addProductPrice[]') {
                                draftData['addProductPrice'].push(input.val());
                            } else {
                                if (input.attr('name') === 'addProductSlg[]') {
                                    draftData['addProductSlg'].push(input.val());
                                } else {
                                    if (input.attr('name') === 'addProductCode[]') {
                                        draftData['addProductCode'].push(input.val());
                                    } else {
                                        draftData[input.attr('name')] = input.val();
                                    }
                                }
                            }
                        }
                    }
                );
                const rbs = document.querySelectorAll('input[name="service_type"]');
                for (const rb of rbs) {
                    if (rb.checked) {
                        draftData['service_type'] = rb.value;
                        break;
                    }
                }
                if (draftData.hasOwnProperty('receiverName') && draftData.hasOwnProperty('receiverPhone') && draftData.hasOwnProperty('receiverAddress')) {
                    if (draftData.receiverName !== '' && draftData.receiverName !== '' && draftData.receiverName !== '') {
                        setCookie(keyRedisDraft, JSON.stringify(draftData), 30);
                        $('.init-disable').prop('disabled', false);
                    }
                } else {
                    $('.init-disable').prop('disabled', true);
                }
                draftLoop();             //  ..  again which will trigger another
            }, 1000)
        }

        function setCookie(cname, cvalue, exdays) {
            var d = new Date();
            d.setTime(d.getTime() + (exdays*24*60*60*1000));
            var expires = "expires="+ d.toUTCString();
            document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
        }

        function getCookie(cname) {
            var name = cname + "=";
            var decodedCookie = decodeURIComponent(document.cookie);
            var ca = decodedCookie.split(';');
            for(var i = 0; i <ca.length; i++) {
                var c = ca[i];
                while (c.charAt(0) == ' ') {
                    c = c.substring(1);
                }
                if (c.indexOf(name) == 0) {
                    return c.substring(name.length, c.length);
                }
            }
            return "";
        }

        function show_showHide1() {
            $(".showHide1").css('display', 'block');
            $(".showHide2").css('display', 'none');
            document.getElementById("check1").checked = true;
            document.getElementById('address_refund').value = 0;
            addressRefund();
        }
        function show_showHide2() {
            $(".showHide2").css('display', 'block');
            $(".showHide1").css('display', 'none');
            document.getElementById('address_refund').value = '0';
            document.getElementById('box_address_refund').innerHTML = '';
            document.getElementById('txt_box_address_refund_selected').innerHTML = '';
        }

        function changeSender(value, txt1, txt2) {
            document.getElementById('senderId').value = value.substring(1);
            document.getElementById('txtSender1').innerHTML = txt1;
            document.getElementById('txtSender2').innerHTML = txt2;
            change_to_calculator_fee();
        }

        function selectRefund(value, txt) {
            document.getElementById('address_refund').value = value;
            document.getElementById('txt_box_address_refund_selected').innerHTML = txt;
        }

        function addressRefund()
        {
            if($('.chk_address_refund').is(":checked")) {
                document.getElementById('address_refund').value = 'add';
                document.getElementById("box_address_refund").innerHTML = `<h6>Địa chỉ chuyển hoàn</h6>
                                                <div class="form-group row mb-2">
                                                    <div class="col-4">
                                                        <input name="refundName" class="form-control form-control-sm" type="text" required placeholder="Tên người nhận">
                                                    </div>
                                                    <div class="col-4">
                                                        <input name="refundPhone" class="form-control form-control-sm" type="text" required placeholder="Số điện thoại">
                                                    </div>
                                                    <div class="col-4">
                                                        <input name="refundAddress" class="form-control form-control-sm" type="text" required placeholder="Địa chỉ chuyển hoàn">
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <div class="col-4">
                                                        <select class="form-control frm-select2" id="add2-select1" name="refundProvinces" onchange="changeProvinces('add2')">
                                                            @foreach ($provinces as $province)
                                                            <option value="{{ $province->id }}">{{ $province->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-4">
                                                        <select class="form-control frm-select2" id="add2-select2" name="refundDistricts" onchange="changeDistricts('add2')">
                                                            @foreach ($districts as $district)
                                                            <option value="{{ $district->id }}">{{ $district->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-4">
                                                        <select class="form-control frm-select2" id="add2-select3" name="refundWards">
                                                            @foreach ($wards as $ward)
                                                            <option value="{{ $ward->id }}">{{ $ward->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>`;
                $('.frm-select2').select2({theme: "classic"});
            } else {
                document.getElementById('address_refund').value = '0';
                document.getElementById("box_address_refund").innerHTML = '';
            }
        }

        function plus() {
            let countEl = document.getElementById("quantity_products");
            let count = countEl.value;

            count++;
            countEl.value = count;
            document.getElementById('lb_quantity_products').innerHTML = count;

            $("#product_arr").append(`<div class="product_item form-group row">
                                            <div class="col-4 pr-1">
                                                <input name="addProductName[]" class="form-control" type="text" required placeholder="Tên sản phẩm">
                                            </div>
                                            <div class="col-3 pl-1 pr-1">
                                                <div class="input-group">
                                                    <input name="addProductPrice[]" class="form-control input_calculator_money" type="number" required placeholder="Giá trị sản phẩm" min="0" step="1000">
                                                    <div class="input-group-append"><span class="input-group-text">vnđ</span></div>
                                                </div>
                                            </div>
                                            <div class="col-2 pl-1 pr-1">
                                                <div class="input-group">
                                                    <input name="addProductSlg[]" class="form-control input_calculator_money" type="number" required placeholder="Số lượng" value="1" min="1" step="1">
                                                    <div class="input-group-append"><span class="input-group-text">cái</span></div>
                                                </div>
                                            </div>
                                            <div class="col-2 pl-1 pr-1">
                                                <input name="addProductCode[]" class="form-control" type="text" placeholder="Nhập mã tự quản lý">
                                            </div>
                                            <div class="col-1 pl-1">
                                                <button class="btn btn-sm btn-dark active remove-btn-product m-1" type="button" aria-pressed="true" style="padding: 0px 2px">
                                                    <i class="c-icon cil-trash"></i>
                                                </button>
                                            </div>
                                        </div>`);
        }

        function minus() {
            let countEl = document.getElementById("quantity_products");
            let count = countEl.value;

            if (count > 1) {
                count--;
                countEl.value = count;
                document.getElementById('lb_quantity_products').innerHTML = count;
                // $('#product_arr .product_item').last().remove();
            }
        }

        function changeProvinces(randTxt) {
            let provinceID = document.getElementById(randTxt+'-select1').value;
            let routeApi = '{{ route('api.districts.get-by-province', ":slug") }}';
            routeApi = routeApi.replace(':slug', provinceID);

            $.ajax({
                beforeSend: function (xhr) {
                    xhr.setRequestHeader ("Authorization", "Bearer {{ $currentUser->passport_token }}");
                },
                url: routeApi,
                success: function(response){
                    if (response.status_code === 200) {
                        let html = '';
                        response.data.forEach(function (item) {
                            html += '<option value="'+ item.id +'">'+ item.name +'</option>';
                        });
                        document.getElementById(randTxt+"-select2").innerHTML = html;
                        changeDistricts(randTxt);
                    }
                }
            });
        }
        function changeDistricts(randTxt) {
            let districtID = document.getElementById(randTxt+'-select2').value;
            let routeApi = '{{ route('api.wards.get-by-district', ":slug") }}';
            routeApi = routeApi.replace(':slug', districtID);

            $.ajax({
                beforeSend: function (xhr) {
                    xhr.setRequestHeader ("Authorization", "Bearer {{ $currentUser->passport_token }}");
                },
                url: routeApi,
                success: function(response){
                    if (response.status_code === 200) {
                        let html = '';
                        response.data.forEach(function (item) {
                            html += '<option value="'+ item.id +'">'+ item.name +'</option>';
                        });
                        document.getElementById(randTxt+"-select3").innerHTML = html;
                    }
                }
            });
        }

        function makeid(length) {
            var result           = '';
            var characters       = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
            var charactersLength = characters.length;
            for ( var i = 0; i < length; i++ ) {
                result += characters.charAt(Math.floor(Math.random() * charactersLength));
            }
            return result;
        }

        function formatNumber(num) {
            return num.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,')
        }
    </script>
@endsection
