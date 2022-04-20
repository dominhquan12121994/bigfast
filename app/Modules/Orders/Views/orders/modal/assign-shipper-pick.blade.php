<form action="" method="post" id="frm_select_shipper_pick">
    <input type="hidden" name="_token" value="{{ csrf_token() }}" />
    <div class="form-group row">
        <label for="select_shipper_pick" class="required">Nhân viên thu gom</label>
        <select class="form-control" id="select_shipper_pick" name="select_shipper">
            @if (isset($apiUserPickup))
                @foreach ($apiUserPickup as $user)
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