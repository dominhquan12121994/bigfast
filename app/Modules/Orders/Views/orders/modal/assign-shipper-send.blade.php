<form action="" method="post" id="frm_select_shipper_send">
    <div class="form-group row">
        <label for="select_shipper_send" class="required">Nhân viên giao hàng</label>
        <select class="form-control" id="select_shipper_send" name="select_shipper">
            @if (isset($apiUserShipper))
                @foreach ($apiUserShipper as $user)
                    <option value="{{ $user->id }}">{{ $user->name . ' (' . $user->email . ')' }}</option>
                @endforeach
            @endif
        </select>
    </div>
    <div class="form-group row justify-content-end">
        <button class="btn btn-info" type="submit">Xác nhận</button>
        <button class="btn btn-secondary ml-2" type="button" data-dismiss="modal">Huỷ</button>
    </div>
</form>