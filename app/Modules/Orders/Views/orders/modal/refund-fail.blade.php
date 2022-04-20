<form action="" method="post" id="frm_refund_fail">
    <input type="hidden" name="_token" value="{{ csrf_token() }}" />
    <div class="form-group row mb-1">
        <label class="col-md-3 col-form-label required" for="fail_note">Mô tả</label>
        <div class="col-md-9">
            <textarea class="form-control" id="fail_note" name="fail_note" rows="3" placeholder="Mô tả chi tiết" required></textarea>
        </div>
    </div>
    <p>Thao tác này không thể hoàn tác. Bạn có chắc chắn?</p>
    <div class="row justify-content-center">
        <button class="btn btn-info" type="submit">Xác nhận</button>
        <button class="btn btn-secondary ml-2" type="button" data-dismiss="modal">Huỷ</button>
    </div>
</form>