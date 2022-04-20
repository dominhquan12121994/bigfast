<form action="" method="post" id="frm_store">
    <div class="form-group row mb-1">
        <label class="col-md-3 col-form-label" for="post_office">Bưu cục lưu kho</label>
        <div class="col-md-9">
            <select class="form-control" id="post_office" name="post_office"></select>
        </div>
    </div>
    <div class="form-group row mb-1">
        <label class="col-md-3 col-form-label" for="select_user_receiver">Người lưu kho</label>
        <div class="col-md-9">
            <select class="form-control" id="select_user_receiver" name="select_user_receiver"></select>
        </div>
    </div>
    <div class="form-group row mb-1">
        <label class="col-md-3 col-form-label" for="store_note">Mô tả</label>
        <div class="col-md-9">
            <textarea class="form-control" id="store_note" name="store_note" rows="3" placeholder="Mô tả chi tiết" required></textarea>
        </div>
    </div>
    <div class="form-group row justify-content-end mb-0">
        <button class="btn btn-info" type="submit">Xác nhận</button>
        <button class="btn btn-secondary ml-2" type="button" data-dismiss="modal">Huỷ</button>
    </div>
</form>