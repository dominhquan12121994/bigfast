@extends('layouts.base')

@section('css')
	<style>
        .hidden-request {
            display: none;
        }
        #modal_aside_left {
            padding-right: 0px !important; 
        }
        #modal_aside_left .active {
            color: #4f5d73 !important;
            background-color: #b9bec7 !important;
            border-color: #b2b8c1 !important;
            box-shadow: 0 0 0 0.2rem rgba(187, 192, 201, 0.5) !important;
        }
        .select2-container {
            width: 100% !important;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="animated fadeIn">
            <div class="row">
                <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
					<div class="card">
						<div class="card-header">
							<i class="fa fa-align-justify"></i> Log hệ thống
                            <a class="float-right"><button type="button" class="btn btn-info" data-toggle="modal" data-target="#modal_aside_left" style="margin-top: 4px">Tìm kiếm</button></a>
						</div>
						<div class="card-body">
							<table class="table table-responsive-sm table-responsive-md table-responsive-lg table-striped" id="table_logs">
							<thead>
								<tr>
									<th class="text-center" style="width: 50px;">STT</th>
									<th style="min-width:125px">Người dùng</th>
                                    <th style="min-width:125px">Tính năng</th>
                                    <th style="min-width:125px">Hành vi</th>
									<th style="min-width:110px">Thông tin</th>
									<th style="min-width:160px">Dữ liệu truyền lên<i class="fa fa-question-circle ml-1 text-danger" data-toggle="tooltip" data-html="true"  data-placement="top" title="Dữ liệu gửi lên từ API, form điền thông tin"></i></th>
                                    <th style="min-width:110px">Dữ liệu cũ<i class="fa fa-question-circle ml-1 text-danger" data-toggle="tooltip" data-html="true"  data-placement="top" title="Dữ liệu trước khi bị thay đổi"></i></th>
								</tr>
							</thead>
							<tbody>
								@foreach ($log_system as $keyLog => $value)
								<tr>
									<td class="text-center">{{ $keyLog + 1 }}</td>
									<td style="width: 200px; word-break:break-all;">
                                        <p>
                                            @if($value->user_type == 'shop')
                                                <span class="badge badge-primary">
                                                    {{ $value->user_type }}
                                                </span> 
                                                {{ $value->shop()->name }}
                                            @else
                                                <span class="badge badge-primary">
                                                    {{ implode(',', $value->user() ? $value->user()->getRoleNames()->toArray() : ['N/A'] ) }}
                                                </span> 
                                                {{ $value->user() ? $value->user()->name : 'N/A' }}
                                            @endif
                                        </p>
                                        <p>IP: {{ $value->ip }}</p>
                                        <p><i class="cil-av-timer"></i>: {{ date('H:i', strtotime($value->created_at) ) }}</p>
                                        <p>
                                            <i class="cil-devices" data-toggle="tooltip" data-placement="top" title="{{ $value->agent }}"></i>
                                        </p>
                                    </td>
                                    <td >
                                        {{ $value->log_name ? ( \App\Modules\Systems\Constants\SystemLogConstant::log_name[$value->log_name] ??  $value->log_name ) : '' }}
                                    </td>
                                    <td >
                                        {{ $value->description ? ( \App\Modules\Systems\Constants\SystemLogConstant::description[$value->description] ??  $value->description ) : '' }}
                                    </td>
                                    <td >
                                        <p>Phương thức: {{ $value->method }}</p>
                                    </td>
                                    <td style="width: 400px; word-break:break-all;">
                                        @if ($value->request)
                                            @php
                                                $listProduct = [];
                                                $count = 1;
                                                $ary = false;
                                            @endphp
                                            <ul class="log_system{{$keyLog}}">
                                                @foreach( $value->request as $key => $val)
                                                    @if ( is_array($val) )
                                                        @include('Systems::logs.shared.item-children', ['count' => $count, 'key' => $key, 'val' => $val])
                                                        @php $ary = true; @endphp
                                                    @else 
                                                        <li @if( $count > 5) class="hidden-request" @endif >{{$key}}: {{ $val }}</li>
                                                    @endif
                                                    @php $count++; @endphp
                                                @endforeach
                                            </ul>
                                            @if( $count > 6 || $ary)
                                                <a href="javascript:void(0)" onclick="showmore('{{$keyLog}}', this)" >Hiện</a>
                                            @endif
                                        @endif
                                    </td>
                                    <td style="width: 400px; word-break:break-all;">
                                        @if ($value->data)
                                            @foreach( $value->data as $key => $val)
                                                @if ( isset($val['old_data']) )
                                                    <div id="exampleAccordion" data-children=".item">
                                                        <div class="item">
                                                            <a data-toggle="collapse" data-parent="#exampleAccordion" href="#exampleAccordion{{$keyLog}}{{$key}}" aria-expanded="false" aria-controls="exampleAccordion{{$keyLog}}{{$key}}" class="collapsed">
                                                                {{ $val['model'] }}
                                                            </a>
                                                            <div class="collapse" id="exampleAccordion{{$keyLog}}{{$key}}" role="tabpanel" style="">
                                                                <p class="mb-3">
                                                                    <ul>
                                                                        @foreach( $val['old_data'] as $index => $item)
                                                                            @if ( is_array($item) )
                                                                                @include('Systems::logs.shared.item-children', ['key' => $index, 'val' => $item])
                                                                            @else 
                                                                                <li>{{$index}}: {{ $item }}</li>
                                                                            @endif
                                                                        @endforeach
                                                                    </ul>
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @else
                                                    <div>{{ $val['model'] }}</div>
                                                @endif
                                            @endforeach
                                        @endif
                                    </td>
								</tr>
								@endforeach
							</tbody>
							</table>
							{{ $log_system->withQueryString()->links() }}
						</div>
					</div>
                </div>
            </div>
        </div>
    </div>

    @include('Systems::logs.shared.right')

@endsection

@section('javascript')
    <script type="text/javascript" src="{{ asset('libs/moment/moment.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('libs/daterangepicker/daterangepicker.min.js') }}"></script>
    <link rel="stylesheet" type="text/css" href="{{ asset('libs/daterangepicker/daterangepicker.min.css') }}" />

    <script>
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
                if (repo.id === '') return 'Tìm kiếm Shop cần tìm';
                return repo.name || repo.phone;
            }

            let routeApiUser = '{{ route('api.user.find-by-text') }}';
            var $userSelected = $('#userSelected');
            $userSelected.select2({
                theme: "classic",
                placeholder: 'Nhập tên User cần tìm',
                allowClear: true,
                ajax: {
                    delay: 300,
                    url: routeApiUser,
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
                templateResult: formatRepoUser,
                templateSelection: formatRepoSelectionUser,
                language: {
                    inputTooShort: function() {
                        return 'Nhập thông tin tìm kiếm';
                    },
                    noResults: function() { return 'Không có kết quả phù hợp'; }
                }
            });

            function formatRepoUser (repo) {
                if (repo.loading) {
                    return repo.text;
                }

                var $container = $(
                    "<div class='select2-result-repository clearfix'>" +
                    "<div class='select2-result-repository__name'></div>" +
                    "</div>" +
                    "</div>" +
                    "</div>"
                );

                $container.find(".select2-result-repository__name").text('Tên user: '+ repo.text);

                return $container;
            }

            function formatRepoSelectionUser (repo) {
                if (repo.id === '') return 'Tìm kiếm User cần tìm';
                return repo.text;
            }

            $('input[type=radio][name="user_type"]').on('change', function() {
                if (this.value == 'user') {
                    $('.shopSelected').hide();
                    $('.userSelected').show();
                } else {
                    $('.userSelected').hide();
                    $('.shopSelected').show();
                }
            });

        });

        function showmore(key, e) {
            $(`.log_system${key}`).find('.hidden-request').toggle();
            let text = $(e).text();
            if (text == 'Hiện') {
                $(e).text('Ẩn');
            } else {
                $(e).text('Hiện');
            }
        }
    </script>
@endsection
