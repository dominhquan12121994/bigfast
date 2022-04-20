<form action="" method="post" id="frm_re_shipper_pick">
    <input type="hidden" name="_token" value="{{ csrf_token() }}" />
    <p style="text-align: center">Bạn có chắc chắn?</p>
    <div class="row justify-content-center">
        <button class="btn btn-info" type="submit">Xác nhận</button>
        <button class="btn btn-secondary ml-2" type="button" data-dismiss="modal">Huỷ</button>
    </div>
</form>