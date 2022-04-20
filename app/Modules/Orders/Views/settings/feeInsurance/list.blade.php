@extends('layouts.base')

@section('css')
	<style>
	.alert-popup-close-icon {
		position: absolute;
		top: -30px;
		color: #fff;
		font-size: 24px;
		font-weight: 900;
		right: 0;
		cursor: pointer;
	}
	.center {
		position: absolute;
		left: 40%;
		top: 43%;
		transform: translate(-50%, -50%);
	}
	.width100 {
		width: 100px;
	}
	.badge {
		color: #fff;
	}
	.cil-short-text {
		cursor: pointer;
	}
	.min-max {
		width: 80px;
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
							<i class="fa fa-align-justify"></i>{{ __('Cấu hình mức phí bảo hiểm') }}
							@if($currentUser->can('action_order_settings_insurance_create'))
								<button type="button" class="btn btn-primary float-right my-2 my-sm-0" data-toggle="modal" data-target="#crudModal">
									Thêm mới mức phí
								</button>
							@endif
						</div>
						<div class="card-body">
							<div class="row">
								<div class="col-12">
								<div class="tab-content" id="nav-tabContent">
									<div class="tab-pane fade active show table-responsive" role="tabpanel" aria-labelledby="list-contacts-list">
										<table class="table table-striped" id="table1">
											<thead>
												<tr>
													<th>Hạn mức<i class="fa fa-question-circle ml-1 text-danger" data-toggle="tooltipNew" data-html="true"  data-placement="top" title="Mức tiền áp dụng mức phí bảo hiểm"></i></th>
													<th>Tiền</th>
													@if($currentUser->can('action_order_settings_insurance_update') || $currentUser->can('action_order_settings_insurance_delete'))
														<th style="width:105px"></th>
													@endif
												</tr>
											</thead>
											<tbody>
												@foreach( $cods as $key => $item )
													<tr class="data-id-{{$item->id}}">
														<td>Từ {{ number_format($item->min) }}{{ $item->max == 0 ? " vnd trở lên" : ' đến '. number_format($item->max) . ' vnd' }}</td>
														<td>{{ number_format($item->value) }}</td>
														@if($currentUser->can('action_order_settings_insurance_update') || $currentUser->can('action_order_settings_insurance_delete'))
														<td>
															<form action="{{ route('admin.fee-insurance.destroy', $item->id) }}" method="POST">
																@method('DELETE')
																<input type="hidden" name="_token" value="{{ csrf_token() }}" />
																@if($currentUser->can('action_order_settings_insurance_update'))
																	<button type="button" class="btn btn-sm btn-primary" onclick='handleShowEdit({{$item->id}})'>Sửa</button>
																@endif
																@if($currentUser->can('action_order_settings_insurance_delete'))
																	<button class="btn btn-sm btn-danger" onclick=" return confirm('Bạn có muốn xóa không ?')">Xóa</button>
																@endif
															</form>
														</td>
														@endif
													</tr>
												@endforeach
											</tbody>
										</table>
									</div>
								</div>
								</div>
							</div>
						</div>
					</div>
                </div>
            </div>
        </div>
    </div>

	<!-- Modal create contact type -->
	<div class="modal fade" id="crudModal" tabindex="-1" role="dialog" aria-labelledby="crudModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
		<div class="modal-dialog" role="document" style="margin-top:10%">
			<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Thêm mới mức phí bảo hiểm</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body row">
				<div class="col-md-12">
					<label for="sla">Hạn mức<b style="color:red"> (*)</b><i class="fa fa-question-circle ml-1 text-danger" data-toggle="tooltipNew" data-html="true"  data-placement="top" title="Mức tiền áp dụng mức phí bảo hiểm"></i></label>
					<div class="input-group">
						<div class="input-group-append"><span class="input-group-text min-max">Nhỏ nhất</span></div>
						<input class="form-control money-tooltip" min="0" step="1000" type="number" name="min" id="min" value="0" max="4000000000" data-toggle="tooltip" data-placement="top" title="0">
						<div class="input-group-append"><span class="input-group-text">vnd</span></div>
					</div>
					<div class="input-group">
						<div class="input-group-append"><span class="input-group-text min-max">Lớn nhất</span></div>
						<input class="form-control money-tooltip" min="0" step="1000" type="number" name="max" id="max" value="0" max="4000000000" data-toggle="tooltip" data-placement="top" title="0">
						<div class="input-group-append"><span class="input-group-text">vnd</span></div>
					</div>
				</div>
				<div class="col-md-12" style="margin-top:10px">
					<label for="sla">Tiền<b style="color:red"> (*)</b> </label>
					<div class="input-group">
						<input class="form-control money-tooltip" min="1000" step="1000" type="number" name="value" id="value" value="1000" max="4000000000" data-toggle="tooltip" data-placement="top" title="1,000">
						<div class="input-group-append"><span class="input-group-text">đ</span></div>
					</div>
				</div>

				<div class="col-md-12 alert-create alert alert-danger" style="margin-top:10px;display:none;">
					<ul>
						<li>Vui lòng nhập tên loại trợ giúp</li>
					</ul>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary closedModal" data-dismiss="modal">Hủy</button>
				<button type="button" class="btn btn-primary" onclick="handleSaveContactType()">Thêm mới</button>
			</div>
			</div>
		</div>
	</div>

	<!-- Modal edit contact type -->
	<div class="modal fade" id="updateModal" tabindex="-1" role="dialog" aria-labelledby="updateModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Cập nhật mức phí bảo hiểm</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body row">
				<div class="col-md-12">
					<label for="sla">Hạn mức<b style="color:red"> (*)</b><i class="fa fa-question-circle ml-1 text-danger" data-toggle="tooltipNew" data-html="true"  data-placement="top" title="Mức tiền áp dụng mức phí bảo hiểm"></i></label>
					<div class="input-group">
						<div class="input-group-append"><span class="input-group-text min-max">Nhỏ nhất</span></div>
						<input class="form-control money-tooltip" min="0" step="1000" type="number" name="min_update" id="min_update" value="0" max="4000000000" data-toggle="tooltip" data-placement="top" title="0">
						<div class="input-group-append"><span class="input-group-text">vnd</span></div>
					</div>
					<div class="input-group">
						<div class="input-group-append"><span class="input-group-text min-max">Lớn nhất</span></div>
						<input class="form-control money-tooltip" min="0" step="1000" type="number" name="max_update" id="max_update" value="0" max="4000000000" data-toggle="tooltip" data-placement="top" title="0">
						<div class="input-group-append"><span class="input-group-text">vnd</span></div>
					</div>
				</div>
				<div class="col-md-12" style="margin-top:10px">
					<label for="sla">Tiền<b style="color:red"> (*)</b> </label>
					<div class="input-group">
					<input class="form-control money-tooltip" min="1000" step="1000" type="number" name="value_update" id="value_update" value="1000" max="4000000000" data-toggle="tooltip" data-placement="top" title="1,000">
						<div class="input-group-append"><span class="input-group-text">đ</span></div>
					</div>
				</div>
				<div class="col-md-12 alert-update alert alert-danger" style="margin-top:10px;display:none;">
					<ul>
						<li>Vui lòng nhập tên loại trợ giúp</li>
					</ul>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary closedModal" data-dismiss="modal">Hủy</button>
				<button type="button" class="btn btn-primary" onclick="handleEditContactType()">Cập nhật</button>
			</div>
			</div>
		</div>
	</div>
@endsection

@section('javascript')
	<script>
		let apiStore = `{{ route("api.store-order-fee-insurance") }}`;
		let headerAuthorization = `Bearer {{ $currentUser->passport_token }}`;
		let routeIndex = `{{ route('admin.fee-insurance.index') }}`;
		let apiFind = `{{ route("api.find-order-fee-insurance") }}`;
		let apiUpdate = `{{ route("api.update-order-fee-insurance") }}`;
		document.querySelectorAll('[data-toggle="tooltipNew"]').forEach((element) => {
			new coreui.Tooltip(element)
		})
	</script>

	<script src="{{ asset('js/pages/orders/feeInsurance/list.min.js') }} "></script>
@endsection
