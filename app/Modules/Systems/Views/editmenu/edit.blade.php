@extends('layouts.base')

@section('content')


    <div class="container-fluid">
        <div class="fade-in">
            <div class="row">
                <div class="col-sm-12">
                    <form action="{{ route('admin.menu.update') }}" method="POST">
                        <div class="card">
                            <div class="card-header justify-content-between d-flex">
                                <div>
                                    <h4>Cập nhật menu</h4>
                                </div>
                                <div>
                                    <button class="btn btn-primary" type="submit">Cập nhật</button>
                                    <a class="btn btn-primary"
                                       href="{{ route('admin.menu.index', ['menu' => $menuElement->menu_id]) }}">Quay
                                        lại</a>
                                </div>
                            </div>
                            <div class="card-body">
                                @if(Session::has('message'))
                                    <div class="alert alert-success" role="alert">{{ Session::get('message') }}</div>
                                @endif
                                @if ($errors->any())
                                    <div class="alert alert-danger">
                                        <ul>
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                                <input type="hidden" name="_token" value="{{ csrf_token() }}"/>
                                <input type="hidden" name="id" value="{{ $menuElement->id }}" id="menuElementId"/>
                                <table class="table table-striped table-bordered datatable">
                                    <tbody>
                                    <tr>
                                        <th>
                                            Tên menu
                                        </th>
                                        <td>
                                            <select class="form-control" name="menu" id="menu">
                                                @foreach($menulist as $menu1)
                                                    @if($menu1->id == $menuElement->menu_id  )
                                                        <option value="{{ $menu1->id }}"
                                                                selected>{{ $menu1->name }}</option>
                                                    @else
                                                        <option value="{{ $menu1->id }}">{{ $menu1->name }}</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>
                                            Vai trò người dùng
                                        </th>
                                        <td>
                                            <table>
                                                <tr>
                                                    <td width="400px">
                                                        <h4>Khối quản trị</h4>
                                                        <table class="table">
                                                            @foreach($roles as $key => $role)
                                                                <tr>
                                                                    <td>
                                                                        <?php
                                                                        $temp = false;
                                                                        foreach ($menuroles as $menurole) {
                                                                            if ($key == $menurole->role_name) {
                                                                                $temp = true;
                                                                            }
                                                                        }
                                                                        if ($temp === true) {
                                                                            echo '<input checked type="checkbox" name="role[]" value="' . $key . '" class="form-control"/>';
                                                                        } else {
                                                                            echo '<input type="checkbox" name="role[]" value="' . $key . '" class="form-control"/>';
                                                                        }
                                                                        ?>
                                                                    </td>
                                                                    <td>
                                                                        {{ $role['name'] }}
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </table>
                                                    </td>
                                                    <td width="400px">
                                                        <h4>Khối khách hàng</h4>
                                                        <table class="table">
                                                            @foreach($roleShop as $key => $role)
                                                                <tr>
                                                                    <td>
                                                                        <?php
                                                                        $temp = false;
                                                                        foreach ($menuroles as $menurole) {
                                                                            if ($key == $menurole->role_name) {
                                                                                $temp = true;
                                                                            }
                                                                        }
                                                                        if ($temp === true) {
                                                                            echo '<input checked type="checkbox" name="role[]" value="' . $key . '" class="form-control"/>';
                                                                        } else {
                                                                            echo '<input type="checkbox" name="role[]" value="' . $key . '" class="form-control"/>';
                                                                        }
                                                                        ?>
                                                                    </td>
                                                                    <td>
                                                                        {{ $role['name'] }}
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </table>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>
                                            Tên
                                        </th>
                                        <td>
                                            <input
                                                type="text"
                                                class="form-control"
                                                name="name"
                                                value="{{ $menuElement->name }}"
                                                placeholder="Name"
                                            />
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>
                                            Phân loại
                                        </th>
                                        <td>
                                            <select class="form-control" name="type" id="type">
                                                @if($menuElement->slug === 'link')
                                                    <option value="link" selected>Link</option>
                                                @else
                                                    <option value="link">Link</option>
                                                @endif
                                                @if($menuElement->slug === 'title')
                                                    <option value="title" selected>Title</option>
                                                @else
                                                    <option value="title">Title</option>
                                                @endif
                                                @if($menuElement->slug === 'dropdown')
                                                    <option value="dropdown" selected>Dropdown</option>
                                                @else
                                                    <option value="dropdown">Dropdown</option>
                                                @endif
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>
                                            Thông tin khác
                                        </th>
                                        <td>
                                            <div id="div-href">
                                                Đường dẫn:
                                                <input
                                                    type="text"
                                                    name="href"
                                                    class="form-control"
                                                    placeholder="Nhập đường dẫn"
                                                    value="{{ $menuElement->href }}"
                                                />
                                            </div>
                                            <br><br>
                                            <div id="div-dropdown-parent">
                                                Menu cha:
                                                <input type="hidden" id="parentId"
                                                       value="{{ $menuElement->parent_id }}"/>
                                                <select class="form-control" name="parent" id="parent">

                                                </select>
                                            </div>
                                            <br><br>
                                            <div id="div-icon">
                                                Icon - Tìm icon class tại:
                                                <a
                                                    href="https://coreui.io/docs/icons/icons-list/#coreui-icons-free-502-icons"
                                                    target="_blank"
                                                >
                                                    CoreUI icons documentation
                                                </a>
                                                <br>
                                                <input
                                                    class="form-control"
                                                    name="icon"
                                                    type="text"
                                                    placeholder="CoreUI Icon class - ví dụ: cil-bell"
                                                    value="{{ $menuElement->icon }}"
                                                >
                                            </div>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    </div>

@endsection

@section('javascript')
    <script src="{{ asset('js/axios.min.js') }}"></script>
    <script src="{{ asset('js/menu-edit.js') . '?v=' . config('app.version') }}"></script>
@endsection
