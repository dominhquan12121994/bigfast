@extends('layouts.base')

@section('css')
<style>
    .wrap-text {
        color: #321fdb;
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
                    <i class="fa fa-align-justify"></i>{{ __('Gói cước') }}
                    <span class="float-right">
                        @if($currentUser->can('action_order_services_create'))
                            <button data-toggle="modal" data-target="#storeModal" class="btn btn-primary">{{ __('Thêm mới gói cước') }}</button>
                        @endif
                    </span>
                </div>
                <div class="card-body table-responsive">
                    <table class="table table-responsive-sm table-responsive-md table-responsive-lg table-striped" id="table1">
                        <thead>
                            <tr>
                                <th style="min-width: 135px">Tên gói cước</th>
                                <th style="min-width: 135px">Tên định danh<i data-toggle="tooltip" html="true" title="Tên viết tắt của tên gói cước, phục vụ cho việc tính cước phí" class="fa fa-question-circle ml-1 text-danger"></i></th>
                                <th style="min-width: 100px">Mô tả</th>
                                <th style="min-width: 100px">Trạng thái</th>
                                @if($currentUser->can('action_order_services_update') || $currentUser->can('action_order_services_delete'))
                                    <th style="width:120px"></th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach( $parents as $value)
                                <tr>
                                    <td>{{ $value->name }}</td>
                                    <td>{{ $value->alias }}</td>
                                    <td>
                                        <div>
                                            <span class="wrap-text" data-toggle="tooltip" data-placement="top" title="{{ $value->description }}">{{ strlen($value->description) > 400 ? substr($value->description, 0, strrpos(substr($value->description, 0, 400), " ")) . "..." : $value->description }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        {{ $value->status == 1 ? 'Hoạt động' : 'Không hoạt động' }}
                                    </td>
                                    @if($currentUser->can('action_order_services_update') || $currentUser->can('action_order_services_delete'))
                                        <td>
                                            <form method="POST" action="{{ route('admin.order-service.destroy', $value->id) }}">
                                                @method('DELETE')
                                                <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                                                @if($currentUser->can('action_order_services_update'))
                                                    <button type="button" class="btn btn-sm btn-primary" onclick="handleEdit({{ $value->id }})">Sửa</button>
                                                @endif
                                                @if($currentUser->can('action_order_services_delete'))
                                                    <button class="btn btn-sm btn-danger" onclick="return confirm('Thao tác này không thể hoàn tác. Bạn có muốn xóa không?');">Xóa</button>
                                                @endif
                                            </form>
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{ $parents->appends(request()->input())->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Modal thêm mới vùng -->
	<div class="modal fade" id="storeModal" tabindex="-1" role="dialog" aria-labelledby="storeModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<form method="POST" action="{{ route('admin.order-service.store') }}">
					<input type="hidden" name="_token" value="{{ csrf_token() }}" />
					<div class="modal-header">
						<h5 class="modal-title">Thêm mới gói cước</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body row">
						<div class="col-md-12">
							<label for="name">Tên gói cước</label><b style="color:red"> (*)</b>
							<input autofocus id="name" class="form-control" type="text" placeholder="Tên gói cước" name="name" maxlength="255" required>
						</div>
                        <div class="col-md-12" style="margin-top:10px">
							<label for="name">Tên định danh</label><b style="color:red"> (*)</b>
							<input autofocus id="alias" class="form-control" type="text" placeholder="Tên định danh" name="alias" maxlength="255" required>
						</div>
                        <div class="col-md-12" style="margin-top:10px">
							<label for="description">Mô tả gói cước</label>
                            <textarea class="form-control" name="description" id="description" cols="5" rows="2"></textarea>
                        </div>
                        @if ($errors->any() && Session::get('action') == 'create')
						<div class="col-md-12 alert alert-danger" style="margin-top:10px;">
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
						<button class="btn btn-primary">Thêm mới</button>
					</div>
				</form>
			</div>
		</div>
	</div>

    <!-- Modal sửa vùng -->
	<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<form method="POST" class="editModal" action="{{ old('id_edit') ? route('order-service.update', array('order_service' => old('id_edit'))) : '' }}">
					<input type="hidden" name="_token" value="{{ csrf_token() }}" />
                    @method('PUT')
					<div class="modal-header">
						<h5 class="modal-title">Cập nhật gói cước</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
						</button>
					</div>
                    <input id="id_edit" type="hidden" name="id_edit" value="{{ old('id_edit') }}">
					<div class="modal-body row">
						<div class="col-md-12">
							<label for="name_edit">Tên gói cước</label><b style="color:red"> (*)</b>
							<input autofocus id="name_edit" class="form-control" type="text" placeholder="Tên gói cước" name="name_edit" value="{{ old('name_edit') }}" maxlength="255" required>
						</div>
                        <div class="col-md-12" style="margin-top:10px">
							<label for="alias_edit">Tên định danh</label><b style="color:red"> (*)</b>
							<input autofocus id="alias_edit" class="form-control" type="text" placeholder="Tên định danh" name="alias_edit" value="{{ old('alias_edit') }}" maxlength="255" required>
						</div>
                        <div class="col-md-12" style="margin-top:10px">
							<label for="description_edit">Mô tả gói cước</label>
						    <textarea class="form-control" name="description_edit" id="description_edit" cols="5" rows="2"> {{ old('description_edit') }} </textarea>
                        </div>
                        <div class="col-md-12" style="margin-top:10px">
							<label for="status_edit">Trạng thái</label>
                            <select name="status_edit" class="form-control" id="status_edit">
                                <option value="0" {{ old('status_edit') == 0 ? 'selected' : '' }}>Không hoạt động</option>
                                <option value="1" {{ old('status_edit') == 1 ? 'selected' : '' }}>Hoạt động</option>
                            </select>
						</div>

						@if ($errors->any() && Session::get('action') == 'edit')
						<div class="col-md-12 alert alert-danger" style="margin-top:10px;">
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
						<button class="btn btn-primary">Cập nhật</button>
					</div>
				</form>
			</div>
		</div>
	</div>
@endsection

@section('javascript')
    <script type="text/javascript" src="{{ asset('js/tooltips.js') }}"></script>
    <script>
        @if(Session::get('action') == 'create')
            $('#storeModal').modal('show');
        @elseif(Session::get('action') == 'edit')
            $('#editModal').modal('show');
        @endif

        $('.select2').select2({theme: "classic"});

        function handleEdit(id) {
            urlAjax = '{{ route("api.order_service.find") }}';
			$.ajax({
				beforeSend: function (xhr) {
					xhr.setRequestHeader ("Authorization", "Bearer {{ $currentUser->passport_token }}");
				},
                method: "POST",
                url: urlAjax,
                data: {
                    "_token": "{{ csrf_token() }}",
                    "id": id,
                }
            })
            .done(function( msg ) {
                $('.editModal').prop('action', `{{ url('/admin/order-service').'/${id}' }}`);
                $('#id_edit').val(id);
                $('#description_edit').val(msg.data.description);
                $('#alias_edit').val(msg.data.alias);
                $('#name_edit').val(msg.data.name);
                $('#status_edit').val(msg.data.status);
                $('#editModal').modal('show');
            })
            .fail(function() {

            });
        }
    </script>
@endsection
