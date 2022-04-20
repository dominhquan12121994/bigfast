@extends('layouts.baseShop')

@section('css')
    <style>
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
    </style>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="animated fadeIn">
            <div class="row">
                <div class="col-sm-12 col-md-10 col-lg-8 col-xl-6">
                <div class="card">
                    <form id="myForm" method="POST" action="{{ route('shop.contacts.update', $contacts->id) }}" enctype="multipart/form-data">
                        <div class="card-header">
                            <i class="fa fa-align-justify"></i> Cập nhật hỗ trợ
                        </div>
                        <div class="card-body">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                            @method('PUT')

                            <div class="form-group row">
                                <div class="col">
                                    <label for="order_id">Mã đơn hàng</label>
                                    <input disabled id="order_id" class="form-control shopInput @error('name') is-invalid @enderror" type="text" placeholder="{{ __('Name') }}" maxlength="255" required
                                    value="{{$contacts->lading_code}}">
                                    <div class="error_order" style="display:none">
                                        <br>
                                        <p class="alert alert-danger" role="alert">
                                            Mã đơn hàng không hợp lệ!
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col">
                                    <label for="shop">Cửa hàng</label>
                                    <input id="shop" class="form-control @error('name') is-invalid @enderror" type="text" maxlength="255" disabled
                                    value="{!!$contacts->ordershop->name!!}" >
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col">
                                <label for="contacts_type_id">Loại yêu cầu</label>
                                <select id="contacts_type_id" class="type-contact custom-select custom-select-lg mb-3" name="contacts_type_id">
                                    @foreach($listContact as $key => $item)
                                        <option  @if($item->id == $contacts->contacts_type_id) selected @endif value="{{$item->id}}">{{$item->name}}</option>
                                    @endforeach
                                </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col">
                                    <label for="detail">Nội dung</label><b style="color:red"> (*)</b>
                                    <textarea id="detail" class="form-control @error('detail') is-invalid @enderror"" rows="3" name="detail" required>{{ $contacts->detail }}</textarea>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col">
                                    <label for="status">Trạng thái</label>
                                    <select onchange="changeStatus(this)" class="form-control" name="status" id="status">
                                        @foreach($status as $key => $sts)
                                            @if( $contacts->status < 2 && $contacts->status >= $key) @continue @endif
                                            @if( $contacts->status == 2  && $contacts->status != $key) @continue @endif
                                            <option @if($key == $contacts->status) selected @endif value="{{$key}}">{{$sts}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row reason" @if( $contacts->status != 3) style="display:none;" @endif>
                                <div class="col">
                                    <label for="reason">Lý do</label><b style="color:red"> (*)</b>
                                    <textarea id="reason" class="form-control " rows="3" name="reason" disabled required=""></textarea>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col">
                                    <label for="assign_id">Người xử lý</label><b style="color:red"> (*)</b>
                                    <select class="user-contact custom-select custom-select-lg mb-3" name="assign_id" id="assign_id">
                                        @foreach($users as $user)
                                            <option  @if($user->id == $contacts->assign_id) selected @endif value="{{ $user->id }}">{{ $user->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col">
                                    <div class="selectedFiles">
                                        @foreach($file_path as $key => $path)
                                            <div class='file-item'>
                                                <input class="fileedit" type="hidden" name="fileedit[]" value="{{$path}}">
                                                <p class='text-truncate-number'>{{$key+1}}</p><p>. <a href="{{ route('shop.contacts.getDownload', ['id' => $contacts->id, 'position' => $key] ) }}">{{$path}}</a></p> <i class='cil-trash remove' title='Xoá' onclick='handleDeleteFile(this)'></i>
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="ticket-btn">
                                        <button  type="button" class="btn btn-primary-page btn-add-file" onclick="handleAddFile()">Đính kèm file <i class="cil-file"></i></button>
                                        <span>Dung lượng file tối đa 2MB - Tối đa 10 file</span>
                                    </div>
                                </div>
                            </div>

                            @if ($errors->any())
                                <br>
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </div>
                        <div class="card-footer">
                            <button class="btn btn-success" type="submit">Cập nhật</button>
                            <a href="{{ url()->previous() }}" class="btn btn-primary">Quay lại</a>
                        </div>
                    </form>
                </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('javascript')
    <script>
        let timeOutShop = null;
        let storedFiles = [];
        let indexFileAdd = {{count($file_path)}};
        let numberFileAdd = {{count($file_path)}};

        $(document).ready(function() {
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

        });

        function handleFileSelect(e) {
            var files = e.target.files;
            var filesArr = Array.prototype.slice.call(files);
            filesArr.forEach(function(f) {
                var html = "<p class='text-truncate-number'>"+ indexFileAdd + "</p><p>. " + f.name +
                "</p> <i class='cil-trash remove' title='Xoá' onclick='handleDeleteFile(this)'></i>";
                $(`.file-item-${numberFileAdd}`).append(html);
            });
        }

        function handleAddFile() {
            indexFileAdd++;
            numberFileAdd++;
            var html = `<div class='file-item file-item-${numberFileAdd}'><input class="fileAppent${numberFileAdd} hiddenFile" +  type="file" name="file[]"></div>`;
            $(".selectedFiles").append(html);
            $(`.fileAppent${numberFileAdd}`).trigger('click');
            $(`.fileAppent${numberFileAdd}`).on("change", handleFileSelect);
        }

        function handleDeleteFile(e) {
            indexFileAdd--;
            $(e).parent().remove();
            var getText = $( ".file-item" ).find( ".text-truncate-number" );
            getText.each(function(index, f) {
                $(f).html(index + 1);
            });
        }

        function changeStatus(e) {
            $('.reason').hide();
            $('#reason').prop('disabled', true);
            if (e.value == 3) {
                $('.reason').show();
                $('#reason').prop('disabled', false);
            }
        }
    </script>
@endsection
