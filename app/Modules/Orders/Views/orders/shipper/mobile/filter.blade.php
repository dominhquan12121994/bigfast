<div class="row m-0 mb-2 pt-3 pb-1 border-top">
    <div class="col-auto mr-auto">
        <form class="form-inline" id="frm_filter_orders" action="" method="get" autocomplete="off">
            <div class="form-group mr-3">
                <label class="mr-1" for="filter_status_detail" onclick="showDatepicker()"><i class="cil-calendar" style="font-size: 30px;"></i></label>
                <select class="form-control-sm frm_filter_orders" id="filter_status_detail" name="filter_status_detail" onchange="submitFilterOrders();">
                    <option value="0">Tất cả</option>
                    @foreach($arrStatusDetail as $key => $status)
                        <option value="{{ $key }}" {{ $filter['status_detail'] == $key ? 'selected="selected"' : '' }}>{{ $status['name'] }}</option>
                    @endforeach
                </select>
                <input type="text" class="filter_datepicker" style="position:relative; z-index:-1; float:left; width: 1px" />
            </div>
            <button class="btn btn-sm btn-secondary" type="button" id="btn_filter_submit" onclick="submitFilterOrders();" style="display:none;">Tìm kiếm</button>
        </form>
    </div>
</div>