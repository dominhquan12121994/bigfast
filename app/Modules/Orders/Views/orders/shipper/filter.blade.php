<div class="row m-0 mb-2 pt-3 pb-1 border-top">
    <div class="col-auto mr-auto">
        <form class="form-inline" id="frm_filter_orders" action="" method="get" autocomplete="off">
            <label class="mr-2">
                <i class="c-icon c-icon-xl cil-list-filter"></i>
            </label>
            <div class="form-group mr-3">
                <label class="mr-1" for="filter_status_detail">Trạng thái</label>
                <select class="form-control form-control-sm frm_filter_orders" id="filter_status_detail" name="filter_status_detail" onchange="submitFilterOrders();">
                    {{--<option value="0">Tất cả</option>--}}
                    @foreach($arrStatusDetail as $key => $status)
                        <option value="{{ $key }}" {{ $filter['status_detail'] == $key ? 'selected="selected"' : '' }}>{{ $status['name'] }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group mr-2">
                <label class="mr-1" for="filter_daterange">Thời gian</label>
                <input type="text" class="form-control form-control-sm frm_filter_orders" id="filter_daterange" name="filter_daterange">
            </div>
            <button class="btn btn-sm btn-secondary" type="button" id="btn_filter_submit" onclick="submitFilterOrders();">Tìm kiếm</button>
        </form>
    </div>
    <div class="col-auto form-inline">
        <select class="form-control form-control-sm mr-1" id="filter_limit" name="filter_limit">
            @foreach(config('options.page_limit') as $limit)
                <option value="{{ $limit }}" {{ $filter['limit'] == $limit ? 'selected="selected"' : '' }}>{{ $limit }}</option>
            @endforeach
        </select>
        <label for="filter_limit">kết quả một trang</label>
    </div>
</div>
