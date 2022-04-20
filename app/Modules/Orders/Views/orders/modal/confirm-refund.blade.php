<form action="" method="post" id="frm_confirm_refund">
    <input type="hidden" name="_token" value="{{ csrf_token() }}" />
    <div class="form-group row mb-1">
        <label class="col-md-3 col-form-label required" for="confirm_refund_reason">Lý do</label>
        <div class="col-md-9">
            <select class="form-control" id="confirm_refund_reason" name="confirm_refund_reason">
                <option value="Không liên lạc được với người mua nhiều lần">Không liên lạc được với người mua nhiều lần</option>
                <option value="Liên lạc được nhưng tới địa chỉ người nhận đi vắng">Liên lạc được nhưng tới địa chỉ người nhận đi vắng</option>
                <option value="Người nhận vì 1 lý do nào đó hẹn chuyển phát lần sau">Người nhận vì 1 lý do nào đó hẹn chuyển phát lần sau</option>
                <option value="Người nhận từ chối nhận hàng khi hàng hóa không đúng như mô tả, sai màu sắc, kích thước">Người nhận từ chối nhận hàng khi hàng hóa không đúng như mô tả, sai màu sắc, kích thước</option>
                <option value="Thông tin người nhận không chính xác (địa chỉ, số điện thoại)">Thông tin người nhận không chính xác (địa chỉ, số điện thoại)</option>
                <option value="Hàng hư hỏng">Hàng hư hỏng</option>
                <option value="Lý do khác">Lý do khác</option>
            </select>
        </div>
    </div>
    <p>Thao tác này không thể hoàn tác. Bạn có chắc chắn?</p>
    <div class="row justify-content-center">
        <button class="btn btn-info" type="submit">Xác nhận</button>
        <button class="btn btn-secondary ml-2" type="button" data-dismiss="modal">Huỷ</button>
    </div>
</form>
