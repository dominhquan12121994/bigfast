@extends('layouts.base')

@section('css')
    <link href="{{ asset('libs/datatables/jquery.dataTables.min.css') }}" rel="stylesheet">
    <style type="text/css">
        select + .select2-container {
            width: 200px !important;
        }

        @media screen and (max-width: 575px) {
            .sepa_icon {
                display: none;
            }
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="animated fadeIn">
            <div class="row">
                <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                    <div class="card">
                        <div class="card-header d-block d-sm-flex justify-content-between">
                            <div class="flex">
                                <i class="fa fa-align-justify"></i>
								<b>Gán Ship theo khu vực</b>
								&nbsp;<span class="sepa_icon">|</span>&nbsp;
								<a href="{{ route('admin.assign-ship.scan-barcode' ) }}">Quét mã vạch vận đơn</a>
                            </div>
                            <div class="flex text-right">
                                @php
                                    $paramPick = array('type' => 'pickup');
                                    $paramSend = array('type' => 'shipper');
                                    $paramRefund = array('type' => 'refund');
                                    if ($shop) {
                                        $paramPick['shop'] = $paramSend['shop'] = $paramRefund['shop'] = $shop->id;
                                    }
                                @endphp
                                @if ($type == 'pickup')
                                    <span class="my-2 my-sm-0 w-100 text-center text-sm-right d-block d-sm-inline">
										&nbsp;<b>Gán ship lấy hàng</b>&nbsp;<span class="sepa_icon">|</span>&nbsp;</span>
                                @else
                                    <a class="my-2 my-sm-0 w-100 text-center text-sm-right d-block d-sm-inline"
                                       href="{{ route('admin.assign-ship.show', $paramPick ) }}">Gán ship lấy hàng&nbsp;<span
                                                class="sepa_icon">|</span>&nbsp;</a>
                                @endif
                                @if ($type == 'shipper')
                                    <span class="my-2 my-sm-0 w-100 text-center text-sm-right d-block d-sm-inline">
										&nbsp;<b>Gán ship giao hàng</b>&nbsp;<span class="sepa_icon">|</span>&nbsp;</span>
                                @else
                                    <a class="my-2 my-sm-0 w-100 text-center text-sm-right d-block d-sm-inline"
                                       href="{{ route('admin.assign-ship.show', $paramSend ) }}"> Gán ship giao hàng&nbsp;<span
                                                class="sepa_icon">|</span>&nbsp;</a>
                                @endif
                                @if ($type == 'refund')
                                    <span class="my-2 my-sm-0 w-100 text-center text-sm-right d-block d-sm-inline">
										&nbsp;<b>Gán ship chuyển hoàn</b></span>
                                @else
                                    <a class="my-2 my-sm-0 w-100 text-center text-sm-right d-block d-sm-inline"
                                       href="{{ route('admin.assign-ship.show', $paramRefund ) }}"> Gán ship chuyển hoàn</a>
                                @endif
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-auto mr-auto">
                                    @if ($shop)
                                        <span class="badge badge-success ">Shop</span> {!! $shop->name !!}
                                        - {{ $shop->phone }}<br>
                                        {{ $shop->address }}
                                    @else
                                        <form class="form-inline" id="frm_filter_orders" action="" method="get"
                                              autocomplete="off">
                                            <label class="mr-2">
                                                <i class="c-icon c-icon-xl cil-list-filter"></i>
                                            </label>

                                            <div class="form-group mr-3">
                                                <label class="mr-1" for="add-select1">Tỉnh/thành</label>
                                                <select class="form-control frm-select2 form-control-sm"
                                                        id="add-select1" name="filter_province"
                                                        onchange="changeProvinces('add')">
                                                    <option value="0" selected>Tất cả</option>
                                                    @foreach ($provinces as $province)
                                                        <option value="{{ $province->id }}" {{ (old('province') == $province->id) ? 'selected="selected"' : '' }}>{{ $province->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group mr-3 filter_district"
                                                 @if( old('province') ==0 ) style="display:none;" @endif>
                                                <label class="mr-1" for="add-select2">Quận/huyện</label>
                                                <select class="form-control frm-select2 form-control-sm"
                                                        id="add-select2" name="filter_district"
                                                        onchange="changeDistricts('add')">
                                                    <option value="0" selected>Tất cả</option>
                                                    @foreach ($districts as $district)
                                                        <option value="{{ $district->id }}" {{ (old('district') == $district->id) ? 'selected="selected"' : '' }}>{{ $district->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group mr-1 filter_ward"
                                                 @if( old('district') == 0) style="display:none;" @endif>
                                                <label class="mr-1" for="add-select3">Phường/xã</label>
                                                <select class="form-control frm-select2 form-control-sm"
                                                        id="add-select3" name="filter_ward">
                                                    <option value="0" selected>Tất cả</option>
                                                    @foreach ($wards as $ward)
                                                        <option value="{{ $ward->id }}" {{ (old('ward') == $ward->id) ? 'selected="selected"' : '' }}>{{ $ward->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <button class="btn btn-sm btn-info" type="button"
                                                    onclick="submitFilterOrders()">Tìm kiếm
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>

                            <div>
                                <table class="table table-responsive-sm table-responsive-md table-responsive-lg table-striped"
                                       id="table_orders">
                                    <thead>
                                    <tr>
                                        <th class="pr-0">
                                            <input type="checkbox" id="selectall"/>
                                        </th>
                                        @if (!$shop)
											<th>Shop</th>
                                        @endif
                                        @if( $type == 'refund')
                                            <th>Người nhận hoàn</th>
                                            <th>Nơi hoàn hàng</th>
                                            <th class="text-right">Số đơn hoàn</th>
                                        @elseif( $type == 'shipper')
                                            <th>Người nhận hàng</th>
                                            <th>Nơi nhận hàng</th>
                                            <th class="text-right">Số đơn giao</th>
                                        @elseif( $type == 'pickup')
                                            <th>Người gửi hàng</th>
                                            <th>Nơi gửi hàng</th>
                                            <th class="text-right">Số đơn gửi</th>
                                        @endif
                                        <th class="text-right">Tổng trọng lượng</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($arrData as $key => $item)
                                        <tr>
                                            @if( $type == 'refund')
												<td class="pr-0">
													<input type="checkbox" class="singlechkbox"
														   @if( !$item->shop->address || !$item->refund->address ) disabled
														   @endif
														   id="singlechkbox{{ $key }}"
														   name="cbx_address_id[]"
														   value="{{ implode(',', $item->orderIds) }}"/>
												</td>
												@if (!$shop)
													<td>
														<label for="singlechkbox{{ $key }}">
															@if( !$item->shop->address || !$item->refund->address )
																<a href="{{ route('admin.shops.edit', $item->shop->id ) }}">{!! $item->shop->name !!}</a>
																<i class="text-danger cil-warning" data-toggle="tooltip"
																   data-placement="top"
																   title="Bạn cần cập nhật đầy đủ thông tin của shop trước khi sử dụng dịch vụ của chúng tôi"></i>
															@else
																{!! $item->shop->name !!}
															@endif
														</label>
													</td>
												@endif
												<td>
													<span class="badge badge-info">{{ $item->refund->phone }}</span> {{ $item->refund->name }}
												</td>
                                                <td>
                                                    {{ $item->refund->address }}
                                                    @if( old('ward') == 0 && !$shop)
                                                        <a class="badge badge-secondary "
                                                           href="{{ route('admin.assign-ship.show')
                                                    .'?province='.$item->refund->provinces->id
                                                    .'&district='.$item->refund->districts->id
                                                    .'&ward='.$item->refund->wards->id
                                                    .'&type='.$type
                                                    }}"
                                                        >
                                                            {{ $item->refund->wards->name }}
                                                        </a>
                                                        <a class="badge badge-secondary "
                                                           href="{{ route('admin.assign-ship.show')
                                                    .'?province='.$item->refund->provinces->id
                                                    .'&district='.$item->refund->districts->id
                                                    .'&ward=0'
                                                    .'&type='.$type
                                                    }}"
                                                        >
                                                            {{ $item->refund->districts->name }}
                                                        </a>
                                                        <a class="badge badge-secondary "
                                                           href="{{ route('admin.assign-ship.show')
                                                    .'?province='.$item->refund->provinces->id
                                                    .'&district=0'
                                                    .'&ward=0'
                                                    .'&type='.$type
                                                    }}"
                                                        >
                                                            {{ $item->refund->provinces->name }}
                                                        </a>
                                                    @endif
                                                </td>
                                            @elseif( $type == 'pickup')
												<td class="pr-0">
													<input type="checkbox" class="singlechkbox"
														   @if( !$item->shop->address || !$item->sender->address ) disabled
														   @endif
														   id="singlechkbox{{ $key }}"
														   name="cbx_address_id[]"
														   value="{{ implode(',', $item->orderIds) }}"/>
												</td>
												@if (!$shop)
													<td>
														<label for="singlechkbox{{ $key }}">
															@if( !$item->shop->address || !$item->sender->address )
																<a href="{{ route('admin.shops.edit', $item->shop->id ) }}">{!! $item->shop->name !!}</a>
																<i class="text-danger cil-warning" data-toggle="tooltip"
																   data-placement="top"
																   title="Bạn cần cập nhật đầy đủ thông tin của shop trước khi sử dụng dịch vụ của chúng tôi"></i>
															@else
																{!! $item->shop->name !!}
															@endif
														</label>
													</td>
												@endif
												<td>
													<span class="badge badge-info">{{ $item->sender->phone }}</span> {{ $item->sender->name }}
												</td>
                                                <td>
                                                    {{ $item->sender->address }}
                                                    @if( old('ward') == 0 && !$shop)
                                                        <a class="badge badge-secondary "
                                                           href="{{ route('admin.assign-ship.show')
                                                    .'?province='.$item->sender->provinces->id
                                                    .'&district='.$item->sender->districts->id
                                                    .'&ward='.$item->sender->wards->id
                                                    .'&type='.$type
                                                    }}"
                                                        >
                                                            {{ $item->sender->wards->name }}
                                                        </a>
                                                        <a class="badge badge-secondary "
                                                           href="{{ route('admin.assign-ship.show')
                                                    .'?province='.$item->sender->provinces->id
                                                    .'&district='.$item->sender->districts->id
                                                    .'&ward=0'
                                                    .'&type='.$type
                                                    }}"
                                                        >
                                                            {{ $item->sender->districts->name }}
                                                        </a>
                                                        <a class="badge badge-secondary "
                                                           href="{{ route('admin.assign-ship.show')
                                                    .'?province='.$item->sender->provinces->id
                                                    .'&district=0'
                                                    .'&ward=0'
                                                    .'&type='.$type
                                                    }}"
                                                        >
                                                            {{ $item->sender->provinces->name }}
                                                        </a>
                                                    @endif
                                                </td>
                                            @elseif ($type == 'shipper')
												<td class="pr-0">
													<input type="checkbox" class="singlechkbox"
														   @if( !$item->shop->address || !$item->receiver->address ) disabled
														   @endif
														   id="singlechkbox{{ $key }}"
														   name="cbx_address_id[]"
														   value="{{ implode(',', $item->orderIds) }}"/>
												</td>
												@if (!$shop)
													<td>
														<label for="singlechkbox{{ $key }}">
															@if( !$item->shop->address || !$item->receiver->address )
																<a href="{{ route('admin.shops.edit', $item->shop->id ) }}">{!! $item->shop->name !!}</a>
																<i class="text-danger cil-warning" data-toggle="tooltip"
																   data-placement="top"
																   title="Bạn cần cập nhật đầy đủ thông tin của shop trước khi sử dụng dịch vụ của chúng tôi"></i>
															@else
																{!! $item->shop->name !!}
															@endif
														</label>
													</td>
												@endif
												<td>
													<span class="badge badge-info">{{ $item->receiver->phone }}</span> {{ $item->receiver->name }}
												</td>
                                                <td>
                                                    {{ $item->receiver->address }}
                                                    @if( old('ward') == 0 && !$shop)
                                                        <a class="badge badge-secondary "
                                                           href="{{ route('admin.assign-ship.show')
                                                    .'?province='.$item->receiver->p_id
                                                    .'&district='.$item->receiver->d_id
                                                    .'&ward='.$item->receiver->w_id
                                                    .'&type='.$type
                                                    }}"
                                                        >
                                                            {{ $item->receiver->wards->name }}
                                                        </a>
                                                        <a class="badge badge-secondary "
                                                           href="{{ route('admin.assign-ship.show')
                                                    .'?province='.$item->receiver->p_id
                                                    .'&district='.$item->receiver->d_id
                                                    .'&ward=0'
                                                    .'&type='.$type
                                                    }}"
                                                        >
                                                            {{ $item->receiver->districts->name }}
                                                        </a>
                                                        <a class="badge badge-secondary "
                                                           href="{{ route('admin.assign-ship.show')
                                                    .'?province='.$item->receiver->p_id
                                                    .'&district=0'
                                                    .'&ward=0'
                                                    .'&type='.$type
                                                    }}"
                                                        >
                                                            {{ $item->receiver->provinces->name }}
                                                        </a>
                                                    @endif
                                                </td>
                                            @endif
                                            <td class="text-right">
                                                {{ number_format($item->countOrder) }}
                                            </td>
                                            <td class="text-right">
                                                {{ round($item->totalWeight / 1000, 3) }} kg
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="row">
                                <div class="col-auto mr-auto">
                                    <div id="boxActions" class="mt-2" style="display: none">
                                        <div class="d-flex">
                                            <div class="flex-grow-1 mr-5">
                                                <b>Đã chọn</b><br>
                                                <b id="countOrderSelected"
                                                   style="font-size: 30px; color: orangered">0</b> đơn hàng
                                            </div>
                                            <div class="d-flex align-items-end">
                                                <div>
                                                    <button class="btn btn-sm btn-pill btn-info mr-2" type="button"
                                                            data-toggle="modal" data-target="#orderActionModal">Thao tác
                                                        đơn hàng được chọn
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-auto">

                                </div>
                            </div>

                            <div class="modal fade" id="orderActionModal" tabindex="-1" role="dialog"
                                 aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static"
                                 data-keyboard="false">
                                <div class="modal-dialog modal-info modal-lg" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h4 class="modal-title">Thao tác <b id="countOrderSelectedModal"
                                                                                style="color: orangered">0</b> đơn hàng
                                                được chọn</h4>
                                            <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">×</span></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="nav-tabs-boxed nav-tabs-boxed-left">
                                                <ul class="nav nav-tabs" role="tablist" style="width: 20%">
                                                    <li class="nav-item">
                                                        <a class="nav-link active" data-toggle="tab" href="#tab1"
                                                           role="tab" aria-controls="tab1" aria-selected="true">
                                                            {{ array(
                                                                    'pickup' => 'Gán ship lấy hàng',
                                                                    'shipper' => 'Gán ship giao hàng',
                                                                    'refund' => 'Gán ship chuyển hoàn'
                                                                    )[$type] }}
                                                        </a></li>
                                                </ul>
                                                <div class="tab-content" style="width: 80%">
                                                    <div class="tab-pane active" id="tab1" role="tabpanel">
                                                        <div class="row justify-content-md-center">
                                                            <div class="col-10">
                                                                @if($type == 'pickup')
                                                                    @include('Orders::orders.modal.assign-pickup-by-zone')
                                                                @endif
                                                                @if($type == 'shipper')
                                                                    @include('Orders::orders.modal.assign-shipper-by-zone')
                                                                @endif
                                                                @if($type == 'refund')
                                                                    @include('Orders::orders.modal.assign-refund-by-zone')
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- /.modal-content-->
                                </div>
                                <!-- /.modal-dialog-->
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('javascript')
    <script src="{{ asset('libs/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('js/tooltips.js') }}"></script>

    <script type="application/javascript">
		let arrOrder = [];
		let arrShopAddress = [];
		let lastChecked = null;
		let $chkboxes = $('.singlechkbox');
		let user_id = '{{ $userId }}';
		let user_type = '{{ $userType }}';

		$(document).ready(function () {
			$('#table_orders').DataTable({
				"columnDefs": [
					{ "orderable": false, "targets": 0 }
				],
				"language": {
					"lengthMenu": "",
					"zeroRecords": "Không tìm thấy dữ liệu",
					"info": "",
					"infoEmpty": "Không tìm thấy dữ liệu",
					"infoFiltered": "(tìm kiếm trong tổng số _MAX_ bản ghi)",
					"decimal": "",
					"emptyTable": "Không tìm thấy dữ liệu",
					"infoPostFix": "",
					"thousands": ",",
					"loadingRecords": "Đang tải...",
					"processing": "Đang tải...",
					"search": "Tìm kiếm:",
					"paginate": {
						"first": "Đầu",
						"last": "Cuối",
						"next": "Sau",
						"previous": "Trước"
					},
					"aria": {
						"sortAscending": ": xếp tăng dần",
						"sortDescending": ": xếp giảm dần"
					}
				},
                stateSave: true,
			});

			$("#frm_select_shipper_pick").on('submit', function (e) {
				e.preventDefault();
				//
				var select_shipper = document.getElementById("select_shipper").value;
				let data = {
					"user_id": user_id,
					"user_type": user_type,
					"status_detail": 12,
					"select_shipper": select_shipper,
					"orders": arrOrder
				}
				$.ajax({
					type: 'PUT',
					beforeSend: function (xhr) {
						xhr.setRequestHeader("Authorization", "Bearer {{ $currentUser->passport_token }}");
					},
					url: '{{ route('api.orders.update-status-order') }}',
					contentType: 'application/json',
					data: JSON.stringify(data), // access in body
				}).done(function () {
					$.Toast("Thành công", "Update trạng thái thành công!", "notice");
					setTimeout(function () {
						location.reload()
					}, 1000);
				}).fail(function (msg) {
					$.Toast("Thất bại", "Update trạng thái thất bại!", "error");
				});
			});

			$("#frm_select_shipper_refund").on('submit', function (e) {
				e.preventDefault();
				//
				var select_shipper = document.getElementById("select_shipper").value;
				let data = {
					"user_id": user_id,
					"user_type": user_type,
					"status_detail": 32,
					"select_shipper": select_shipper,
					"orders": arrOrder
				}
				$.ajax({
					type: 'PUT',
					beforeSend: function (xhr) {
						xhr.setRequestHeader("Authorization", "Bearer {{ $currentUser->passport_token }}");
					},
					url: '{{ route('api.orders.update-status-order') }}',
					contentType: 'application/json',
					data: JSON.stringify(data), // access in body
				}).done(function () {
					$.Toast("Thành công", "Update trạng thái thành công!", "notice");
					setTimeout(function () {
						location.reload()
					}, 1000);
				}).fail(function (msg) {
					$.Toast("Thất bại", "Update trạng thái thất bại!", "error");
				});
			});

			$("#frm_select_shipper_send").on('submit', function (e) {
				e.preventDefault();
				//
				var select_shipper = document.getElementById("select_shipper").value;
				let data = {
					"user_id": user_id,
					"user_type": user_type,
					"status_detail": 23,
					"select_shipper": select_shipper,
					"orders": arrOrder
				}
				$.ajax({
					type: 'PUT',
					beforeSend: function (xhr) {
						xhr.setRequestHeader("Authorization", "Bearer {{ $currentUser->passport_token }}");
					},
					url: '{{ route('api.orders.update-status-order') }}',
					contentType: 'application/json',
					data: JSON.stringify(data), // access in body
				}).done(function () {
					$.Toast("Thành công", "Update trạng thái thành công!", "notice");
					setTimeout(function () {
						location.reload()
					}, 1000);
				}).fail(function (msg) {
					$.Toast("Thất bại", "Update trạng thái thất bại!", "error");
				});
			});
		});

		jQuery(function ($) {
			$('body').on('click', '#selectall', function () {
				let checked = this.checked;
				$('#table_orders input').each(
					function (index) {
						var input = $(this);
						if (input.attr('class') === 'singlechkbox') {
							// logicCookieOrder(checked, input.val());
							logicOrderChecked(checked, input.val());
						}
					});
				$('.singlechkbox').prop('checked', this.checked);
				if (this.checked) {
					window.scrollTo(0, document.body.scrollHeight);
				}
			}).on('click', '.singlechkbox', function (e) {
				let input = $(this);
				let checked = input.is(':checked');
				// logicCookieOrder(checked, input.val());
				logicOrderChecked(checked, input.val());
				if ($('.singlechkbox').length == $('.singlechkbox:checked').length) {
					$('#selectall').prop('checked', true);
				} else {
					$("#selectall").prop('checked', false);
				}

				// Checked by Shift
				if (!lastChecked) {
					lastChecked = this;
					return;
				}
				// if (e.shiftKey) {
				//     var start = $chkboxes.index(this);
				//     var end = $chkboxes.index(lastChecked);
				//
				//     $chkboxes.slice(Math.min(start,end), Math.max(start,end)+ 1).prop('checked', lastChecked.checked);
				// }
				lastChecked = this;
			});

			$('#orderActionModal').on('show.coreui.modal', function (e) {
				let routeApi = `{{ route('api.user.find-by-roles', array('roles' => $arrRoleAssign[$type])) }}`;
				$.ajax({
					beforeSend: function (xhr) {
						xhr.setRequestHeader("Authorization", "Bearer {{ $currentUser->passport_token }}");
					},
					url: routeApi,
					success: function (response) {
						if (response.status_code === 200) {
							let html = '';
							response.data.forEach(function (item) {
								html += '<option value="' + item.id + '">' + item.name + ' (' + item.email + ')</option>';
							});
							document.getElementById("select_shipper").innerHTML = html;
						}
					}
				});
			});
		});

		function logicOrderChecked(checked = false, orderIds = '') {
			let arrOrderId = orderIds.split(',');
			if (arrOrderId.length > 0) {
				arrOrderId.forEach(function (order_id) {
					if (checked) {
						if (!arrOrder.includes(order_id)) {
							arrOrder.push(order_id);
						}
					} else {
						if (arrOrder.includes(order_id)) {
							const index = arrOrder.indexOf(order_id);
							if (index > -1) {
								arrOrder.splice(index, 1);
							}
						}
					}
					document.getElementById("countOrderSelected").innerHTML = arrOrder.length;
					document.getElementById("countOrderSelectedModal").innerHTML = arrOrder.length;

					if (arrOrder.length > 0)
						$("#boxActions").fadeIn();
					else
						$("#boxActions").fadeOut();
				})
			}
		}

		function changeProvinces(randTxt) {
			let provinceID = document.getElementById(randTxt + '-select1').value;
			if (provinceID == 0) {
				$('.filter_district').hide();
				$('.filter_ward').hide();
				$('#add-select2').val(0);
				$('#add-select3').val(0);
				return;
			}
			let routeApi = '{{ route('api.districts.get-by-province', ":slug") }}';
			routeApi = routeApi.replace(':slug', provinceID);

			$.ajax({
				beforeSend: function (xhr) {
					xhr.setRequestHeader("Authorization", "Bearer {{ $currentUser->passport_token }}");
				},
				url: routeApi,
				success: function (response) {
					if (response.status_code === 200) {
						let html = '<option value="0">Tất cả</option>';
						response.data.forEach(function (item) {
							html += '<option value="' + item.id + '">' + item.name + '</option>';
						});
						document.getElementById(randTxt + "-select2").innerHTML = html;
						$('.filter_district').show();
						changeDistricts(randTxt);
					}
				}
			});
		}

		function changeDistricts(randTxt) {
			let districtID = document.getElementById(randTxt + '-select2').value;
			if (districtID == 0) {
				$('.filter_ward').hide();
				$('#add-select3').val(0);
				return;
			}
			let routeApi = '{{ route('api.wards.get-by-district', ":slug") }}';
			routeApi = routeApi.replace(':slug', districtID);

			$.ajax({
				beforeSend: function (xhr) {
					xhr.setRequestHeader("Authorization", "Bearer {{ $currentUser->passport_token }}");
				},
				url: routeApi,
				success: function (response) {
					if (response.status_code === 200) {
						let html = '<option value="0">Tất cả</option>';
						response.data.forEach(function (item) {
							html += '<option value="' + item.id + '">' + item.name + '</option>';
						});
						document.getElementById(randTxt + "-select3").innerHTML = html;
						$('.filter_ward').show();
					}
				}
			});
		}

		function submitFilterOrders() {
			// sk-rect, sk-dot, sk-cube, sk-bounce, sk-circle
			HoldOn.open({theme: "sk-rect"});
			let route = '{{ route('admin.assign-ship.show') }}';
			let newParams = '';
			$('#frm_filter_orders select').each(
				function (index) {
					let input = $(this);
					if (input.attr('name') === 'filter_province') {
						newParams += '&province=' + input.val();
					}
					if (input.attr('name') === 'filter_district') {
						newParams += '&district=' + input.val();
					}
					if (input.attr('name') === 'filter_ward') {
						newParams += '&ward=' + input.val();
					}
				});
			window.location = reFormatUriParam(route, newParams);
		}

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
    </script>
@endsection
