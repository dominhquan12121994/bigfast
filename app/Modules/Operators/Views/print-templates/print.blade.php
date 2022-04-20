<style>
    @media print {
        @page { 
            margin: 0;
            @if($page_landscape)
                size: landscape;
            @endif
        }
        .body-html {
            @if ( $filter['page_size'] !== 'K80' )
                height: calc(<?php echo $css['page']['height']; ?>mm - 20mm) !important;
            @endif
            page-break-after: always;
        }
        .print-setting {
            display: none;
        }
    }
    .body-html {
        @if ( $filter['page_size'] !== 'K80' )
            width: calc(<?php echo $css['page']['width']; ?>*3.779527559055px);
            height: calc(<?php echo $css['page']['height']; ?>*3.779527559055px);
        @endif
        overflow: hidden;
    }
    .body-html-children {
        padding: 5px;
        float:left;
        @if ( $filter['page_size'] !== 'K80' )
            width: calc( (<?php echo $css['page_children']['width']; ?>*3.779527559055px) - 10px);
            height: calc( (<?php echo $css['page_children']['height']; ?>*3.779527559055px) - 10px);
        @else
            width: 100%;
        @endif
        overflow: hidden;
    }
    .print-setting {
        position: absolute;
        top: 30px;
        left: calc(<?php echo $css['page']['width']; ?>*4px);
        background-color: #f7f7f7;
        border: solid 1px;
        padding: 10px;
    }
    .print {
        width: 100px;
        height: 50px;
    }
    body {
        margin: 0px !important;
    }
</style>
<div class="row print-setting">
    @if ( $filter['page_size'] !== 'K80' )
        <form method="GET" action="{{ route('admin.print-orders.print') }}">
            <input type="hidden" name="page_size" value="{{ $filter['page_size'] }}">
            <input type="hidden" name="order_id" value="{{ $filter['order_id'] }}">
            <div >
                <label for="">Mẫu in: </label>
                <select name="type" id="" class="form-control" onchange="handleChange()">
                    <option @if( $current_type == 'doc') selected @endif value="doc">Dọc</option>
                    <option @if( $current_type == 'ngang') selected @endif value="ngang">Ngang</option>
                </select>
            </div>
            @if ($number_order > 1 && $filter['page_size'] !== 'K80')
            <div >
                <label for="">Bản ghi/trang: </label>
                <select name="per_page" id="" class="form-control" onchange="handleChange()">
                    @foreach($per_page[$filter['page_size']] as $key => $val)
                        <option @if($current_per_page == $key) selected @endif value="{{$key}}">{{$key}}</option>
                    @endforeach
                </select>
            </div>
            @endif
            <button id="change" style="display:none">Thay đổi</button>
        </form>
    @endif
    <button class="print" onclick="window.print();">In</button>
</div>
@foreach ($html as $item)
    <div class="body-html">
        @foreach ($item as $val)
            <div class="body-html-children">
                {!! $val['htmlConvert'] !!}
            </div>
        @endforeach
    </div>
@endforeach

<script>
    function handleChange() {
        document.getElementById("change").click();
    }
</script>