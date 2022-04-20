<style>
    #storeModal select + .select2-container {
        width: 100% !important;
    }
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
		.information {
			display:none;
		}
</style>

<!-- Modal create contact type -->
<div class="modal fade" id="storeModal" role="dialog" aria-labelledby="storeModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<form method="POST" action="{{ route('shop.contacts-staff.store') }}" enctype="multipart/form-data">
				
				<input type="hidden" name="_token" value="{{ csrf_token() }}" />
				<div class="modal-header">
					<h5 class="modal-title">Thêm mới trợ giúp</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body row">
					<div class="col-md-12">
						<label for="lading_code">Mã đơn hàng</label><b style="color:red"> (*)</b>
						<input type="hidden" name="order_id">
						<input type="hidden" name="route" value="shop.order-staff.index">
						<input autofocus id="lading_code" class="form-control shopInput @error('lading_code') is-invalid @enderror" type="text" placeholder="{{ __('Name') }}" name="lading_code" value="{{ old('lading_code') }}" maxlength="255" readonly>
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
						<input id="shop_id" type="hidden" name="shop_id" maxlength="255" value="{{ old('shop') }}" >
					</div>
					<div class="col-md-12 information" style="margin-top: 10px;">
						<label class="mr-1" for="contacts_type_id">Loại yêu cầu</label>
						<select id="contacts_type_id" class="type-contact form-control custom-select custom-select-lg mb-3" name="contacts_type_id">
							@foreach($listContact as $key => $item)
								<option value="{{$item->id}}" {{ old('contacts_type_id') == $item->id ? 'selected' : '' }}>{{$item->name}}</option>
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

<script>
	let apiListShop = `{{ route("api.shop.list") }}`;
	let headerAuthorization = `Bearer {{ $currentUser->passport_token }}`;
	let apiFindShop = `{{ route ('api.shop.find') }}`;

	$(document).ready(function() {
		if ( '{{ $errors->any() }}' == '1' ) {
			getShopName();
			$('#storeModal').modal('show');
			$.Toast('Thất bại', 'Thêm mới yêu cầu thất bại!', 'error');
		}
	});
</script>

<script type="text/javascript" src="{{ asset('js/pages/shops/contacts/modal-create.min.js') }}"></script>