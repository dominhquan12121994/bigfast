@extends('layouts.base')

@section('css')
    <style type="text/css">
        select + .select2-container {
            width: 200px !important;
        }
    </style>
@endsection

@section('script-header')
    <link rel="stylesheet" href="{{ asset('libs/multiselect/css/bootstrap-multiselect.min.css') }}" type="text/css">
    <script type="text/javascript" src="{{ asset('libs/multiselect/js/bootstrap-multiselect.min.js') }}"></script>
@endsection

@section('content')

    <div class="container-fluid">
        <div class="animated fadeIn">
            <div class="row">
                <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                    <div class="card">
                        @include('Orders::orders.shared.header')
                        {{--@if($shop)--}}
                        <div class="card-body">

                            @include('Orders::orders.shared.count-status')

                            @include('Orders::orders.shared.filter')

                            @include('Orders::orders.shared.order-queue')

                            <div class="row">
                                <div class="col-auto mr-auto"></div>
                                <div class="col-auto">
                                    {{ $orders->withQueryString()->links() }}
                                </div>
                            </div>

                            @include('Orders::orders.shared.right')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('javascript')
    <script type="text/javascript" src="{{ asset('libs/moment/moment.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('libs/daterangepicker/daterangepicker.min.js') }}"></script>
    <link rel="stylesheet" type="text/css" href="{{ asset('libs/daterangepicker/daterangepicker.min.css') }}" />

    <script type="application/javascript">
        $(document).ready(function() {
            let routeApi = '{{ route('api.shops.find-by-name') }}';
            var $shopSelected = $('#shopSelected');
            $shopSelected.select2({
                theme: "classic",
                placeholder: 'Nhập tên Shop để lên đơn hàng',
                allowClear: true,
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
                width: 'resolve',
                minimumInputLength: 3,
                templateResult: formatRepo,
                templateSelection: formatRepoSelection,
                language: {
                    inputTooShort: function() {
                        return 'Nhập thông tin tìm kiếm';
                    },
                    noResults: function() { return 'Không có kết quả phù hợp'; }
                }
            });

            $('#search_status').multiselect({
                maxHeight : 400,
                // enableFiltering: true,
                enableClickableOptGroups: true,
                nonSelectedText: 'Vui lòng chọn trạng thái',
                onChange: function(option, checked) {
                    // Get selected options.
                    let statusDetailArr = [];
                    let selectedOptions = $('#search_status option:selected');
                    selectedOptions.each(function() {
                        statusDetailArr.push($(this).val());
                    });
                    document.getElementById("statusList").value = statusDetailArr.join();
                },
                buttonText: function(options, select) {
                    if (options.length === 0) {
                        return 'Vui lòng chọn trạng thái!';
                    }
                    else {
                        return 'Có ' + options.length + ' trạng thái được chọn!';
                    }
                }
            });

            $('#filter_limit').on('change', function() {
                HoldOn.open({theme:"sk-rect"});
                let route = '{{ route('admin.orders.index') }}';
                window.location = reFormatUriParam(route, '&limit=' + this.value);
            });

            (function countdown(remaining) {
                if(remaining <= 0)
                    location.reload(true);
                if (remaining < 10) remaining = '0' + remaining;
                document.getElementById('countdown').innerHTML = '00 : ' + remaining;
                setTimeout(function(){ countdown(remaining - 1); }, 1000);
            })(59); // 60 seconds
        });

        function reFormatUriParam(route = '', params = '') {
            const queryString = window.location.search;
            const urlParams = new URLSearchParams(queryString);

            const newParams = new URLSearchParams(params);
            const newKeys = newParams.keys();
            for (const key of newKeys) {
                if (parseInt(newParams.get(key)) !== -1) {
                    urlParams.set(key, newParams.get(key));
                }
            }
            return route + '?' + urlParams.toString();
        }

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

        function shopSelectedChange() {
            HoldOn.open({theme:"sk-rect"});
            let shopSelected = document.getElementById('shopSelected').value;
            let route = '{{ route('admin.orders.index') }}';
            {{--let route = '{{ route('admin.orders.index', ":slug") }}';--}}
            // route = route.replace(':slug', shopSelected);
            const queryString = window.location.search;
            if (queryString === '') route += '?';
            window.location = route + queryString + '&shop=' + shopSelected;
        }

        function shopSelectedRedis(shopSelected) {
            HoldOn.open({theme:"sk-rect"});
            let route = '{{ route('admin.orders.index') }}';

            const queryString = window.location.search;
            if (queryString === '') route += '?';
            window.location = route + queryString + '&shop=' + shopSelected;
        }

        function formatRepoSelection (repo) {
            if (repo.id === '') return 'Tìm kiếm Shop cần quản lý';
            return repo.name || repo.phone;
        }
    </script>
@endsection

