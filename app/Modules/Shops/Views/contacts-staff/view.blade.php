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
                    <div class="card-header">
                        <i class="fa fa-align-justify"></i> {{ __('Edit Contact') }}
                    </div>
                    <div class="card-body">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                        @method('PUT')

                        <div class="form-group row">
                            <div class="col">
                                <p for="order_id">Mã đơn hàng:{{$contacts->lading_code}}</printf>
                                <p>Shop: {!!$contacts->ordershop->name!!}</p>
                                <p>Loại yêu cầu: {{$listContact[ $contacts->contacts_type_id]->name}}</p>
                                <p>Nội dung: {{ $contacts->detail }}</p>
                                <p>Trạng thái: {{ $status[$contacts->status] }}</p>
                                <p>Người xử lý: {{ $contacts->assign_id ? $users[$contacts->assign_id]->name  : ''}}</p>
                                <div class="selectedFiles">
                                    File đính kèm:
                                    @foreach($file_path as $key => $path)
                                        <div class='file-item'>
                                            <input class="fileedit" type="hidden" name="fileedit[]" value="{{$path}}">
                                            <p class='text-truncate-number'>{{$key+1}}</p><p>. <a href="{{ route('admin.admin.contacts.getDownload', ['id' => $contacts->id, 'position' => $key] ) }}">{{$path}}</a></p> <i class='cil-trash remove' title='Xoá' onclick='handleDeleteFile(this)'></i>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <a href="{{ url()->previous() }}" class="btn btn-primary">Quay lại</a>
                    </div>
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
            $('.type-contact').select2({theme: "classic"});
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
    </script>
@endsection
