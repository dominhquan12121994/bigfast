@extends('layouts.base')

@section ('css')

@endsection
@section('content')
    <div class="container-fluid">
        <div class="animated fadeIn">
        <div class="row">
            <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
            <div class="card">
                <div class="card-header">
                    <i class="fa fa-align-justify"></i> {{ __('Biểu phí') }} /
                    <a href="{{ route('admin.cod.index') }}">COD</a>
                    @if($currentUser->can('action_order_settings_fee_update'))
                        <a href="{{ route('admin.order-setting.edit') }}" class="btn btn-primary" style="float:right">Cập nhật biểu phí</a>
                    @endif
                </div>
                <div class="card-body table-responsive">
                    <table class="table table-responsive-sm table-responsive-md table-responsive-lg table-striped" id="table1">
                        <thead>
                            <tr>
                                <th>Tuyến</th>
                                <th style="min-width: 100px">Gói cước</th>
                                <th style="min-width: 100px">Khối lượng</th>
                                @foreach( $regions as $value)
                                    <th style="min-width: 120px">{{ $value }}</th>
                                @endforeach
                                <th style="min-width: 150px">Thêm {{ $extra }} kg<i data-toggle="tooltip" html="true" title="Với mỗi {{ $extra }} kg tăng thêm, giá cước sẽ tăng thêm giá trị khai báo trong biểu phí" class="fa fa-question-circle ml-1 text-danger"></i></th>
                                <th style="min-width: 150px">Thời gian giao</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ( $orderSettings as $key => $item)
                                @php
                                    $dataJson = json_decode($item->result, true);
                                    $weightJson = $dataJson['weight'];
                                    $timeJson = $dataJson['time'];
                                    $regionJson = $dataJson['region'];
                                @endphp
                                @if ( $item->disable !== 'on' && $item->orderService->status == 1 )
                                    <tr>
                                        <td><span class="badge {{ $colors[$item['route']] }}" style="font-size:13px">{{ $routes[$item->route] }}</span></td>
                                        <td>{{ $item->orderService->name }}</td>
                                        <td>
                                            {{ $weightJson['from'] }} -
                                            {{ $weightJson['to'] . ' g'}}
                                        </td>
                                        @foreach( $regions as $index => $value)
                                            <td>{{ isset($regionJson[$index]) ? number_format(array_sum($regionJson[$index])) . ' vnd' : '' }}</td>
                                        @endforeach
                                        <td>{{ number_format($dataJson['extra'])  . ' vnd'}}</td>
                                        <td>
                                            {{ $timeJson['from'] }} -
                                            {{ $timeJson['to'] . ' ngày'}}
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('javascript')
    <script type="text/javascript" src="{{ asset('js/tooltips.js') }}"></script>

@endsection
