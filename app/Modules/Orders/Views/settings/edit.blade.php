@extends('layouts.base')

@section ('css')
<style>
    input[type="text"] {
        width: 100%;
    }
    input[type="number"] {
        width: 100%;
    }
    @media only screen and (min-width: 1400px){
        .inputNumber {
            max-width: 160px;
        }
    }
    input::-webkit-outer-spin-button,
    input::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
    }

    input[type=number] {
    -moz-appearance: textfield;
    }
</style>
@endsection
@section('content')
    <div class="container-fluid">
        <div class="animated fadeIn">
        <div class="row">
            <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
            <div class="card">
                <form action="{{ route('admin.order-setting.update') }}" method="POST">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                    <div class="card-header">
                        <i class="fa fa-align-justify"></i>{{ __('Cập nhật biểu phí') }}
                        <button onclick="return checkform()" class="btn btn-primary" style="float:right">Cập nhật</button>
                        <a href="{{ route('admin.order-setting.index') }}" class="btn btn-default" style="float:right">Hủy</a>
                    </div>
                    <div class="card-body table-responsive">
                            <table class="table table-responsive-sm table-responsive-md table-responsive-lg table-striped" id="table1">
                                <thead>
                                    <tr>
                                        <th style="min-width:150px">Tuyến</th>
                                        <th style="min-width: 125px; width:125px">Khối lượng (g)</th>
                                        @foreach( $regions as $value)
                                            <th style="min-width: 120px">{{ $value }} </th>
                                        @endforeach
                                        <th style="min-width: 150px">Thêm {{ $extra }}kg </th>
                                        <th style="min-width: 100px; width: 100px">Ngày giao</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ( $data as $index => $item)
                                        @php
                                            $dataFilter = json_decode($item['result']);
                                            $weightJson = get_object_vars($dataFilter->weight);
                                            $timeJson = get_object_vars($dataFilter->time);
                                            $regionJson = get_object_vars($dataFilter->region);
                                        @endphp
                                        <tr>
                                            <td style="display:none">
                                                <input {{ $item['disable'] == 'on' ? 'checked ' : ' ' }}
                                                type="checkbox" name="show[{{ $item['route'] }}][{{ $item['order_service']['id'] }}]">
                                            </td>
                                            <!-- <td style="display:none">
                                                <div class="input-group input-group-sm">
                                                    @foreach( $regions as $indexRE => $value)
                                                        @foreach ($fee_type as $keyfee => $type )
                                                            <input class="form-control fee-{{ $item['route'] }}-{{ $item['order_service']['id'] }}-{{ $indexRE }}-{{$keyfee}}" value="{{ isset($regionJson[$indexRE]->$keyfee) ? $regionJson[$indexRE]->$keyfee : '' }}" required
                                                            type="number" min="0" type="text"  step="1000"
                                                            {{ $item['disable'] == 'on' ? 'disabled="true"' : '' }}
                                                            name="region[{{ $item['route'] }}][{{ $item['order_service']['id'] }}][{{ $indexRE }}][{{$keyfee}}]" >
                                                        @endforeach
                                                    @endforeach
                                                </div>
                                            </td> -->
                                            <td>
                                                <span class="badge {{ $colors[$item['route']] }}" style="font-size:13px">{{ $routes[$item['route']] }}</span> <br>{{ $item['order_service']['name'] }}
                                            </td>
                                            <td class="row">
                                                <div class="input-group input-group-sm">
                                                    <div class="input-group-append"><span class="input-group-text" style="width: 40px;">Từ</span></div> 
                                                    <input class="weightFrom{{$index}} form-control form-control-sm" autocomplete="off" style="width:45%" type="number" 
                                                    value="{{ $weightJson['from'] }}"  required min="0" step="100"
                                                    {{ $item['disable'] == 'on' ? 'disabled="true"' : '' }}
                                                    name="weight[{{ $item['route'] }}][{{ $item['order_service']['id'] }}][from]">
                                                </div>
                                                <div class="input-group input-group-sm">
                                                    <div class="input-group-append"><span class="input-group-text">Đến</span></div> 
                                                    <input class="weightTo form-control form-control-sm" autocomplete="off" style="width:45%"  type="number" required 
                                                    value="{{ $weightJson['to'] }}" min="0" step="100"
                                                    {{ $item['disable'] == 'on' ? 'disabled="true"' : '' }}
                                                    name="weight[{{ $item['route'] }}][{{ $item['order_service']['id'] }}][to]">
                                                </div>
                                            </td>
                                            @foreach( $regions as $indexRE => $value)
                                            <!-- <td class="inputNumber">
                                                <div class="input-group input-group-sm">
                                                    <input class="form-control sum" value="{{ isset($item[$indexRE]) ? $item[$indexRE] : '' }}" required
                                                    type="number" min="0" type="text" step="1000" 
                                                    data-route="{{$item['route']}}" data-service="{{$item['order_service']['id']}}" data-regions="{{$indexRE}}" 
                                                    {{ $item['disable'] == 'on' ? 'disabled="true"' : '' }}
                                                    name="sum[{{ $item['route'] }}][{{ $item['order_service']['id'] }}][{{ $indexRE }}]">
                                                    <div class="input-group-append"><span class="input-group-text">vnd</span></div> 
                                                </div>
                                            </td> -->
                                            <td class="inputNumber">
                                                @foreach ($fee_type as $keyfee => $type )
                                                    <div class="input-group input-group-sm">
                                                        <input class="form-control fee-{{ $item['route'] }}-{{ $item['order_service']['id'] }}-{{ $indexRE }}-{{$keyfee}}" value="{{ isset($regionJson[$indexRE]->$keyfee) ? $regionJson[$indexRE]->$keyfee : '' }}" required
                                                        type="number" min="0" type="text"  step="100"
                                                        {{ $item['disable'] == 'on' ? 'disabled="true"' : '' }}
                                                        name="region[{{ $item['route'] }}][{{ $item['order_service']['id'] }}][{{ $indexRE }}][{{$keyfee}}]" >
                                                        <div class="input-group-append"><span class="input-group-text">vnd</span></div> 
                                                    </div>
                                                @endforeach
                                            </td>
                                            @endforeach
                                            <td class="inputNumber">
                                                <div class="input-group input-group-sm">
                                                    <input class="form-control" value="{{ $dataFilter->extra }}" required
                                                    type="number" min="0"
                                                    {{ $item['disable'] == 'on' ? 'disabled="true"' : '' }}
                                                    name="extra[{{ $item['route'] }}][{{ $item['order_service']['id'] }}]" >
                                                    <div class="input-group-append"><span class="input-group-text">vnd</span></div> 
                                                </div>
                                            </td>
                                            <td class="row">
                                                <div class="input-group input-group-sm">
                                                    <div class="input-group-append"><span class="input-group-text" style="width: 40px;">Từ</span></div> 
                                                    <input class="timeFrom{{$index}} form-control form-control-sm" autocomplete="off" style="width:45%" type="number" 
                                                    value="{{ $timeJson['from'] }}"  required min="0"
                                                    {{ $item['disable'] == 'on' ? 'disabled="true"' : '' }}
                                                    name="time[{{ $item['route'] }}][{{ $item['order_service']['id'] }}][from]">
                                                </div>
                                                <div class="input-group input-group-sm">
                                                    <div class="input-group-append"><span class="input-group-text">Đến</span></div> 
                                                    <input class="timeTo form-control form-control-sm" autocomplete="off" style="width:45%"  type="number" required 
                                                    value="{{ $timeJson['to'] }}" min="0"
                                                    {{ $item['disable'] == 'on' ? 'disabled="true"' : '' }}
                                                    name="time[{{ $item['route'] }}][{{ $item['order_service']['id'] }}][to]">
                                                </div>
                                            </td>
                                            <td colspan="9">
                                                @if( $item['disable'] == 'on')
                                                    <button style="display:none" type="button" class="btn btn-primary float-right" title="Ẩn" onclick="handleToogle({{ $index }},'on', this)">
                                                        <i class="cil-low-vision"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-secondary float-right" title="Hiện" onclick="handleToogle({{ $index }}, 'off', this)">
                                                        <i class="cil-low-vision"></i>
                                                    </button>
                                                @else
                                                    <button type="button" class="btn btn-primary float-right" title="Ẩn" onclick="handleToogle({{ $index }},'on', this)">
                                                        <i class="cil-low-vision"></i>
                                                    </button>
                                                    <button style="display:none" type="button" class="btn btn-secondary float-right" title="Hiện" onclick="handleToogle({{ $index }}, 'off', this)">
                                                        <i class="cil-low-vision"></i>
                                                    </button>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <!-- Modal thêm mới vùng -->
    <!-- <div class="modal fade" id="sumModal" tabindex="-1" role="dialog" aria-labelledby="sumModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Nhập cước phí</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body row">
                    <div class="col-md-4">
                        <label for="">Phí vận chuyển: </label>
                    </div>
                    <div class="col-md-8">
                        <div class="input-group input-group-sm">    
                            <input autofocus onkeyup="sumFee()" id="fee_send" class="form-control" type="number" min="0" placeholder="Phí vận chuyển" name="fee_send" step="1000" maxlength="255" required>
                            <div class="input-group-append"><span class="input-group-text">vnd</span></div> 
                        </div>
                    </div>
                    <div class="col-md-4" style="margin-top:10px">
                        <label for="">Phí lấy hàng: </label>
                    </div>
                    <div class="col-md-8" style="margin-top:10px">
                        <div class="input-group input-group-sm">
                            <input id="fee_pick" onkeyu9p="sumFee()" class="form-control" type="number" min="0" placeholder="Phí lấy hàng" name="fee_pick" maxlength="255" step="1000" required>
                            <div class="input-group-append"><span class="input-group-text">vnd</span></div> 
                        </div>
                    </div>
                    <div class="col-md-4" style="margin-top:10px">
                        <label for="">Phí chuyển tiếp: </label>
                    </div>
                    <div class="col-md-8" style="margin-top:10px">
                        <div class="input-group input-group-sm">
                            <input id="fee_forward" onkeyup="sumFee()" class="form-control" type="number" min="0" placeholder="Phí chuyển tiếp" name="fee_forward" maxlength="255" step="1000" required>
                            <div class="input-group-append"><span class="input-group-text">vnd</span></div> 
                        </div>
                    </div>
                    <div class="col-md-4" style="margin-top:10px">
                        <label for="">Tổng: </label>
                    </div>
                    <div class="col-md-8" style="margin-top:10px">
                        <div class="input-group input-group-sm">
                            <input id="modalSum" class="form-control" type="number" min="0" placeholder="Tổng" name="modalSum" maxlength="255" readonly>
                            <div class="input-group-append"><span class="input-group-text">vnd</span></div> 
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary closedModal" data-dismiss="modal">Hủy</button>
                    <button class="btn btn-primary" onclick="submitSum()">Cập nhật</button>
                </div>
			</div>
		</div>
	</div> -->
