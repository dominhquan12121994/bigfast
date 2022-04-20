<form action="" method="post" id="frm_warehouse">
    <div class="form-group row mb-1">
        <label class="col-md-3 col-form-label" for="post_office">Bưu cục nhận</label>
        <div class="col-md-9">
            <select class="form-control" id="post_office" name="post_office" required></select>
        </div>
    </div>
    <div class="form-group row mb-1">
        <label class="col-md-3 col-form-label" for="select_user_receiver">Người nhập kho</label>
        <div class="col-md-9">
            <select class="form-control" id="select_user_receiver" name="select_user_receiver"></select>
        </div>
    </div>
    <div class="form-group row mb-1">
        <label class="col-md-3 col-form-label" for="warehouse_note">Mô tả</label>
        <div class="col-md-9">
            <textarea class="form-control" id="warehouse_note" name="warehouse_note" rows="3" placeholder="Mô tả chi tiết" required></textarea>
        </div>
    </div>
    <div class="form-group row justify-content-end mb-0">
        <button class="btn btn-info" type="submit">Xác nhận</button>
        <button class="btn btn-secondary ml-2" type="button" data-dismiss="modal">Huỷ</button>
    </div>
</form>