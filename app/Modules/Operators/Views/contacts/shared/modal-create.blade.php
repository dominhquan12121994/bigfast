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
				<form method="POST" action="{{ route('admin.contacts.store') }}" enctype="multipart/form-data">
					
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
							<input type="hidden" name="route" value="orders.index">
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
							<label class="mr-2" for="assign_id">Người xử lý</label><b style="color:red"> (*)</b>
							<select class="user-contact form-control custom-select custom-select-lg mb-3" name="assign_id" id="assign_id">
								@foreach($users as $user)
									<option value="{{ $user->id }}" {{ old('assign_id') == $user->id ? 'selected' : '' }} >{{ $user->name }}</option>
								@endforeach
							</select>
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
        let checkDetail = false;
		let checkLading = false;
        function showContacts(lading_code) {
            $( "#lading_code" ).val(lading_code);
            getShopName();
            $('#storeModal').modal('show');
        }

        $(document).ready(function() {
            if ( '{{ $errors->any() }}' == '1' ) {
                getShopName();
                $('#storeModal').modal('show');
                $.Toast('Thất bại', 'Thêm mới yêu cầu thất bại!', 'error');
            }
            $('.type-contact').select2({
				theme: "classic"
			});
			$('.user-contact').select2({
				// ajax: {
				// 	url: '{{ route("api.user.find-by-text") }}',
				// 	dataType: 'json',
				// 	method: "GET",
				// 	data: function (params) {
				// 		var query = {
				// 			search: params.term,
				// 			type: 'public'
				// 		}

				// 		return query;
				// 	},
				// 	processResults: function (data) {
				// 		return {
				// 			results: data.data
				// 		};
				// 	}
				// }
			});

			$('.shop-contact').select2({
				theme: "classic",
				ajax: {
					url: '{{ route("api.shop.list") }}',
					dataType: 'json',
					method: "POST",
					beforeSend: function (xhr) {
						xhr.setRequestHeader ("Authorization", "Bearer {{ $currentUser->passport_token }}");
					},
					data: function (params) {
						var query = {
							search: params.term,
							type: 'public'
						}

						return query;
					},
					processResults: function (data) {
						var defaultV= [
							{
								'id': '',
								'text': 'Tất cả'
							}
						];
						return {
							results: defaultV.concat(data.data)
						};
					}
				}
			});

        });

        //Xử lý tạo mới contacts
		let timeOutShop = null;
        let storedFiles = [];
        let numberAddFile = 0;
        function getShopName() {
			$('.information').hide();
            let urlAjax = "{{ route ('api.shop.find') }}";
            let lading_code = $( "#lading_code" ).val();
			if (lading_code == '') {
				return;
			}
            $('.error_order').hide();
            $.ajax({
				beforeSend: function (xhr) {
					xhr.setRequestHeader ("Authorization", "Bearer {{ $currentUser->passport_token }}");
				},
                method: "POST",
                url: urlAjax,
                data: { 
                    "_token": "{{ csrf_token() }}",
                    "lading_code": lading_code 
                }
            })
            .done(function( msg ) {
                $( "input[type=text][name=shop]" ).val('');
                if (msg.status_code == 200) {
                    $( "input[type=text][name=shop]" ).val(msg.data.name);
                    $( "input[type=hidden][name=shop_id]" ).val(msg.data.id);
					$( "input[type=hidden][name=order_id]" ).val(msg.data.order_id);
					checkLading = true;
					$('.information').show();
					if ( checkDetail && checkLading ) $( '.btn-save' ).prop('disabled', false);
                } else {
					$('.error_order').show();
                }
            })
            .fail(function() {
                $( "input[type=text][name=shop]" ).val('');
				$('.error_order').show();
            });
        };
        
        $( ".shopInput" ).keyup(function() {
			checkLading = false;
			$( '.btn-save' ).prop('disabled', true);
            if (timeOutShop) {
                clearTimeout(timeOutShop);
            }
            timeOutShop = setTimeout(function(){ 
                getShopName(); 
            },1000)
        });

		$( "#detail" ).keyup(function() {
			checkDetail = true;
			if ($.trim(this.value) == '') {
				checkDetail = false;
				$( '.btn-save' ).prop('disabled', true);
			}
			if ( checkDetail && checkLading ) $( '.btn-save' ).prop('disabled', false);
		});	

        function handleFileSelect(e) {
            var files = e.target.files;
            var filesArr = Array.prototype.slice.call(files);
            filesArr.forEach(function(f) {
                var html = "<p class='text-truncate-number'>"+ numberAddFile + "</p><p>. " + f.name + 
                "</p> <i class='cil-trash remove' title='Xoá' onclick='handleDeleteFile(this)'></i>";
                $(`.file-item-${numberAddFile}`).append(html);
            });
        }

        function handleAddFile() {
            numberAddFile++;
            var html = `<div class='file-item file-item-${numberAddFile}'><input class="fileAppent${numberAddFile} hiddenFile" +  type="file" name="file[]"></div>`;
            $(".selectedFiles").append(html);
            $(`.fileAppent${numberAddFile}`).trigger('click');
            $(`.fileAppent${numberAddFile}`).on("change", handleFileSelect);
        }

        function handleDeleteFile(e) {
            numberAddFile--;
            $(e).parent().remove();
            var getText = $( ".file-item" ).find( ".text-truncate-number" );
            getText.each(function(index, f) {
                $(f).html(index + 1);
            });
        }
    </script>