@extends('layouts.baseShop')

@section('css')
    <style type="text/css">
        .box-info-order-draft {
            font-size: 13px;
        }

        .box-parent {
            position: relative;
            padding-bottom: 80px;
        }

        .box-child-actions {
            display: none;
            position: absolute;
            bottom: 0;
            left: 15px;
            width: 100%;
            height: 80px;
            box-shadow: 0 -5px 5px -5px #666666;
        }
    </style>
@endsection

@section('content')

        <div class="container-fluid">
          <div class="animated fadeIn">
            <div class="row">
              <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                <div class="card">
                    @include('Shops::order-staff.shared.header')
                    <div class="card-body box-parent">

                        <div class="row m-0 mb-3">
                            @include('Shops::order-staff.shared.count-status')
                        </div>

                        <div class="row m-2">
                            <div class="form-check form-check-inline mr-5">
                                <input class="form-check-input" type="checkbox" name="cbxDraftAll" id="cbxFullData" value="1">
                                <label class="form-check-label" for="cbxFullData" style="cursor: pointer">Chọn tất cả đơn nháp hợp lệ</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="cbxDraftAll" id="cbxMissData" value="2">
                                <label class="form-check-label" for="cbxMissData" style="cursor: pointer">Chọn tất cả đơn nháp <b>không</b> hợp lệ</label>
                            </div>
                        </div>

                        <div class="row m-2">
                            @php
                                $count = 0;
                            @endphp
                            @foreach($draftList as $key => $draft)
                                @php
                                    $count++;
                                @endphp
                            <div class="col-sm-12 col-md-6 col-lg-4 col-xl-3 p-2">
                                <div class="card col p-2 mb-0">
                                    <div class="card-body pt-0 pb-0">
                                        <div class="row">
                                            <div class="col p-0">
                                                <span class="badge badge-pill badge-dark">{{ $count }}</span>
                                                <a href="{{ route('shop.orders-drafts', array('shop_id' => $shop->id)) . '?rm=' . $key }}"
                                                   class="badge badge-pill badge-danger float-right">
                                                    <i class="c-icon c-icon-sm cil-x"></i>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="row mt-1 box-info-order-draft" style="cursor: pointer"
                                             onclick="location.href=`{{ route('shop.orders.create-by-shop') . '?draft=' . $key }}`;return false;">
                                            <div class="col-6 p-0">
                                                {!! $shop->name !!}<br>
                                                {{ $shop->address }}
                                            </div>
                                            {{--<div class="col-2 p-0" style="display: table; height: 100%;">--}}
                                                {{--<i class="c-icon c-icon-2xl cil-arrow-thick-right"></i>--}}
                                            {{--</div>--}}
                                            <div class="col-6 p-0" style="text-align: right;">
                                                {{ $draft->receiverName }}<br>
                                                {{ $draft->receiverPhone }}<br>
                                                {{ $draft->receiverAddress }}
                                            </div>
                                        </div>
                                        <div class="row form-group">
                                            <div class="col p-0 mt-1">
                                            <div class="progress mb-2 mt-2 p-0" style="height: 3px;">
                                                <div class="progress-bar {{ ($draft->process === 100) ? 'bg-success' : 'bg-warning' }}" role="progressbar" style="width: {{ $draft->process }}%;" aria-valuenow="{{ $draft->process }}" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                            <div class="form-check float-right">
                                                <input class="form-check-input {{ ($draft->process === 100) ? 'cbxFullData' : 'cbxMissData' }} cbxData" type="checkbox" name="{{ ($draft->process === 100) ? 'cbxFullData' : 'cbxMissData' }}" value="{{ $key }}">
                                            </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <div class="row box-child-actions p-2" id="boxActions">
                            <div class="d-flex">
                                <div class="flex-grow-1 ml-3">
                                    <b>Đã chọn</b><br>
                                    <b id="countOrderDraftSelected" style="font-size: 30px; color: orangered">1</b> đơn nháp
                                </div>
                                <div class="d-flex align-items-center mr-2 show-create-orders">
                                    <p class="m-0 text-right show-create-orders">Vui lòng kiểm tra kỹ thông tin chi tiết<br> từng đơn hàng trước khi tạo đơn</p>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div><button class="btn btn-pill btn-dark mr-2 show-delete-draft" type="button" onclick="deleteDraft();">Xoá đơn nháp</button></div>
                                    <div><button class="btn btn-pill btn-primary show-create-orders" type="button" onclick="createOrder();">Tạo đơn hàng</button></div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
              </div>
            </div>
          </div>
        </div>

