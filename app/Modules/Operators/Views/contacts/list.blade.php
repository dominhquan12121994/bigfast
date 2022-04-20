@extends('layouts.base')

@section('css')
	<style>
		.status-dropdown {
			border-radius: 0;
			font-weight: 700;
			justify-content: center;
		}
		.status-menu {
			min-width: 0;
			padding: 0;
		}
		.wrap-text {
    		color: #321fdb;
		}
		.badge {
			color: #fff;
		}
		.cil-trash {
            cursor: pointer;
            font-weight: 900;
            padding-top: 5px;
            padding-left: 4px;
        }
        .file-item {
            color: #f26522;
            font-size: 13px;
            font-weight: 600;
            display: flex;
            margin-left: 8px;
            padding-bottom: 2px;
        }
        .ticket-btn .btn-add-file {
            background: #e2e2e2!important;
            height: 24px!important;
            border-radius: 8px!important;
            width: 124px;
            padding: 2px;
        }
        .hiddenFile{
            display:none;
		}
		.form-inline select + .select2-container {
            width: 200px !important;
		}
		#storeModal select + .select2-container {
            width: 100% !important;
        }
		#checkAllUserModal select + .select2-container {
            width: 100% !important;
        }
		.information {
			display:none;
		}

        @media screen and (max-width: 575px) {
            .shop-contact, .type-contact, .create_btn, .search_input {
                width: 100% !important;
                margin-top: 8px;
            }

            .form-inline select + .select2-container,
            label[for=search] {
                width: 100% !important;
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
						<div class="card-header">
							<i class="fa fa-align-justify"></i> {{ __('Yêu cầu') }}
							<span class="float-right">
								@if ($currentUser->can('action_contacts_create'))
									<button onclick="openModalCreate()" class="btn btn-primary">{{ __('Thêm mới yêu cầu') }}</button>
								@endif
							</span>
						</div>
						<div class="card-body">
							<form class="form-inline d-sm-flex d-block" action=" {{ route('admin.contacts.index') }}" method="GET">
								<div class="form-group mr-2">
									<label class="mr-1 d-block" for="shop">Cửa hàng</label>
									<select class="shop-contact form-control" name="shop" id="shop">
										<option value="" {{ isset($filter['shop']) && $filter['shop'] == 0 ? 'selected' : '' }}>Tất cả</option>
										@if( $shop )
											<option value="{{ $shop->id }}" selected>{!! $shop->name !!}</option>
										@endif
									</select>
								</div>
								<div class="form-group mr-2">
									<label class="mr-1 d-block" for="lading_code">Mã đơn hàng</label>
									<input id="lading_code_search" type="text" class="form-control form-control-sm" placeholder="Ví dụ: B21052199994" name="lading_code" value="{{ isset($filter['lading_code']) ?  $filter['lading_code'] : '' }}" />
								</div>
								<div class="form-group mr-2">
									<label class="mr-1 d-block" for="status">Trạng thái</label>
									<select class="type-contact form-control" name="status" id="status">
										<option value="" selected>Tất cả</option>
										@foreach($status as $key => $value)
											<option @if( isset($filter['status']) && $key == $filter['status']  && $filter['status'] != '') selected @endif value="{{$key}}">{{ $value }}</option>
										@endforeach
									</select>
								</div>
								<div class="form-group mr-2">
									<label class="mr-1 d-block" for="filter_daterange">Thời gian</label>
									<input type="text" class="form-control form-control-sm frm_filter_orders" id="filter_daterange" name="filter_daterange">
								</div>
								<div class="form-group mr-2">
									<label class="mr-1" for="search">
										<button class="btn btn-primary btn-sm btn-block"><i class="cil-search"></i></button>

									</label>
								</div>
							</form>
							<br>
							<div class="table-responsive">
								<table class="table table-responsive-sm table-responsive-md table-responsive-lg table-striped" id="table_contacts">
								<thead>
									<tr>
										@if ( $currentUser->can('action_contacts_assign') )
											<th style="width: 40px;"><input class="checkAll" type="checkbox" name="checkAll"></th>
										@endif
										<th class="text-center" style="width: 50px;">STT</th>
										<th>Đơn hàng</th>
										<th>Trợ giúp</th>
										<th class="text-center" style="min-width: 110px">Trạng thái<i class="fa fa-question-circle ml-1 text-danger" data-toggle="tooltip" data-html="true"  data-placement="top" title="Tình trạng của yêu cầu hỗ trợ"></i></th>
										<th class="text-center" style="min-width: 110px">Mức độ<i class="fa fa-question-circle ml-1 text-danger" data-toggle="tooltip" data-html="true"  data-placement="top" title="Mức độ yêu cầu hỗ trợ"></i></th>
										<th style="min-width: 110px">Người xử lý</th>
										<th style="min-width: 200px">Người tạo</th>
										<th style="width:90px"></th>
									</tr>
								</thead>
								<tbody>
									@foreach ($contacts as $key => $value)
									<tr>
										@if ( $currentUser->can('action_contacts_assign') )
											<td><input type="checkbox" name="checkbox" value="{{$value->id}}"></td>
										@endif
										<td class="text-center">{{ $key + 1 }}</td>
										<td>
											@if($value->expired)
												<span class="badge badge-danger">Quá hạn</span><br>
											@endif
											<div><a href="{{ route('admin.orders.show', $value->order_id) }}">{{ $value->lading_code }}</a> - <a href="{{ route('admin.shops.show', $value->shop_id) }}">{!! $value->orderShop->name !!}</a></div>
										</td>
										<td>
											<div style="font-weight: 600;">{{ $value->typeContacts->name }}</div>
											<div style="width:220px">
											<span class="wrap-text" data-toggle="tooltip" data-placement="top" title="{{ $value->detail }}">{{ strlen($value->detail) > 40 ? substr($value->detail, 0, strrpos(substr($value->detail, 0, 40), " ")) . "..." : $value->detail }}</span>
											</div>
										</td>
										<td class="text-center">
											<div class="nav-item dropdown">
												@php
													switch ($value->status) {
														case 0:
															$colorStatus = 'badge bg-danger';
															break;
														case 1:
															$colorStatus = 'badge bg-primary';
															break;
														case 2:
															$colorStatus = 'badge bg-success';
															break;
														case 3:
															$colorStatus = 'badge bg-warning';
															break;
													}
												@endphp
												@if ($value->assign && \Auth::guard('admin')->id() == $value->assign->id)
													<span class="status text-white {{$colorStatus}}"  data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">{{ $status[$value->status] }}</span>
													<div class="dropdown-menu status-menu" style="margin: 0px;">
														@foreach($status as $index => $sval)
															@if( $value->status < 2 && $value->status >= $index) @continue @endif
															@if( $value->status == 2) @break @endif
															@if ( $index == 3 )
																	<a class="badge bg-light text-dark dropdown-item status-dropdown" href="javascript:void(0)" onclick="openRefuseModal({{$value->id}}, {{$index}}, this)">{{ $sval }}</a>
															@else
																<a class="badge bg-light text-dark dropdown-item status-dropdown" href="javascript:void(0)" onclick="handleChangeStatus({{$value->id}}, {{$index}}, this)">{{ $sval }}</a>
															@endif
														@endforeach
													</div>
												@else
													<span class="status text-white {{$colorStatus}}">{{ $status[$value->status] }}</span>
												@endif
											</div>
										</td>
										<td class="text-center">
											<span>{{ $value->level ? $value->level : 'N/A' }}</span>
										</td>
										<td class="assign-{{$value->id}}">{{ $value->assign ? $value->assign->name : 'N/A' }}</td>
										<td>
											@if( $value->type == 'admin')
												{{ $value->user->name }}
											@else
												Cửa hàng: {!! $value->shop->name !!}
											@endif
											<br>
											{{ date('d-m-Y H:i', strtotime($value->created_at)) }}
										</td>
										<td class="box-actions">
											<form action="{{ route('admin.contacts.destroy', $value->id) }}" method="POST">
												@method('DELETE')
												<input type="hidden" name="_token" value="{{ csrf_token() }}" />
												<button type="button" class="btn btn-sm btn-info" title="View" onclick="handleShowContacts({{$value->id}})">Tra cứu</button>
												@if ($currentUser->can('action_contacts_delete'))
													<button title="Delete" class="btn btn-sm btn-danger" onclick=" return confirm('Bạn có muốn xóa không ?')">Xóa</button>
												@endif
											</form>
										</td>
									</tr>
									@endforeach
								</tbody>
								</table>
							</div>
							{{ $contacts->withQueryString()->links() }}
                            <div class="row">
                                <div class="col-auto mr-auto">
                                    <div id="contact_modal" style="display: none;">
                                        <div class="d-flex">
                                            <div class="flex-grow-1 ml-3 mr-5">
                                                <b>Đã chọn</b><br>
                                                <b id="count_contact_selected" style="font-size: 30px; color: orangered">0</b> Yêu cầu hỗ trợ
                                            </div>
                                            <div class="d-flex align-items-end">
                                                @if ( $currentUser->can('action_contacts_assign') )
                                                <button style="margin-top:10px;display:none;" type="button" class="form-inline btn btn-pill btn-primary actionCheck" onclick="openModalCheck('user')">Người Xử lý</button>
                                                @endif
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
    </div>

	<!-- Modal đổi người xử lý -->
	<div class="modal fade" id="checkAllUserModal" role="dialog" aria-labelledby="checkAllUserModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
			<form action="{{ route('admin.contacts.checkAllContacts') }}" method="POST">
				<input type="hidden" name="_token" value="{{ csrf_token() }}" />
				<div class="modal-header">
					<h5 class="modal-title">Đổi người xử lý</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body row">
					<div class="col-md-12">
						<label for="userChecked">Chọn người xử lý</label>
						<br>
						<select id="user-contact-change" class="form-control user-contact custom-select custom-select-lg mb-3" name="userChecked" id="userChecked">

						</select>
						<input type="hidden" name="idChecked">
					</div>
				</div>
				<div class="modal-footer">
					<button class="btn btn-primary">Cập nhật</button>
				</div>
			</form>
			</div>
		</div>
	</div>

	<!-- Modal create contact type -->
	<div class="modal fade" id="storeModal" role="dialog" aria-labelledby="storeModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<form method="POST" action="{{ route('admin.contacts.store') }}" enctype="multipart/form-data">

					<input type="hidden" name="_token" value="{{ csrf_token() }}" />
					<div class="modal-header">
						<h5 class="modal-title">Thêm mới yêu cầu</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body row">
						<div class="col-md-12">
							<label for="lading_code">Mã đơn hàng</label><b style="color:red"> (*)</b>
							<input type="hidden" name="order_id" value="{{ old('order_id') }}">
							<input autofocus id="lading_code" class="form-control shopInput @error('lading_code') is-invalid @enderror" type="text" value="{{ old('lading_code') }}" placeholder="Ví dụ: B21052199994" name="lading_code" maxlength="255" required>
							<div class="error_order" style="display:none">
								<br>
								<p class="alert alert-danger" role="alert">
									Mã đơn hàng không hợp lệ!
								</p>
							</div>
						</div>
						<div class="col-md-12 information" style="margin-top: 10px;">
							<label for="shop">Cửa hàng</label>
							<input id="shop" class="form-control @error('shop_id') is-invalid @enderror" type="text" value="{{ old('shop') }}" name="shop" maxlength="255" readonly >
							<input id="shop_id" type="hidden" name="shop_id" value="{{ old('shop_id') }}" maxlength="255" >
						</div>
						<div class="col-md-12 information" style="margin-top: 10px;">
							<label class="mr-2" for="assign_id">Người xử lý</label><b style="color:red"> (*)</b>
							<select id="user-contact-create" class="user-contact form-control custom-select custom-select-lg mb-3" name="assign_id" id="assign_id">

							</select>
						</div>
						<div class="col-md-12 information" style="margin-top: 10px;">
							<label class="mr-1" for="contacts_type_id">Loại yêu cầu</label>
							<select id="contacts_type_id" class="type-contact form-control custom-select custom-select-lg mb-3" name="contacts_type_id">
								@foreach($listContact as $key => $item)
									<option value="{{$item->id}}">{{$item->name}}</option>
								@endforeach
							</select>
						</div>
						<div class="col-md-12 information" style="margin-top: 10px;">
							<label for="detail">Nội dung</label><b style="color:red"> (*)</b>
							<textarea id="detail" class="form-control @error('detail') is-invalid @enderror" rows="3" name="detail" required>{{ old('detail') }}</textarea>
						</div>
						<div class="col-md-12 information" style="margin-top: 10px;">
							<div class="selectedFiles"></div>
							<div class="ticket-btn">
								<button  type="button" class="btn btn-primary-page btn-add-file" onclick="handleAddFile()">Đính kèm file <i class="cil-file"></i></button>
								<span>Dung lượng file tối đa 2MB - Tối đa 10 file</span>
								<input class="fileAppent hiddenFile" type="file" multiple name="file[]">
								<div class="list-file"></div>
							</div>
						</div>
						@if ($errors->any())
						<div class="col-md-12 alert-create alert alert-danger" style="margin-top:10px;">
							<ul>
								@foreach ($errors->all() as $error)
									<li>{{ $error }}</li>
								@endforeach
							</ul>
						</div>
						@endif
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary closedModal" data-dismiss="modal">Hủy</button>
						<button class="btn btn-primary btn-save" disabled="disabled">Thêm mới</button>
					</div>
				</form>
			</div>
		</div>
	</div>

	<!-- Modal view contact type -->
	<div class="modal fade" id="viewModal" tabindex="-1" role="dialog" aria-labelledby="viewModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">Chi tiết trợ giúp</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body row">
						<div class="col-md-12">

						</div>
					</div>
				</form>
			</div>
		</div>
	</div>

	<!-- Modal refuse contact -->
	<div class="modal fade" id="refuseModal" tabindex="-1" role="dialog" aria-labelledby="viewModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">Từ chối xử lý yêu cầu</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body row">
						<div class="col-md-12">
							<label for="reason">Lý do từ chối</label><b style="color:red"> (*)</b>
							<textarea id="reason" class="form-control @error('reason') is-invalid @enderror" rows="3" name="reason" required>{{ old('reason') }}</textarea>
						</div>
						<div class="col-md-12 alert alert-danger alert-refuse" style="display:none;margin-top:10px">
							Vui lòng điền lý do từ chối
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary closedModal" data-dismiss="modal">Hủy</button>
						<button class="btn btn-primary btn-save" onclick="hanldeRefuse()">Cập nhật</button>
					</div>
				</form>
			</div>
		</div>
	</div>

@endsection

@section('javascript')
	<script type="text/javascript" src="{{ asset('libs/moment/moment.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('libs/daterangepicker/daterangepicker.min.js') }}"></script>
    <link rel="stylesheet" type="text/css" href="{{ asset('libs/daterangepicker/daterangepicker.min.css') }}" />

	<script>
		let apiFindByPermissionUser = `{{ route('api.user.find-by-permission') }}?search=action_contacts_handler`;
		let headerAuthorization = `Bearer {{ $currentUser->passport_token }}`;
		let apiChangeStatusContact = `{{ route("api.contact.changeStatus") }}`;
		let apiListShop = `{{ route("api.shop.list") }}`;
		let userId = `{{ Auth::guard('admin')->id() }}`;
		let apiRefuseContact = `{{ route("api.contact.refuse") }}`;
		let apiFindShop = `{{ route ('api.shop.find') }}`;
		let apiFindContact = `{{ route("api.contact.find") }}`;
		let startRangeFilter = `{{ $filter['created_range'][0] }}`;
		let endRangeFilter = `{{ $filter['created_range'][1] }}`;
		let urlDownloadContact = `{{ url('/admin/contacts/download') }}`;
		let shopId = null;

		if ( '{{ $errors->any() }}' == '1' ) {
			$('#storeModal').modal('show');
			$.Toast('Thất bại', 'Thêm mới yêu cầu thất bại!', 'error');
		}

	</script>

	<script type="text/javascript" src="{{ asset('js/pages/operators/contacts/list.min.js') }}"></script>
@endsection
