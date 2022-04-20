<form action="" method="post" id="frm_reconcile_damaged">
    <input type="hidden" name="_token" value="{{ csrf_token() }}" />
    {{--<div class="form-group row mb-1">--}}
        {{--<label class="col-md-3 col-form-label required">Số tiền bồi thường</label>--}}
        {{--<div class="col-md-9">--}}
            {{--<input name="money_indemnify" class="form-control" type="text" value="" required placeholder="Nhập số tiền bồi thường">--}}
        {{--</div>--}}
    {{--</div>--}}
    <div class="form-group row mb-1">
        <label class="col-md-3 col-form-label required" for="reconcile_damaged_note">Mô tả</label>
        <div class="col-md-9">
            <textarea class="form-control" id="reconcile_damaged_note" name="reconcile_damaged_note" rows="3" placeholder="Mô tả chi tiết" required></textarea>
        </div>
    </div>
    <p>Thao tác này không thể hoàn tác. Bạn có chắc chắn?</p>
    <div class="row justify-content-center">
        <button class="btn btn-info" type="submit">Xác nhận</button>
        <button class="btn btn-secondary ml-2" type="button" data-dismiss="modal">Huỷ</button>
    </div>
</form>