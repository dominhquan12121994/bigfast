<form action="" method="post" id="frm_set_refund">
    <input type="hidden" name="_token" value="{{ csrf_token() }}" />
    <div class="form-group row mb-1">
        <label class="col-md-3 col-form-label required" for="refund_reason">Lý do</label>
        <div class="col-md-9">
            <select class="form-control" id="refund_reason" name="refund_reason">
                <option value="KH từ chối nhận hàng">KH từ chối nhận hàng</option>
                <option value="Bưu tá nhiều lần đi giao hàng nhưng ko liên hệ đc với KH nên yêu cầu chuyển hoàn">Bưu tá nhiều lần đi giao hàng nhưng ko liên hệ đc với KH nên yêu cầu chuyển hoàn</option>
                <option value="Gửi nhầm hàng cho KH">Gửi nhầm hàng cho KH</option>
                <option value="Hàng bị bóp méo trong quá trình vận chuyển">Hàng bị bóp méo trong quá trình vận chuyển</option>
                <option value="Bị cướp đơn (khách mua bên khác rồi)">Bị cướp đơn (khách mua bên khác rồi)</option>
                <option value="Sai thông tin địa chỉ của KH">Sai thông tin địa chỉ của KH</option>
                <option value="KH có nhiều lý do để không nhận đc hàng như: đi công tác, ko có tiền, người nhà ko cho mua, ko đặt hàng...">KH có nhiều lý do để không nhận đc hàng như: đi công tác, ko có tiền, người nhà ko cho mua, ko đặt hàng...</option>
                <option value="Lý do khác">Lý do khác</option>
            </select>
        </div>
    </div>
    <div class="form-group row mb-1">
        <label class="col-md-3 col-form-label" for="refund_note">Mô tả</label>
        <div class="col-md-9">
            <textarea class="form-control" id="refund_note" name="refund_note" rows="3" placeholder="Mô tả chi tiết"></textarea>
        </div>
    </div>
    <p>Thao tác này không thể hoàn tác. Bạn có chắc chắn hoàn đơn hàng?</p>
    <div class="row justify-content-center">
        <button class="btn btn-info" type="submit">Xác nhận</button>
        <button class="btn btn-secondary ml-2" type="button" data-dismiss="modal">Huỷ</button>
    </div>
</form>