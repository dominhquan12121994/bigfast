<form action="" method="post" id="frm_store">
    <div class="form-group row mb-1">
        <label class="col-md-3 col-form-label required" for="post_office_store">Bưu cục lưu kho</label>
        <div class="col-md-9">
            <select class="form-control" id="post_office_store" name="post_office" required>
                @if (isset($apiPostOffices))
                    @foreach ($apiPostOffices as $office)
                        <option value="{{ $office->id }}">{{ $office->name }}</option>
                    @endforeach
                @endif
            </select>
        </div>
    </div>
    <div class="form-group row mb-1">
        <label class="col-md-3 col-form-label required" for="select_user_receiver_store">Người lưu kho</label>
        <div class="col-md-9">
            <select class="form-control" id="select_user_receiver_store" name="select_user_receiver" required>
                @if (isset($apiUserAccountancy))
                    @foreach ($apiUserAccountancy as $user)
                        <option value="{{ $user->id }}">{{ $user->name . ' (' . $user->email . ')' }}</option>
                    @endforeach
                @endif
            </select>
        </div>
    </div>
    <div class="form-group row mb-1">
        <label class="col-md-3 col-form-label required" for="store_note">Mô tả</label>
        <div class="col-md-9">
            <textarea class="form-control" id="store_note" name="store_note" rows="3" placeholder="Mô tả chi tiết" required></textarea>
        </div>
    </div>
    <div class="form-group row justify-content-end mb-0">
        <button class="btn btn-info" type="submit">Xác nhận</button>
        <button class="btn btn-secondary ml-2" type="button" data-dismiss="modal">Huỷ</button>
    </div>
</form>