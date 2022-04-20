@extends('layouts.base')

@section('content')
    <div class="container-fluid">
        <div class="fade-in">
            <div class="row">
                <div class="col-sm-12">
                    <form action="{{ route('admin.menu.store') }}" method="POST">
                        <div class="card">
                            <div class="card-header justify-content-between d-flex">
                                <div>
                                    <h4>Thêm mới menu</h4>
                                </div>
                                <div>
                                    <button class="btn btn-primary" type="submit">Thêm mới</button>
                                    <a class="btn btn-primary" href="{{ route('admin.menu.index') }}">Quay lại</a>
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
                                <table class="table table-striped table-bordered datatable">
                                    <tbody>
                                    <tr>
                                        <th>
                                            Menu
                                        </th>
                                        <td>
                                            <select class="form-control" name="menu" id="menu">
                                                @foreach($menulist as $menu1)
                                                    <option value="{{ $menu1->id }}">{{ $menu1->name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>
                                            Vai trò người dùng
                                        </th>
                                        <td>
                                            <table class="table">
                                                <tr>
                                                    <td width="400px">
                                                        <h4>Khối quản trị</h4>
                                                        <table class="table">
                                                            @foreach($roles as $key => $role)
                                                                <tr>
                                                                    <td>
                                                                        <input type="checkbox" name="role[]" value="{{ $key }}"
                                                                            class="form-control"/>
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
                                                                        <input type="checkbox" name="role[]" value="{{ $key }}"
                                                                            class="form-control"/>
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
                                            <input class="form-control" type="text" name="name" placeholder="Name"
                                                   required/>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>
                                            Phân loại
                                        </th>
                                        <td>
                                            <select class="form-control" name="type" id="type">
                                                <option value="link">Link</option>
                                                <option value="title">Title</option>
                                                <option value="dropdown">Dropdown</option>
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
                                                <input type="text" name="href" class="form-control"
                                                       placeholder="Nhập đường dẫn"/>
                                            </div>
                                            <br><br>
                                            <div id="div-dropdown-parent">
                                                Chọn menu cha:
                                                <select class="form-control" name="parent" id="parent">

                                                </select>
                                            </div>
                                            <br><br>
                                            <div id="div-icon">
                                                Icon - Tìm icon class theo đường dẫn:
                                                <a
                                                    href="https://coreui.io/docs/icons/icons-list/#coreui-icons-free-502-icons"
                                                    target="_blank"
                                                >
                                                    CoreUI icons documentation
                                                </a>
                                                <br>
                                                <input class="form-control" name="icon" type="text"
                                                       placeholder="CoreUI Icon class - ví dụ: cil-bell">
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
    <script src="{{ asset('js/menu-create.js') . '?v=' . config('app.version') }}"></script>
@endsection
