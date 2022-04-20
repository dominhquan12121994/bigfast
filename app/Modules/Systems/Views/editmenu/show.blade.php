@extends('layouts.base')

@section('content')


<div class="container-fluid">
  <div class="fade-in">
    <div class="row">
      <div class="col-sm-12">
        <div class="card">
          <div class="card-header"><h4>Tra cứu thông tin menu</h4></div>
            <div class="card-body">
                @if(Session::has('message'))
                    <div class="alert alert-success" role="alert">{{ Session::get('message') }}</div>
                @endif

                    <table class="table table-striped table-bordered datatable">
                        <tbody>
                            <tr>
                                <th>
                                    Tên menu
                                </th>
                                <td>
                                    @foreach($menulist as $menu1)
                                        @if($menu1->id == $menuElement->menu_id  )
                                            {{ $menu1->name }}
                                        @endif
                                    @endforeach
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    Vai trò người dùng
                                </th>
                                <td>
                                    <?php
                                        $first = true;
                                        foreach($menuroles as $menurole){
                                            if($first === true){
                                                $first = false;
                                            }else{
                                                echo ', ';
                                            }
                                            echo $menurole->role_name;
                                        }
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    Phân loại
                                </th>
                                <td>
                                    {{ $menuElement->slug }}
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    Đường dẫn
                                </th>
                                <td>
                                    {{ $menuElement->href }}
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    Menu cha
                                </th>
                                <td>
                                    <?php
                                        if(isset($menuElement->parent_name)){
                                            echo $menuElement->parent_name;
                                        }
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    Biểu tượng
                                </th>
                                <td>
                                    <i class="{{ $menuElement->icon }}"></i>
                                    &nbsp;&nbsp;&nbsp;&nbsp;
                                    {{ $menuElement->icon }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <a class="btn btn-primary" href="{{ route('admin.menu.index', ['menu' => $menuElement->menu_id]) }}">Quay lại</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

@endsection

@section('javascript')


@endsection
