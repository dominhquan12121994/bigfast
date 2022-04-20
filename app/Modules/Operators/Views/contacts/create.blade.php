@extends('layouts.base')

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
                    <form method="POST" action="{{ route('admin.contacts.store') }}" enctype="multipart/form-data">
                        <div class="card-header">
                            <i class="fa fa-align-justify"></i> {{ __('Create Contact') }}
                        </div>
                        <div class="card-body">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                            
                            <div class="form-group row">
                                <div class="col">
                                    <label for="lading_code">Mã đơn hàng</label>
                                    <input type="hidden" name="order_id">
                                    <input autofocus id="lading_code" class="form-control shopInput @error('name') is-invalid @enderror" type="text" placeholder="{{ __('Name') }}" name="lading_code" maxlength="255" required>
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
                                    <label for="shop">Shop</label>
                                    <input id="shop" class="form-control @error('name') is-invalid @enderror" type="text" name="shop" maxlength="255" readonly >
                                    <input id="shop_id" type="hidden" name="shop_id" maxlength="255" >
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col">
                                <label for="contacts_type_id">Loại yêu cầu</label>
                                <select id="contacts_type_id" class="type-contact custom-select custom-select-lg mb-3" name="contacts_type_id">
                                    @foreach($contacts as $key => $item)
                                        <option value="{{$item->id}}">{{$item->name}}</option>
                                    @endforeach
                                </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col">
                                    <label for="detail">Nội dung</label>
                                    <textarea id="detail" class="form-control" rows="3" name="detail"></textarea>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col">
                                    <div class="selectedFiles">

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
                            <button class="btn btn-success" type="submit">Thêm mới</button>
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
        let numberAddFile = 0;

        $(document).ready(function() {
            $('.type-contact').select2({theme: "classic"});
        });

        function getShopName() {
            let urlAjax = "{{ route ('api.shop.find') }}";
            let lading_code = $( "input[type=text][name=lading_code]" ).val();
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
            if (timeOutShop) {
                clearTimeout(timeOutShop);
            }
            timeOutShop = setTimeout(function(){ 
                getShopName(); 
            },1000)
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
@endsection