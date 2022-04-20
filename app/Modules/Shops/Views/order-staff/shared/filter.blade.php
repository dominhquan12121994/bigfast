@if($shop && $statusActive)
    <div class="row m-0 mb-2 pt-3 pb-1 border-top">
        <div class="col-auto mr-auto">
            <form class="form-inline" id="frm_filter_orders" action="" method="get" autocomplete="off">
                <label class="mr-2">
                    <i class="c-icon c-icon-xl cil-list-filter"></i>
                </label>
                <div class="form-group mr-3">
                    <label class="mr-1" for="filter_status_detail">Trạng thái</label>
                    <select class="form-control form-control-sm frm_filter_orders" id="filter_status_detail" name="filter_status_detail" onchange="submitFilterOrders();">
                        <option value="0">Tất cả</option>
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
@endif
@if ($statusActive === -1)
    <div class="row m-0 mb-2 pt-3 pb-1 border-top">
        <div class="col-auto mr-auto">
            <form class="form-inline" id="frm_filter_orders" action="" method="get" autocomplete="off">
                <label class="mr-2">
                    <i class="c-icon c-icon-xl cil-list-filter"></i>
                </label>
                <div class="form-group mr-3">
                    <label class="mr-1" for="filter_status_detail">Tìm thấy&nbsp;<b>{{ $total }}</b>&nbsp;kết quả</label>
                </div>
            </form>
        </div>
        <div class="col-auto form-inline">
            @if (count($search) > 0 && !request()->session()->has('order-search-excel'))
                <div class="d-flex justify-content-end">
                    @foreach($search as $key => $value)
                        @if($key === 'created_range' && $value[0] && $value[1])
                            <div class="mr-3" style="font-size: 12px; position: relative; line-height: 20px">
                                <button class="close" type="button" data-dismiss="alert" aria-label="Close" style="position: absolute; right: 0px" onclick="window.location = removeParam(['created_from', 'created_to']);">
                                    <span aria-hidden="true">×</span>
                                </button>
                                Thời gian tạo<br>{{ date('d-m-Y', strtotime($value[0])) }} - {{ date('d-m-Y', strtotime($value[1])) }}
                            </div>
                        @endif
                        @if($key === 'send_success_range' && $value[0] && $value[1])
                            <div class="mr-3" style="font-size: 12px; position: relative; line-height: 20px">
                                <button class="close" type="button" data-dismiss="alert" aria-label="Close" style="position: absolute; right: 0px" onclick="window.location = removeParam(['send_success_from', 'send_success_to']);"><span aria-hidden="true">×</span></button>
                                Giao thành công<br>{{ date('d-m-Y', strtotime($value[0])) }} - {{ date('d-m-Y', strtotime($value[1])) }}
                            </div>
                        @endif
                        @if($key === 'collect_money_range' && $value[0] && $value[1])
                            <div class="mr-3" style="font-size: 12px; position: relative; line-height: 20px">
                                <button class="close" type="button" data-dismiss="alert" aria-label="Close" style="position: absolute; right: 0px" onclick="window.location = removeParam(['collect_money_from', 'collect_money_to']);"><span aria-hidden="true">×</span></button>
                                Chờ đối soát<br>{{ date('d-m-Y', strtotime($value[0])) }} - {{ date('d-m-Y', strtotime($value[1])) }}
                            </div>
                        @endif
                        @if($key === 'reconcile_send_range' && $value[0] && $value[1])
                            <div class="mr-3" style="font-size: 12px; position: relative; line-height: 20px">
                                <button class="close" type="button" data-dismiss="alert" aria-label="Close" style="position: absolute; right: 0px" onclick="window.location = removeParam(['reconcile_send_from', 'reconcile_send_to']);"><span aria-hidden="true">×</span></button>
                                Đối soát giao hàng<br>{{ date('d-m-Y', strtotime($value[0])) }} - {{ date('d-m-Y', strtotime($value[1])) }}
                            </div>
                        @endif
                        @if($key === 'reconcile_refund_range' && $value[0] && $value[1])
                            <div class="mr-3" style="font-size: 12px; position: relative; line-height: 20px">
                                <button class="close" type="button" data-dismiss="alert" aria-label="Close" style="position: absolute; right: 0px" onclick="window.location = removeParam(['reconcile_refund_from', 'reconcile_refund_to']);"><span aria-hidden="true">×</span></button>
                                Đối soát hoàn<br>{{ date('d-m-Y', strtotime($value[0])) }} - {{ date('d-m-Y', strtotime($value[1])) }}
                            </div>
                        @endif
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@endif
