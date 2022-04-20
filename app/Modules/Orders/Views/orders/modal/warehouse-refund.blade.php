<form action="" method="post" id="frm_warehouse_refund">
    <div class="form-group row mb-1">
        <label class="col-md-3 col-form-label required" for="post_office">Bưu cục nhận</label>
        <div class="col-md-9">
            <select class="form-control" id="post_office" name="post_office" required>
                @if (isset($apiPostOffices))
                    @foreach ($apiPostOffices as $office)
                        <option value="{{ $office->id }}">{{ $office->name }}</option>
                    @endforeach
                @endif
            </select>
        </div>
    </div>
    <div class="form-group row mb-1">
        <label class="col-md-3 col-form-label required" for="select_user_receiver">Người nhập kho</label>
        <div class="col-md-9">
            <select class="form-control" id="select_user_receiver" name="select_user_receiver" required>
                @if (isset($apiUserAccountancy))
                    @foreach ($apiUserAccountancy as $user)
                        <option value="{{ $user->id }}">{{ $user->name . ' (' . $user->email . ')' }}</option>
                    @endforeach
                @endif
            </select>
        </div>
    </div>
    <div class="form-group row mb-1">
        <label class="col-md-3 col-form-label required" for="warehouse_note">Mô tả</label>
        <div class="col-md-9">
            <textarea class="form-control cls_select_user_receiver" id="warehouse_note" name="warehouse_note" rows="3" placeholder="Mô tả chi tiết"></textarea>
        </div>
    </div>
    <div class="form-group row justify-content-end mb-0">
        <button class="btn btn-info" type="submit">Xác nhận</button>
        <button class="btn btn-secondary ml-2" type="button" data-dismiss="modal">Huỷ</button>
    </div>
</form>