@endsection

@section('javascript')
    <script>
        function handleToogle(key, type, e) {
            $(e).css('display', 'none');
            $('input[type="checkbox"]').each(function(index, item) {
                if ( key == index) {
                    if ( type == 'on') {
                        $(item).prop('checked', true);
                        $(e).parent().children('.btn-secondary').css('display', 'block');
                    } else {
                        $(item).prop('checked', false);
                        $(e).parent().children('.btn-primary').css('display', 'block');
                    }
                    return false;
                }
            });

            let numColSpan = 0;
            if ( type == 'on') {
                numColSpan= 9;
            }
            $(e).parent().attr('colspan', numColSpan);
            $(e).parent().parent().children('td').each(function(index, item) {
                if (index < 9 && index > 0 && type == 'on') {
                    $(item).children('input:not([type="checkbox"])').prop('disabled', true);
                    $(item).children().children('input').prop('disabled', true);
                }
                if (index < 9 && index > 0 && type == 'off') {
                    $(item).children('input:not([type="checkbox"])').prop('disabled', false);
                    $(item).children().children('input').prop('disabled', false);
                }
            });
        }
        function checkform() {
            $('.weightTo').each(function (index, item) {
                item.setCustomValidity('');
                if (item.value == '') return true;
                if ( +item.value <= +$(`.weightFrom${index}`).val() ) item.setCustomValidity('Số sau phải lớn hơn số vndằng trước');
            });
            $('.timeTo').each(function (index, item) {
                item.setCustomValidity('');
                if (item.value == '') return true;
                if ( +item.value <= +$(`.timeFrom${index}`).val() ) item.setCustomValidity('Số sau phải lớn hơn số vndằng trước');
            });
            return true;
        }
        
        let routeId = 0;
        let serviceId = 0;
        let regionsId = 0;
        let fee_send = 0;
        let fee_pick = 0;
        let fee_forward = 0;
        let modalSum = 0;
        let target = null;
        // $('.sum').click(function(e) {
        //     target = e.target;
        //     filterFee(e.target);
        // });
        // $('.sum').keyup(function(e) {
        //     e.preventDefault();
        //     filterFee(e.target);
        // });
        // $('.sum').select(function(e){
        //     let attr = $(this).attr('disabled');
        //     if (typeof attr === typeof undefined)  filterFee(e.target);
        // })

        // function filterFee(e) {
        //     routeId = $(e).data('route');
        //     serviceId = $(e).data('service');
        //     regionsId = $(e).data('regions');
        //     fee_send = $(`.fee-${routeId}-${serviceId}-${regionsId}-fee_send`).val();
        //     fee_pick = $(`.fee-${routeId}-${serviceId}-${regionsId}-fee_pick`).val();
        //     fee_forward = $(`.fee-${routeId}-${serviceId}-${regionsId}-fee_forward`).val();
        //     modalSum = parseInt(fee_send) + parseInt(fee_pick) + parseInt(fee_forward);
        //     $('#fee_send').val(fee_send);
        //     $('#fee_pick').val(fee_pick);
        //     $('#fee_forward').val(fee_forward);
        //     $('#modalSum').val(modalSum);
        //     $('#sumModal').modal('show');
        // }

        function sumFee() {
            fee_send =  $('#fee_send').val();
            fee_pick = $('#fee_pick').val();
            fee_forward = $('#fee_forward').val();
            modalSum = parseInt(fee_send) + parseInt(fee_pick) + parseInt(fee_forward);
            $('#modalSum').val(modalSum);
        }

        // function submitSum() {
        //     fee_send = $('#fee_send').val();
        //     fee_pick = $('#fee_pick').val();
        //     fee_forward = $('#fee_forward').val();
        //     $(`.fee-${routeId}-${serviceId}-${regionsId}-fee_send`).val(fee_send);
        //     $(`.fee-${routeId}-${serviceId}-${regionsId}-fee_pick`).val(fee_pick);
        //     $(`.fee-${routeId}-${serviceId}-${regionsId}-fee_forward`).val(fee_forward);
        //     modalSum = parseInt(fee_send) + parseInt(fee_pick) + parseInt(fee_forward);
        //     $(target).val(modalSum);
        //     $('#sumModal').modal('hide');
        // }
    </script>
@endsection