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
	</style>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="animated fadeIn">
            <div class="row">
                <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
					<div class="card">
						<div class="card-header">
							<i class="fa fa-align-justify"></i> {{ __('Loại trợ giúp') }}
								<button type="button" class="btn btn-primary float-right" data-toggle="modal" data-target="#crudModal">
									Thêm mới loại trợ giúp
								</button>
							@if($currentUser->can('action_contacts_type_create'))
							@endif
						</div>
						<div class="card-body">
							<div class="row">
								<!-- <div class="col-4">
								<div class="list-group" id="list-tab" role="tablist">
									@foreach( $listContactType as $key => $value)
										<a class="list-group-item list-group-item-action @if($key == 0) active @endif" id="list-contacts-list-{{ $key }}" data-toggle="tab" href="#list-contacts-{{ $key }}" role="tab" aria-controls="list-contacts-{{ $key }}" aria-selected="@if($key == 0) true @else false @endif">{{$value}}</a>
									@endforeach
								</div>
								</div> -->
								<div class="col-12">
								<div class="tab-content" id="nav-tabContent">
									@foreach(  $listContactType as $key => $value )
										<div class="tab-pane fade table-responsive @if($key == 0) active show @endif" id="list-contacts-{{$key}}" role="tabpanel" aria-labelledby="list-contacts-list-{{$key}}">
											<table class="table table-striped" id="table1">
												<thead>
													<tr>
														<th>Tên</th>
														<th style="min-width: 150px">Thời gian xử lý<i class="fa fa-question-circle ml-1 text-danger" data-toggle="tooltip" data-html="true"  data-placement="top" title="Cảnh báo khi hỗ trợ quá thời gian quy định phải xử lý"></i></th>
														<th style="min-width: 80px">Mức độ</th>
														@if($currentUser->can('action_contacts_type_update') || $currentUser->can('action_contacts_type_delete'))
															<th style="width:105px"></th>
														@endif
													</tr>
												</thead>
												<tbody>
													@foreach( $list as $item )
														<tr class="data-id-{{$item->id}}">
															@if( $item->parent_id == $key )
																<td>{{ $item->name }}</td>
																<td>{{ $item->sla }}</td>
																<td>
																	@if( $item->level == 1)
																		<span class="badge bg-danger">{{ $level[$item->level] }}</span>
																	@else
																		<span class="badge bg-primary">{{ $level[$item->level] }}</span>
																	@endif
																</td>
																@if($currentUser->can('action_contacts_type_update') || $currentUser->can('action_contacts_type_delete'))
																<td>
																	<form action="{{ route('admin.contacts-type.destroy', $item->id) }}" method="POST">
																		@method('DELETE')
																		<input type="hidden" name="_token" value="{{ csrf_token() }}" />
																		@if($currentUser->can('action_contacts_type_update'))
																			<button type="button" class="btn btn-sm btn-primary" onclick='handleShowEdit({{$item->id}})'>Sửa</button>
																		@endif
																		@if($currentUser->can('action_contacts_type_delete'))
																			<button class="btn btn-sm btn-danger" onclick=" return confirm('Bạn có muốn xóa không ?')">Xóa</button>
																		@endif
																	</form>
																</td>
																@endif
															@endif
														</tr>
													@endforeach
												</tbody>
											</table>
										</div>
									@endforeach
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
		<div class="modal-dialog" role="document">
			<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Thêm mới loại trợ giúp</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body row">
				<div class="col-md-12">
					<label for="name_type">Tên loại trợ giúp<b style="color:red"> (*)</b></label>
					<input class="form-control" type="text" name="name" id="name_type">
				</div>
				<!-- <div class="col-md-12">
					<label for="parent_id">Cấp cha</label>
					<select name="parent_id" id="parent_id" class="form-control">
						@foreach( $listContactType as $key => $value)
							<option value="{{ $key }}">{{ $value }}</option>
						@endforeach
					</select>
				</div> -->
				<div class="col-md-6" style="margin-top:10px">
					<label for="sla">Thời gian xử lý <i class="fa fa-question-circle ml-1 text-danger" data-toggle="tooltip" data-html="true"  data-placement="top" title="Cảnh báo khi hỗ trợ quá thời gian quy định phải xử lý"></i></label>
					<div class="input-group">
						<input class="form-control" min="0" step="5" type="number" name="sla" id="sla">
						<div class="input-group-append"><span class="input-group-text">phút</span></div>
					</div>
				</div>
				<div class="col-md-6" style="margin-top:10px">
					<label for="level">Mức độ <i class="cil-short-text" title="Cấp độ ưu tiên xử lý trợ giúp"></i></label>
					<select name="level" id="level" class="form-control">
						@foreach($level as $key => $value)
							<option value="{{ $key }}">{{ $value }}</option>
						@endforeach
					</select>
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
				<h5 class="modal-title">Cập nhật loại trợ giúp</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body row">
				<div class="col-md-12">
					<label for="name_type_update">Tên loại trợ giúp<b style="color:red"> (*)</b></label>
					<input class="form-control" type="text" name="name_update" id="name_type_update">
				</div>
				<!-- <div class="col-md-12">
					<label for="parent_id">Cấp cha</label>
					<select name="parent_id" id="parent_id" class="form-control">
						@foreach( $listContactType as $key => $value)
							<option value="{{ $key }}">{{ $value }}</option>
						@endforeach
					</select>
				</div> -->
				<div class="col-md-6" style="margin-top:10px">
					<label for="sla_update">Thời gian xử lý <i class="fa fa-question-circle ml-1 text-danger" data-toggle="tooltip" data-html="true"  data-placement="top" title="Cảnh báo khi hỗ trợ quá thời gian quy định phải xử lý"></i></label>
					<div class="input-group">
						<input class="form-control" min="0" step="5" type="number" name="sla_update" id="sla_update">
						<div class="input-group-append"><span class="input-group-text">phút</span></div>
					</div>
				</div>
				<div class="col-md-6" style="margin-top:10px">
					<label for="level_update">Mức độ <i class="cil-short-text" title="Cấp độ ưu tiên xử lý trợ giúp"></i></label>
					<select name="level_update" id="level_update" class="form-control">
						@foreach($level as $key => $value)
							<option value="{{ $key }}">{{ $value }}</option>
						@endforeach
					</select>
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
		var id_picked = 0;
		$(document).ready(function() {
			$('#sla').val(null);
        });

		function handleSaveContactType() {
			let name = $('input[type=text][name=name]').val();
			if ( name.trim().length == 0) {
				$('.alert-create ul li').html('Vui lòng nhập tên loại trợ giúp');
				$('.alert-create').show();
				$('#name_type').addClass('is-invalid');
				return;
			}
			let urlAjax = '{{ route("api.contactType.create") }}';
			let parent_id = 0;
			let sla = $('input[type=number][name=sla]').val();
			let level = $('#level').val();
			$.ajax({
				beforeSend: function (xhr) {
					xhr.setRequestHeader ("Authorization", "Bearer {{ $currentUser->passport_token }}");
					// setting a timeout
					$('#crudModal .btn-primary').html('');
					$('#crudModal .btn-primary').addClass('spinner-border ');
					$('#crudModal .btn-primary').prop( "disabled", true );
				},
                method: "POST",
                url: urlAjax,
                data: {
                    "_token": "{{ csrf_token() }}",
                    "name": name,
					// "parent_id": parent_id,
					"sla": sla,
					"level": level,
				},
            })
            .done(function( msg ) {
				let html = '';
				html += `<tr class="data-id-${msg.data.id}"><td>` + msg.data.name + '</td>';
				html += '<td>' + msg.data.sla + '</td>';
				if (msg.data.level == 0) {
					html += '<td><span class="badge bg-primary">Thấp</span></td>';
				} else {
					html += '<td><span class="badge bg-danger">Cao</span></td>';
				}
				@if($currentUser->can('action_contacts_type_update') || $currentUser->can('action_contacts_type_delete'))
				html += `<td class="width100"><form action="{{ url("/admin/contacts-type") }}/${msg.data.id}" method="POST">
					@method('DELETE')
					<input type="hidden" name="_token" value="{{ csrf_token() }}" />`;
				@if($currentUser->can('action_contacts_type_update'))
				html += `<button type="button" class="btn btn-sm btn-primary" onclick='handleShowEdit(${msg.data.id})'>Sửa</button></button>`;
				@endif
				@if($currentUser->can('action_contacts_type_delete'))
				html += `<button class="btn btn-sm btn-danger" onclick=" return confirm('Bạn có muốn xóa không ?')">Xóa</button>`;
				@endif
				html += `</form></td>`;
				@endif
				html += '</tr>';
				$(`#list-contacts-${parent_id} table tbody`).append(html);
				$('#crudModal .btn-primary').html('Lưu');
				$('#crudModal .btn-primary').removeClass('spinner-border ');
				$('#crudModal .btn-primary').removeClass('spinner-border ');
				$('#crudModal .btn-primary').prop( "disabled", false );
				$('.closedModal').trigger('click');
				$('#name_type').val('');
				$('#sla').val('0');
				$.Toast("Thành công", "Thêm mới loại trợ giúp thành công!", "notice");
            })
            .fail(function(msg) {
				$('#crudModal .btn-primary').html('Lưu');
				$('#crudModal .btn-primary').removeClass('spinner-border ');
				$('#crudModal .btn-primary').prop( "disabled", false );
				$('.alert-create').show();
				let validation_messages = JSON.parse(msg.responseText).errors;
				let html = '';
				for (var key in validation_messages) {
					let obj = validation_messages[key];
					html += '<li>'+ obj['name'] +'</li>'
					$('#'+key).addClass('is-invalid');
				}
				$('.alert-create ul').html(html);
            });
		}

		function handleShowEdit(id) {
			if (id_picked == id) {
				$('#updateModal').modal('show');
				return;
			}
			let urlAjaxShow = '{{ route("api.contactType.find") }}';
			id_picked = id;
			$.ajax({
				beforeSend: function (xhr) {
					xhr.setRequestHeader ("Authorization", "Bearer {{ $currentUser->passport_token }}");
				},
                method: "POST",
                url: urlAjaxShow,
                data: {
					"_token": "{{ csrf_token() }}",
					'id': id
				},
            })
            .done(function( msg ) {
				$('input[type=text][name=name_update]').val(msg.data.name);
				$('input[type=number][name=sla_update]').val(msg.data.sla);
				$('#level_update').val(msg.data.level);
				$('#updateModal').modal('show');
            })
            .fail(function(msg) {

            });
		}

		function handleEditContactType() {
			let name = $('input[type=text][name=name_update]').val();
			if ( name.trim().length == 0) {
				$('.alert-update ul li').html('Vui lòng nhập tên loại trợ giúp');
				$('.alert-update').show();
				$('#name_type_update').addClass('is-invalid');
				return;
			}
			let urlAjax = '{{ route("api.contactType.update") }}';
			let parent_id = 0;
			let sla = $('input[type=number][name=sla_update]').val();
			let level = $('#level_update').val();
			$.ajax({
				beforeSend: function (xhr) {
					xhr.setRequestHeader ("Authorization", "Bearer {{ $currentUser->passport_token }}");
					// setting a timeout
					$('#updateModal .btn-primary').html('');
					$('#updateModal .btn-primary').addClass('spinner-border ');
					$('#updateModal .btn-primary').prop( "disabled", true );
				},
                method: "POST",
                url: urlAjax,
                data: {
                    "_token": "{{ csrf_token() }}",
                    "name": name,
					"id": id_picked,
					"sla": sla,
					"level": level,
				},
            })
            .done(function( msg ) {
				$('#updateModal .btn-primary').html('Cập nhật');
				$('#updateModal .btn-primary').removeClass('spinner-border ');
				$('#updateModal .btn-primary').removeClass('spinner-border ');
				$('#updateModal .btn-primary').prop( "disabled", false );
				if (msg.message == 'Success') {
					let html = '';
					html += `<td>` + msg.data.name + '</td>';
					html += '<td>' + msg.data.sla + '</td>';
					if (msg.data.level == 0) {
						html += '<td><span class="badge bg-primary">Thấp</span></td>';
					} else {
						html += '<td><span class="badge bg-danger">Cao</span></td>';
					}
					@if($currentUser->can('action_contacts_type_update') || $currentUser->can('action_contacts_type_delete'))
					html += `<td class="width100"><form action="{{ url("/admin/contacts-type") }}/${msg.data.id}" method="POST">
						@method('DELETE')
						<input type="hidden" name="_token" value="{{ csrf_token() }}" />`;
					@if($currentUser->can('action_contacts_type_update'))
					html += `<button type="button" class="btn btn-sm btn-primary" onclick='handleShowEdit(${msg.data.id})'>Sửa</button>`;
					@endif
					@if($currentUser->can('action_contacts_type_delete'))
					html += `<button class="btn btn-sm btn-danger" onclick=" return confirm('Bạn có muốn xóa không ?')">Xóa</button>`;
					@endif
					html += `</form></td>`;
					@endif
					$(`#list-contacts-${parent_id} table tbody tr.data-id-${msg.data.id}`).html(html);
					$('.closedModal').trigger('click');
					$.Toast("Thành công", "Cập nhật loại trợ giúp thành công!", "notice");
				}
            })
            .fail(function(msg) {
				$('#updateModal .btn-primary').html('Cập nhật');
				$('#updateModal .btn-primary').removeClass('spinner-border ');
				$('#updateModal .btn-primary').prop( "disabled", false );
				$('.alert-update').show();
				let validation_messages = JSON.parse(msg.responseText).errors;
				let html = '';
				for (var key in validation_messages) {
					let obj = validation_messages[key];
					html += '<li>'+ obj['msg'] +'</li>'
					$('#'+key).addClass('is-invalid');
				}
				$('.alert-update ul').html(html);
            });
		}

	$('#crudModal').on('show.coreui.modal', function (e) {
		$('#name_type').removeClass('is-invalid');
		$('#sla').removeClass('is-invalid');
		$('.alert-create').hide();
		$('#name_type').val('');
		$('#sla').val('0');
		setTimeout(function(){ $('#name_type').focus(); }, 500);
	});
	$('#updateModal').on('show.coreui.modal', function (e) {
		$('#name_type_update').removeClass('is-invalid');
		$('#sla_update').removeClass('is-invalid');
		$('.alert-update').hide();
		setTimeout(function(){ $('#name_type_update').focus(); }, 500);
	});
	</script>
@endsection
