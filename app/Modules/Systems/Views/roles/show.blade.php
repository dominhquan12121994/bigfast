@extends('layouts.base')

@section('content')


<div class="container-fluid">
  <div class="fade-in">
    <div class="row">
      <div class="col-sm-12">
        <div class="card">
          <div class="card-header" style="font-size: 16px">
              Cấu hình phân quyền: <b>{{ $role->name }}</b>
          </div>
            <div class="card-body">
                @foreach($permissions as $module => $permission)

                    <div class="card">
                        <div class="card-body">
                            <h5>{{ $permission['name'] }}</h5>
                            <div class="d-flex flex-wrap">
                            @php
                                unset($permission['name']);
                            @endphp
                            @foreach($permission as $action => $actionItem)
                                <div style="margin: 10px 50px 0px 50px">
                                    <label class="c-switch c-switch-sm c-switch-label c-switch-pill c-switch-opposite-primary">
                                        <input type="checkbox" class="c-switch-input" {{ $role->hasPermissionTo('action_' . $module . '_' . $action) ? 'checked' : '' }}
                                            name="permissionCbx[]" value="{{ 'action_' . $module . '_' . $action }}" onchange="assignPermission(this)">
                                        <span class="c-switch-slider" data-checked="&#x2713;" data-unchecked="&#x2715;"></span>
                                    </label>
                                    <span class="align-top">{{ $actionItem['name'] }}</span>
                                </div>
                            @endforeach
                            </div>

                        </div>
                    </div>

                @endforeach
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

@endsection

@section('javascript')
    <script type="application/javascript">
        function assignPermission(checkboxElem) {
            let status = 0;
            if (checkboxElem.checked) {
                status = 1;
            }
            let data = {"permission": checkboxElem.value, "status": status, "role": `{{ $role->name }}`};
            $.ajax({
                type: 'PUT',
                beforeSend: function (xhr) {
                    xhr.setRequestHeader ("Authorization", "Bearer {{ $currentUser->passport_token }}");
                },
                url: '{{ route('api.role.permission') }}',
                contentType: 'application/json',
                data: JSON.stringify(data), // access in body
            }).done(function () {
                let message = 'Tước quyền thành công!';
                if (checkboxElem.checked) {
                    message = 'Cấp quyền thành công';
                }
                $.Toast("Thành công", message, "notice");
            }).fail(function (msg) {
                $.Toast("Thất bại", "Cập nhật thất bại!", "error");
            });
        }
    </script>
@endsection