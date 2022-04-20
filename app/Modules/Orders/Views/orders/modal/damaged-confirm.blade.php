@if ($currentUser)
    @if ($currentUser->can('action_order_fee_create'))
    <form action="" method="post" id="frm_damaged_confirm">
        <input type="hidden" name="_token" value="{{ csrf_token() }}" />
        <div class="form-group row mb-1">
            <label class="col-md-3 col-form-label required" for="damaged_confirm_indemnify">Bồi thường</label>
            <div class="col-md-9">
                <input name="damaged_confirm_indemnify" id="damaged_confirm_indemnify" class="form-control" type="number" value=""
                       min="0" step="1000" required placeholder="Nhập số tiền bồi thường">
            </div>
        </div>
        <div class="form-group row mb-1">
            <label class="col-md-3 col-form-label required" for="damaged_confirm_note">Mô tả</label>
            <div class="col-md-9">
                <textarea class="form-control" id="damaged_confirm_note" name="damaged_confirm_note" rows="3" placeholder="Mô tả chi tiết" required></textarea>
            </div>
        </div>
        <p>Thao tác này không thể hoàn tác. Bạn có chắc chắn?</p>
        <div class="row justify-content-center">
            <button class="btn btn-info" type="submit">Xác nhận</button>
            <button class="btn btn-secondary ml-2" type="button" data-dismiss="modal">Huỷ</button>
        </div>
    </form>
    @endif
@endif
@if (isset($currentUserApi))
    @if ($currentUserApi->can('action_order_fee_create'))
        <form action="" method="post" id="frm_damaged_confirm">
            <input type="hidden" name="_token" value="{{ csrf_token() }}" />
            <div class="form-group row mb-1">
                <label class="col-md-3 col-form-label required" for="damaged_confirm_indemnify">Bồi thường</label>
                <div class="col-md-9">
                    <input name="damaged_confirm_indemnify" id="damaged_confirm_indemnify" class="form-control" type="number" value=""
                           min="0" step="1000" required placeholder="Nhập số tiền bồi thường">
                </div>
            </div>
            <div class="form-group row mb-1">
                <label class="col-md-3 col-form-label required" for="damaged_confirm_note">Mô tả</label>
                <div class="col-md-9">
                    <textarea class="form-control" id="damaged_confirm_note" name="damaged_confirm_note" rows="3" placeholder="Mô tả chi tiết" required></textarea>
                </div>
            </div>
            <p>Thao tác này không thể hoàn tác. Bạn có chắc chắn?</p>
            <div class="row justify-content-center">
                <button class="btn btn-info" type="submit">Xác nhận</button>
                <button class="btn btn-secondary ml-2" type="button" data-dismiss="modal">Huỷ</button>
            </div>
        </form>
    @endif
@endif