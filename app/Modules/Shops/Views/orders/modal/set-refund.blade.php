<form action="" method="post" id="frm_set_refund">
    <div class="form-group row mb-1">
        <label class="col-md-3 col-form-label" for="refund_reason">Lý do</label>
        <div class="col-md-9">
            <select class="form-control" id="refund_reason" name="refund_reason">
                <option value="reason">Lý do không giao được hàng??</option>
            </select>
        </div>
    </div>
    <div class="form-group row mb-1">
        <label class="col-md-3 col-form-label" for="refund_note">Mô tả</label>
        <div class="col-md-9">
            <textarea class="form-control" id="refund_note" name="refund_note" rows="3" placeholder="Mô tả chi tiết" required></textarea>
        </div>
    </div>
    <p>Thao tác này không thể hoàn tác. Bạn có chắc chắn hoàn đơn hàng?</p>
    <div class="row justify-content-center">
        <button class="btn btn-info" type="submit">Xác nhận</button>
        <button class="btn btn-secondary ml-2" type="button" data-dismiss="modal">Huỷ</button>
    </div>
</form>