<div id="modal_aside_left" class="modal fixed-left fade" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-dialog-aside" role="document">
        <div class="modal-content" style="overflow: auto">
            <form action="{{ route('shop.order-staff.search') }}" method="post" autocomplete="off" id="frmSearch" enctype="multipart/form-data">
                <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                <div class="modal-header">
                    <h5 class="modal-title text-danger font-weight-bolder">Tìm kiếm nâng cao</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" style="overflow-y: visible">
                    <div class="form-group">
                        <label>Mã vận đơn</label>
                        <input class="form-control" type="text" name="code" value="{{ $search['code'] }}" placeholder="Nhập mã vận đơn cần tìm.." maxlength="2000">
                        <span class="help-block font-sm">Tối đa 100 mã hoặc</span>
                        <input type="file" id="fileSearch" name="fileSearch" onchange="document.getElementById('frmSearch').submit();" style="display: none"/>
                        <a onclick="document.getElementById('fileSearch').click()" style="cursor: pointer"><b>Tìm kiếm theo Excel 1000</b></a>
                    </div>
                    <div class="form-group order_status_container">
                        <label>Trạng thái đơn</label>
                        <div>
                            <select class="form-control" id="search_status" multiple="multiple">
                                @foreach(\App\Modules\Orders\Constants\OrderConstant::status as $key => $status)
                                    @if($key > 0)
                                        <optgroup label="{{ $status['name'] }}">
                                            @foreach($status['detail'] as $key_detail => $status_detail)
                                            <option value="{{ $key_detail }}" {{ in_array($key_detail, $search['status_detail']) ? 'selected="selected"' : '' }}>{{ $status_detail['name'] }}</option>
                                            @endforeach
                                        </optgroup>
                                    @endif
                                @endforeach
                            </select>
                            <input type="hidden" name="status_arr" id="statusList" value="{{ implode(',', $search['status_detail']) }}">
                            <input type="hidden" name="search" value="1">
                            @if ($shop)<input type="hidden" name="shop" value="{{ $shop->id }}">@endif
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Thời gian tạo đơn</label>
                        <input type="text" class="form-control form-control-sm" id="search_daterange">
                        <input type="hidden" name="created_from" id="search_created_from" value="{{ date('d-m-Y', strtotime($search['created_range'][0])) }}">
                        <input type="hidden" name="created_to" id="search_created_to" value="{{ date('d-m-Y', strtotime($search['created_range'][1])) }}">
                    </div>
                    <div class="form-group">
                        <label>Giao hàng thành công</label>
                        <input type="text" class="form-control form-control-sm" id="search_daterange2">
                        <input type="hidden" name="send_success_from" id="search_send_success_from" value="{{ $search['send_success_range'][0] ? date('d-m-Y', strtotime($search['send_success_range'][0])) : $search['send_success_range'][0] }}">
                        <input type="hidden" name="send_success_to" id="search_send_success_to" value="{{ $search['send_success_range'][1] ? date('d-m-Y', strtotime($search['send_success_range'][1])) : $search['send_success_range'][1] }}">
                    </div>
                    <div class="form-group">
                        <label>Chờ đối soát</label>
                        <input type="text" class="form-control form-control-sm" id="search_daterange3">
                        <input type="hidden" name="collect_money_from" id="search_collect_money_from" value="{{ $search['collect_money_range'][0] ? date('d-m-Y', strtotime($search['collect_money_range'][0])) : $search['collect_money_range'][0] }}">
                        <input type="hidden" name="collect_money_to" id="search_collect_money_to" value="{{ $search['collect_money_range'][1] ? date('d-m-Y', strtotime($search['collect_money_range'][1])) : $search['collect_money_range'][1] }}">
                    </div>
                    <div class="form-group">
                        <label>Đã đối soát giao hàng</label>
                        <input type="text" class="form-control form-control-sm" id="search_daterange4">
                        <input type="hidden" name="reconcile_send_from" id="search_reconcile_send_from" value="{{ $search['reconcile_send_range'][0] ? date('d-m-Y', strtotime($search['reconcile_send_range'][0])) : $search['reconcile_send_range'][0] }}">
                        <input type="hidden" name="reconcile_send_to" id="search_reconcile_send_to" value="{{ $search['reconcile_send_range'][1] ? date('d-m-Y', strtotime($search['reconcile_send_range'][1])) : $search['reconcile_send_range'][1] }}">
                    </div>
                    <div class="form-group">
                        <label>Đã đối soát hoàn hàng</label>
                        <input type="text" class="form-control form-control-sm" id="search_daterange5">
                        <input type="hidden" name="reconcile_refund_from" id="search_reconcile_refund_from" value="{{ $search['reconcile_refund_range'][0] ? date('d-m-Y', strtotime($search['reconcile_refund_range'][0])) : $search['reconcile_refund_range'][0] }}">
                        <input type="hidden" name="reconcile_refund_to" id="search_reconcile_refund_to" value="{{ $search['reconcile_refund_range'][1] ? date('d-m-Y', strtotime($search['reconcile_refund_range'][1])) : $search['reconcile_refund_range'][1] }}">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                    <a href="{{ route('shop.order-staff.index') }}"><button type="button" class="btn btn-info">Trở về</button></a>
                    <button type="submit" class="btn btn-primary">Tìm kiếm</button>
                </div>
            </form>
        </div>
    </div> <!-- modal-bialog .// -->
</div> <!-- modal.// -->