@endsection


@section('javascript')
    <script type="application/javascript">
        $(document).ready(function() {
            let routeApi = '{{ route('api.shops.find-by-name') }}';

            $('#shopSelected').select2({
                theme: "classic",
                placeholder: 'Nhập tên Shop để lên đơn hàng',
                ajax: {
                    delay: 300,
                    url: routeApi,
                    dataType: 'json',
                    data: function (params) {
                        return {
                            search: params.term, // search term
                        };
                    },
                    processResults: function (data) {
                        // Transforms the top-level key of the response object from 'items' to 'results'
                        let resData = [];
                        if (data.status_code === 200) {
                            resData = data.data;
                        }
                        return {
                            results: resData
                        };
                    },
                    cache: true
                },
                minimumInputLength: 3,
                templateResult: formatRepo,
                templateSelection: formatRepoSelection
            });

            // checkbox full data
            let select_all = document.getElementById("cbxFullData"); //select all checkbox
            let checkboxes = document.getElementsByClassName("cbxFullData"); //checkbox items

            // checkbox miss data
            let select_all_miss = document.getElementById("cbxMissData"); //select all checkbox
            let checkboxes_miss = document.getElementsByClassName("cbxMissData"); //checkbox items

            //select all checkboxes
            select_all.addEventListener("change", function(e){
                for (i = 0; i < checkboxes.length; i++) {
                    checkboxes[i].checked = select_all.checked;
                }
            });

            for (let i = 0; i < checkboxes.length; i++) {
                checkboxes[i].addEventListener('change', function(e){ //".checkbox" change
                    //uncheck "select all", if one of the listed checkbox item is unchecked
                    if(this.checked === false){
                        select_all.checked = false;
                    }
                    //check "select all" if all checkbox items are checked
                    if(document.querySelectorAll('.cbxFullData:checked').length === checkboxes.length){
                        select_all.checked = true;
                    }
                });
            }

            //select all checkboxes
            select_all_miss.addEventListener("change", function(e){
                for (i = 0; i < checkboxes_miss.length; i++) {
                    checkboxes_miss[i].checked = select_all_miss.checked;
                }
            });

            for (let i = 0; i < checkboxes_miss.length; i++) {
                checkboxes_miss[i].addEventListener('change', function(e){ //".checkbox" change
                    //uncheck "select all", if one of the listed checkbox item is unchecked
                    if(this.checked === false){
                        select_all_miss.checked = false;
                    }
                    //check "select all" if all checkbox items are checked
                    if(document.querySelectorAll('.cbxMissData:checked').length === checkboxes_miss.length){
                        select_all_miss.checked = true;
                    }
                });
            }

            select_all.addEventListener("change", function(e){
                if (select_all.checked) {
                    $("#boxActions").fadeIn();
                    select_all_miss.checked = false;
                    document.getElementById("countOrderDraftSelected").innerHTML = checkboxes.length;
                    $(".show-create-orders").show();
                    $(".show-delete-draft").show();
                    for (i = 0; i < checkboxes_miss.length; i++) {
                        checkboxes_miss[i].checked = false;
                    }
                } else {
                    $("#boxActions").fadeOut();
                }
            });
            select_all_miss.addEventListener("change", function(e){
                if (select_all_miss.checked) {
                    $("#boxActions").fadeIn();
                    select_all.checked = false;
                    document.getElementById("countOrderDraftSelected").innerHTML = checkboxes_miss.length;
                    $(".show-create-orders").hide();
                    $(".show-delete-draft").show();
                    for (i = 0; i < checkboxes.length; i++) {
                        checkboxes[i].checked = false;
                    }
                } else {
                    $("#boxActions").fadeOut();
                }
            });

            document.querySelectorAll('.cbxData').forEach(item => {
                item.addEventListener('click', event => {
                    if(document.querySelectorAll('.cbxData:checked').length > 0){
                        $("#boxActions").fadeIn();
                        document.getElementById("countOrderDraftSelected").innerHTML = document.querySelectorAll('.cbxData:checked').length;
                        if(document.querySelectorAll('.cbxMissData:checked').length > 0){
                            $(".show-create-orders").hide();
                        } else {
                            $(".show-create-orders").show();
                        }
                        $(".show-delete-draft").show();
                    } else {
                        $("#boxActions").fadeOut();
                    }
                })
            })
        });

        function formatRepo (repo) {
            if (repo.loading) {
                return repo.text;
            }

            var $container = $(
                "<div class='select2-result-repository clearfix'>" +
                "<div class='select2-result-repository__name'></div>" +
                "<div class='select2-result-repository__phone'></div>" +
                "<div class='select2-result-repository__address'></div>" +
                "</div>" +
                "</div>" +
                "</div>"
            );

            $container.find(".select2-result-repository__name").text('Tên shop: '+ repo.name);
            $container.find(".select2-result-repository__phone").text('Sđt: ' + repo.phone);
            $container.find(".select2-result-repository__address").text('Địa chỉ: '+ repo.address);

            return $container;
        }

        function formatRepoSelection (repo) {
            return repo.name || repo.phone;
        }

        function deleteDraft() {
            if(document.querySelectorAll('.cbxData:checked').length > 0){
                let checkboxes = document.getElementsByClassName("cbxData"); //checkbox items
                if (checkboxes.length > 0) {
                    let strDraftKey = '';
                    for (i = 0; i < checkboxes.length; i++) {
                        if (checkboxes[i].checked) {
                            strDraftKey += checkboxes[i].value + ',';
                        }
                    }
                    location.href = `{{ route('shop.orders-drafts', array('shop_id' => $shop->id)) }}` + '&rm=' + strDraftKey.slice(0, -1);
                }
            }
        }

        function createOrder() {
            if(document.querySelectorAll('.cbxData:checked').length > 0){
                let checkboxes = document.getElementsByClassName("cbxData"); //checkbox items
                if (checkboxes.length > 0) {
                    let strDraftKey = '';
                    for (i = 0; i < checkboxes.length; i++) {
                        if (checkboxes[i].checked) {
                            strDraftKey += checkboxes[i].value + ',';
                        }
                    }
                    strDraftKey = strDraftKey.slice(0, -1);
                    $.ajax({
                        beforeSend: function (xhr) {
                            xhr.setRequestHeader ("Authorization", "Bearer {{ $currentUser->passport_token }}");
                        },
                        url: `{{ route('api.shop.orders.create-by-draft', array('shop' => 'shop')) }}`,
                        dataType: 'json',
                        type: 'post',
                        contentType: 'application/json',
                        data: JSON.stringify({
                            "userId": `{{ $shop->id }}`,
                            "shopId": `{{ $shop->id }}`,
                            "draftKey": strDraftKey
                        }),
                        processData: false,
                        success: function( data, textStatus, jQxhr ){
                            $.Toast("Thành công", "Tạo đơn hàng thành công từ đơn nháp", "notice");
                            setTimeout(function () {
                                location.reload();
                            }, 1000)
                        },
                        error: function( jqXhr, textStatus, errorThrown ){
                            console.log( errorThrown );
                        }
                    });
                }
            }
        }
    </script>
@endsection

