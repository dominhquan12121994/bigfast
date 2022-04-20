<form action="" method="post" id="frm_approval_refund">
    <input type="hidden" name="_token" value="{{ csrf_token() }}" />
    <p>Thao tác này không thể hoàn tác. Bạn có chắc chắn hoàn đơn hàng?</p>
    <div class="row justify-content-center">
        <button class="btn btn-info" type="submit">Xác nhận</button>
        <button class="btn btn-secondary ml-2" type="button" data-dismiss="modal">Huỷ</button>
    </div>
</form>