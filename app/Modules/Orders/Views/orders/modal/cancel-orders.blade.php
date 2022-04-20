<form action="" method="post" id="frm_cancel_orders">
    <input type="hidden" name="_token" value="{{ csrf_token() }}" />
    <div class="form-group row mb-1">
        <label class="col-md-3 col-form-label required" for="cancel_reason">Lý do</label>
        <div class="col-md-9">
            <select class="form-control" id="cancel_reason" name="cancel_reason">
                <option value="Shop yêu cầu huỷ hàng">Shop yêu cầu huỷ hàng</option>
                <option value="Lý do khác">Lý do khác</option>
            </select>
        </div>
    </div>
    <div class="form-group row mb-3">
        <label class="col-md-3 col-form-label" for="cancel_note">Mô tả</label>
        <div class="col-md-9">
            <textarea class="form-control" id="cancel_note" name="cancel_note" rows="3" placeholder="Mô tả chi tiết"></textarea>
        </div>
    </div>
    <p>Thao tác này không thể hoàn tác. Bạn có chắc chắn huỷ đơn hàng?</p>
    <div class="row justify-content-center">
        <button class="btn btn-info" type="submit">Xác nhận</button>
        <button class="btn btn-secondary ml-2" type="button" data-dismiss="modal">Huỷ</button>
    </div>
</form>