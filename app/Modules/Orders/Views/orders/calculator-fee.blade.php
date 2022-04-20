@extends('layouts.base')
@section('content')
<div class="container-fluid">
    <div class="fade-in">
        <form action="{{ route('admin.calculator-fee-finish') }}" method="GET">
            <div class="row">
                <div class="col-12 col-xl-6">
                    <div class="card">
                        <div class="card-header"><strong>Người gửi</strong></div>
                        <div class="card-body">
                            <div class="row">
                                <div class="form-group col-sm-6">
                                    <label>Chọn tỉnh/thành phố:</label>
                                    <select class="form-control" name="p_id_send" id="select1" onchange="changeSenderDistricts();">
                                        @foreach($provinces as $province)
                                        <option value="{{ $province->id }}" {{ $province->id == old('p_id_send') ? 'selected="selected"' : '' }}>{{ $province->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-sm-6">
                                    <label>Chọn quận/huyện:</label>
                                    <select class="form-control" name="d_id_send" id="select2">
                                        @foreach($districtSend as $district)
                                        <option value="{{ $district->id }}" {{ $district->id == old('d_id_send') ? 'selected="selected"' : '' }}>{{ $district->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <!-- /.row-->
                        </div>
                    </div>
                </div>
                <div class="col-12 col-xl-6">
                    <div class="card">
                        <div class="card-header"><strong>Người nhận</strong></div>
                        <div class="card-body">
                            <div class="row">
                                <div class="form-group col-sm-6">
                                    <label>Chọn tỉnh/thành phố:</label>
                                    <select class="form-control" name="p_id_receive" id="select3" onchange="changeReceiverDistricts();">
                                        @foreach($provinces as $province)
                                        <option value="{{ $province->id }}" {{ $province->id == old('p_id_receive') ? 'selected="selected"' : '' }}>{{ $province->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-sm-6">
                                    <label>Chọn quận/huyện:</label>
                                    <select class="form-control" name="d_id_receive" id="select4">
                                        @foreach($districtReceiver as $district)
                                        <option value="{{ $district->id }}" {{ $district->id == old('d_id_receive') ? 'selected="selected"' : '' }}>{{ $district->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <!-- /.row-->
                        </div>
                    </div>
                </div>
                <div class="col-12 col-xl-6">
                    <div class="card">
                        <div class="card-header"><strong>Gói cước</strong></div>
                        <div class="card-body">
                            <div class="row">
                                <div class="form-group col-12 col-sm-6 col-md-4 col-xl-6">
                                    <label>Chọn gói cước:</label>
                                    @foreach($services as $service)
                                    <div class="form-check">
                                        <input class="form-check-input" id="{{ 'radio' . $service->id }}" type="radio"  value="{{ $service->alias }}" name="service" {{ $service->alias == old('service', $services[0]->alias) ? "checked" : '' }}>
                                        <label class="form-check-label" for="{{ 'radio' . $service->id }}">{{ $service->name }}</label>
                                    </div>
                                    @endforeach
                                </div>
                                <div class="form-group col-12 col-sm-6 col-md-8 col-xl-6">
                                    <label>Khối lượng:</label>
                                    <input name="weight" class="form-control" id="input1" type="number" value="{{ old('weight', 0) }}" placeholder="Nhập khối lượng hàng hóa (gram)" min="0">
                                    <button type="submit" class="btn btn-primary mt-3 float-right">Tính phí vận chuyển</button>
                                </div>
                                <div class="form-group col-sm-12">
                                    @if ($errors->any())
                                    <div class="alert alert-danger">
                                        <ul>
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-xl-6">
                    <div class="card">
                        <div class="card-header"><strong>Chi phí vận chuyển</strong></div>
                        <div class="card-body">
                            <div class="row">
                                <div class="form-group col-sm-12">
                                    <label for="name">Tạm tính (vnđ)</label>
                                    <input class="form-control" id="name" type="text" value="{{ old('calculatedFee', 0) }}" disabled>
                                </div>
                            </div>
                            <!-- /.row-->
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
@section('javascript')
<script type="application/javascript">
    function changeSenderDistricts() {
        let xmlhttp = new XMLHttpRequest();
        let provinceID = document.getElementById('select1').value;
        let routeApi = '{{ route('api.districts.get-by-province', ":slug") }}';
        routeApi = routeApi.replace(':slug', provinceID);
        xmlhttp.onreadystatechange = function() {
            if (xmlhttp.readyState == XMLHttpRequest.DONE) { // XMLHttpRequest.DONE == 4
                if (xmlhttp.status == 200) {
                    var response = JSON.parse(xmlhttp.responseText);
                    if (response.status_code == 200) {
                        let html = '';
                        response.data.forEach(function(item) {
                            html += '<option value="' + item.id + '">' + item.name + '</option>';
                        });
                        document.getElementById("select2").innerHTML = html;
                    }
                } else if (xmlhttp.status == 400) {
                    alert('There was an error 400');
                } else {
                    alert('something else other than 200 was returned');
                }
            }
        };
        xmlhttp.open("GET", routeApi, true);
        xmlhttp.send();
    }

    function changeReceiverDistricts() {
        let xmlhttp = new XMLHttpRequest();
        let provinceID = document.getElementById('select3').value;
        let routeApi = '{{ route('api.districts.get-by-province', ":slug") }}';
        routeApi = routeApi.replace(':slug', provinceID);
        xmlhttp.onreadystatechange = function() {
            if (xmlhttp.readyState == XMLHttpRequest.DONE) { // XMLHttpRequest.DONE == 4
                if (xmlhttp.status == 200) {
                    var response = JSON.parse(xmlhttp.responseText);
                    if (response.status_code == 200) {
                        let html = '';
                        response.data.forEach(function(item) {
                            html += '<option value="' + item.id + '">' + item.name + '</option>';
                        });
                        document.getElementById("select4").innerHTML = html;
                    }
                } else if (xmlhttp.status == 400) {
                    alert('There was an error 400');
                } else {
                    alert('something else other than 200 was returned');
                }
            }
        };
        xmlhttp.open("GET", routeApi, true);
        xmlhttp.send();
    }

    $('select').select2({theme: "classic"});

    $(document).ready(function() {
        // changeSenderDistricts();
        // changeReceiverDistricts();
    });
</script>
@endsection
