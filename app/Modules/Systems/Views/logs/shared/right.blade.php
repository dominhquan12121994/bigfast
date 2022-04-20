<div id="modal_aside_left" class="modal fixed-left fade" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-dialog-aside" role="document">
        <div class="modal-content" style="overflow: auto">
            <form action="{{ route('admin.system.log.index') }}" method="GET" autocomplete="off" id="frmSearch">
                <div class="modal-header">
                    <h5 class="modal-title">Tìm kiếm nâng cao</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" style="overflow-y: visible">
                    <div class="form-group">
                        <div class="btn-group btn-group-toggle" data-toggle="buttons" style="margin-bottom: 0.5rem;">
                            <label class="btn btn-secondary @if( $search['user_type'] == 'user' ) active @endif">
                                <input type="radio" name="user_type" value="user" autocomplete="off" @if( $search['user_type'] == 'user' ) checked @endif > Nhân viên 
                            </label>
                            <label class="btn btn-secondary @if( $search['user_type'] == 'shop' ) active @endif">
                                <input type="radio" name="user_type" value="shop" autocomplete="off" @if( $search['user_type'] == 'shop' ) checked @endif > Cửa hàng
                            </label>
                        </div>
                    </div>
                    <div class="form-group userSelected" @if( $search['user_type'] == 'user' ) style="display:block" @else style="display:none" @endif>
                        <select id="userSelected" class="form-control" name="user_id">
                            @if ($user)
                                <option value="{{ $user['id'] }}">{{ $user['name'] }}</option>
                            @endif
                        </select>
                    </div>
                    <div class="form-group shopSelected" @if( $search['user_type'] == 'shop' ) style="display:block" @else style="display:none" @endif>
                        <select id="shopSelected" class="form-control" name="shop_id">
                            @if ($shop)
                                <option value="{{ $shop['id'] }}">{{ $shop['name'] }}</option>
                            @endif
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Tính năng</label>
                        <select class="form-control" id="log_name" name="log_name">
                            <option value="">Tất cả</option>
                            @foreach( \App\Modules\Systems\Constants\SystemLogConstant::log_name as $key_log => $log )
                            <option @if( $search['log_name'] == $key_log ) selected @endif value="{{ $key_log }}">{{ $log }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Hành vi</label>
                        <input class="form-control" type="text" name="description" value="{{ $search['description'] }}" placeholder="Nhập hành vi cần tìm.." maxlength="2000">
                    </div>
                    <div class="form-group">
                        <label>Phương thức</label>
                        <select class="form-control" id="search_status" name="method">
                            <option value="">Tất cả</option>
                            <option value="GET" @if( $search['method'] == 'GET' ) selected @endif>GET</option>
                            <option value="POST" @if( $search['method'] == 'POST' ) selected @endif>POST</option>
                            <option value="PUT" @if( $search['method'] == 'PUT' ) selected @endif>PUT</option>
                            <option value="DELETE" @if( $search['method'] == 'DELETE' ) selected @endif>DELETE</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                    <a href="{{ route('admin.system.log.index') }}"><button type="button" class="btn btn-info">Trở về</button></a>
                    <button type="submit" class="btn btn-primary">Tìm kiếm</button>
                </div>
            </form>
        </div>
    </div> <!-- modal-bialog .// -->
</div> <!-- modal.// -->