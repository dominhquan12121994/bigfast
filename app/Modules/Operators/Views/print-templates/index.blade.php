@extends('layouts.base')

@section('css')
    <style>
        .panel {
            margin-bottom: 20px;
            background-color: #fff;
            border: 1px solid transparent;
            border-radius: 4px;
            -webkit-box-shadow: 0 1px 1px rgba(0,0,0,0.05);
            box-shadow: 0 1px 1px rgba(0,0,0,0.05);
        }
        .panel-default {
            border-color: #ddd;
        }
        .panel-default>.panel-heading {
            color: #333;
            background-color: #f5f5f5;
            border-color: #ddd;
        }
        .panel-heading {
            padding: 10px 15px;
            border-bottom: 1px solid transparent;
            border-top-right-radius: 3px;
            border-top-left-radius: 3px;
        }
        .panel-title {
            margin-top: 0;
            margin-bottom: 0;
            font-size: 16px;
            color: inherit;
        }
        .modal .panel-body {
            color: #444;
        }
        .panel-body {
            padding: 15px;
        }
        .rowItemKeyword {
            margin-bottom: 5px;
        }
        .panel-body:before, .panel-body:after {
            display: table;
            content: " ";
        }
        .cke_button__keywordtemplate_label {
            display: block !important;
        }
        .btn-default:hover {
            border: 1px solid;
        }
        .alert-warning-custom {
            color: rgb(138, 109, 59) !important;
            background-color: rgb(252, 248, 227) !important;
            border-color: rgb(138, 109, 59);
            margin-top: 10px;
        }
        .body-preview {
            width: calc(<?php echo $dataSize['width']; ?>*3.7795275591px);
        }
        div[id*="panel-preview"] {
            width: calc(<?php echo $dataSize['width']; ?>*3.7795275591px);
            height: calc(<?php echo $dataSize['height']; ?>*3.7795275591px);
            background: white;
            margin: 0;
            overflow: hidden;
        }
        #panel-preview {
            font-family: Time New Roman;
        }
        #panel-preview table {
            border-collapse: unset;
        }
        #panel-preview hr {
            margin-top: revert;
            margin-bottom: revert;
            border: revert;
            border-top: revert;
        }
        #panel-preview h1, #panel-preview h2, #panel-preview h3, #panel-preview h4, #panel-preview h5, #panel-preview h6 {
            font-size: initial;
            margin-top: auto;
            margin-bottom: auto;
            font-weight: revert;
            line-height: revert;
        }
        #panel-preview table {
            border-collapse: collapse;
            color: rgb(0, 0, 0);
            font-style: normal;
            font-size: medium;
            font-variant-caps: normal;
            font-variant-east-asian: normal;
            font-variant-ligatures: normal;
            font-variant-numeric: normal;
            font-weight: 400;
            height: 40px;
            line-height: 20px;
            text-align: start;
            text-indent: 0px;
            vertical-align: middle;
            white-space: normal;
            width: 207.778px;
            -webkit-border-horizontal-spacing: 0px;
            -webkit-border-vertical-spacing: 0px;
            font-family: inherit;
        }
        .panel-color {
            background: #e5e4e4;
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
                        <i class="fa fa-align-justify"></i> {{ __('M???u in') }}
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.print-templates.update') }}@if( isset( $filter['page'] ) )?page={{$filter['page']}} @endif" method="POST">

                        <div class="row justify-content-between">
                            <div class="col-md-6">
                                <b>Ch???n kh??? gi???y</b>
                                <select onchange="chosePage(this)" name="page_size" id="page_size" class="form-control">
                                    @foreach( $types as $index => $item)
                                        @foreach ( $item as $key => $val)
                                            <option @if( isset( $filter['page'] ) && $filter['page'] == $index && isset( $filter['type'] ) && $filter['type'] == $key ) selected @endif value="{{ $index }},{{$key}}">{{ $val['name'] }}</option>
                                        @endforeach
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <!-- <form action="{{ route('admin.admin.print-templates.preview') }}" method="POST" target="_blank">
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                                    <input type="hidden" id="htmlnew" name="htmlnew" value=" {{ isset($setting['htmlConvert']) ? $setting['htmlConvert'] : '' }} "/>
                                    <input type="hidden" id="pageSize" name="pageSize" value=" {{ isset($setting['page_size']) ? $setting['page_size'] : '' }} "/>
                                    <input type="hidden" id="type" name="type" value=" {{ isset($setting['type']) ? $setting['type'] : 'doc' }} "/>
                                    <button class="btn btn-primary float-right">In m???u</button>
                                </form> -->
                                @if($currentUser->can('action_print_template_update'))
                                    <button class="btn btn-success float-right"><i class="cil-save"></i> L??u c??i ?????t</button>
                                @endif
                            </div>
                        </div>
                        <div class="row" style="margin-top: 10px">
                            <div style="min-width: 50%;">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                                <input type="hidden" id="page_size" name="page_size" value=" {{ isset($setting['page_size']) ? $setting['page_size'] : '' }} "/>
                                <input type="hidden" id="type" name="type" value=" {{ isset($setting['type']) ? $setting['type'] : '' }} "/>
                                <textarea cols="80" id="html" name="html" rows="10" data-sample-short>{{ isset($setting['html']) ? $setting['html'] : '' }}</textarea>
                                
                            </div>
                            <div class="col-md-6">
                                <div class="panel panel-default panel-color">
                                    <div class="panel-heading">
                                        <h4 class="text-center">Xem tr?????c m???u in</h4>
                                    </div>
                                    <div class="body-preview">
                                        <div id="panel-preview">
                                            {!! isset($setting['htmlConvert']) ? $setting['htmlConvert'] : '' !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="alert alert-warning alert-warning-custom" style="margin-top:20px">
                                    <div><b>Ch?? ??:</b></div>
                                    <div>- M??n h??nh b??n tr??i l?? n???i dung m???u in qu?? kh??ch c?? th??? thay ?????i (Theo kh??? gi???y c???n in: Kh??? A4|A5, Kh??? A4|A5 (kh??? ngang), M???u 8 ????n h??ng 1 trang (Kh??? d???c), M???u 6 ????n h??ng 1 trang (Kh??? ngang)).</div>
                                    <div>- M??n h??nh b??n ph???i l?? m??n h??nh xem tr?????c m???u in sau khi thay ?????i.</div>
                                    <div>- N??t <b>"????t l??m m???c ?????nh"</b>, n???u qu?? kh??ch mu???n s??? d???ng m???u in n??y l??m m???c ?????nh ch???n khi in.</div>
                                    <div>- Sau khi ch???nh s???a ho??n t???t, qu?? kh??ch nh???n <b>"L??u c??i ?????t"</b> ????? l??u l???i n???i dung ???? ch???nh s???a.</div>
                                    <div><b>Gi???i th??ch thu???t ng???:</b></div>
                                    <div>- <b>"T??? kh??a cho m???u in"</b>: L?? t???t c??? c??c t??? kh??a qu?? kh??ch c?? th??? s??? d???ng cho m???u in n??y. T??? kh??a c???a m???u in ???????c x??c ?????nh b??n trong k?? t??? {__ __}, v?? d???: {__MA_DH__} l?? m?? ????n h??ng.</div>
                                    <div>- Khi qu?? kh??ch s??? d???ng t??? kh??a n??y, h??? th???ng s??? thay th??? t??? kh??a ???? b???ng d??? li???u ???? ???????c x??c ?????nh c???a ????n h??ng. V?? d???: tr??n m??n h??nh t??? kh??a <b>"{__MA_DH__}"</b> s??? ???????c thay th??? b???ng m?? ????n h??ng <b>4193531</b> khi in.</div>
                                    <div>- Qu?? kh??ch c?? th??? l???a ch???n nhi???u t??? kh??a m???u in, b???ng c??ch click v??o bi???u t?????ng <b>"T??? kh??a cho m???u in"</b>.</div>
                                    <div><b>L??u ??:</b></div>
                                    <div>- N???i dung xem tr?????c m???u in ch??? l?? m???t n???i dung m???u, kh??ng ph???i l?? m???t ????n h??ng c??? th???.</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                </div>
            </div>
        </div>
    </div>

    <!-- modal -->
    <div class="modal fade" id="keyWordCkeModal" tabindex="-1" role="dialog" aria-labelledby="keyWordCkeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="keyWordCkeModalLabel">Danh s??ch t??? kh??a</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="bootstrap-dialog-body">
                    <div class="bootstrap-dialog-message">
                        <div class="form-horizontal consignment-selector clearfix">
                            <div class="row">
                                @foreach ( $key_words as $headers )
                                    <div class="col-md-12">
                                        <div class="panel panel-default">
                                            <div class="panel-heading">
                                                <h3 class="panel-title">{{ $headers['name'] }}</h3>
                                            </div>
                                            <div class="panel-body">
                                                <div class="row">
                                                    @foreach ( $headers['lists'] as $key => $lists )
                                                        <div class="col-md-6 rowItemKeyword">
                                                            <div class="row">
                                                                <div class="col-md-4">{{ $lists['name'] }}</div>
                                                                <div class="col-md-6"><span>{{ $key }}</span></div>
                                                                <div class="col-md-2">
                                                                    <button onclick="insertCkeditor('{{ $key }}')" type="button" class="btn btn-default btn-sm"><i class="cil-check-alt"></i>Ch???n</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div> 
                </div>
            </div>
        </div>
    </div>
@endsection

@section('javascript')
    <script>
        let listCode = `{{ json_encode($listCode) }}`;
        let dataSizeHeight = `{{ $dataSize['height'] * 3.7795275591 }}px`;
        let dataSizeWidth = `{{ $dataSize['width'] * 3.7795275591 }}px`;
        let routePrintTemplateIndex = `{{ route('admin.print-templates.index') }}`;
    </script>

    <script src="{{ asset('libs/ckeditor_full/ckeditor.js') }} "></script>
    <script src="{{ asset('js/pages/operators/print-templates/index.min.js') }} "></script>
@endsection