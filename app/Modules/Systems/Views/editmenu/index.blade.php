@extends('layouts.base')

@section('css')
    <style>
        .icon_container {
            text-align: center;
        }

        .icon_container svg {
            display: none;
        }
    </style>
@endsection

@section('content')


    <div class="container-fluid">
        <div class="fade-in">
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header"><h4>Danh sách Menu</h4></div>
                        <div class="card-body">
                            <div class="row mb-3 ml-3">
                            <a class="btn btn-lg btn-primary" href="{{ route('admin.menu.create') }}">Thêm mới menu</a>
                            </div>
                            <div class="row mb-3">
                            <div class="col-sm-4">
                            <form action="{{ route('admin.menu.index') }}" methos="GET">
                            <select class="form-control" name="menu">
                            @foreach($menulist as $menu1)
                            @if($menu1->id == $thisMenu)
                            <option value="{{ $menu1->id }}" selected>{{ $menu1->name }}</option>
                            @else
                            <option value="{{ $menu1->id }}">{{ $menu1->name }}</option>
                            @endif
                            @endforeach
                            </select>
                            <br>
                            <button type="submit" class="btn btn-primary">Đổi menu</button>
                            </form>
                            </div>
                            </div>
                            <?php

                            function renderDropdownForMenuEdit($data, $role)
                            {
                                if (array_key_exists('slug', $data) && $data['slug'] === 'dropdown') {
                                    echo '<tr>';
                                    echo '<td class="icon_container">';
                                    if ($data['hasIcon'] === true && $data['iconType'] === 'coreui') {
                                        echo '<svg class="c-nav-icon edit-menu-icon"><use xlink:href="/assets/icons/coreui/free-symbol-defs.svg#' . $data['icon'] . '"></use></svg>';
                                        echo '<i class="' . $data['icon'] . '"></i>';
                                    }
                                    echo '</td>';
                                    echo '<td>' . $data['slug'] . '</td>';
                                    echo '<td>' . $data['name'] . '</td>';
                                    echo '<td></td>';
                                    echo '<td>' . $data['sequence'] . '</td>';
                                    echo '<td>';
                                    echo '<a class="btn btn-success" href="' . route('admin.menu.up', ['id' => $data['id']]) . '"><i class="cil-arrow-thick-top"></i></a>';
                                    echo '</td>';
                                    echo '<td>';
                                    echo '<a class="btn btn-success" href="' . route('admin.menu.down', ['id' => $data['id']]) . '"><i class="cil-arrow-thick-bottom"></i></a>';
                                    echo '</td>';
                                    echo '<td>';
                                    echo '<a class="btn btn-primary" href="' . route('admin.menu.show', ['id' => $data['id']]) . '">Xem</a>';
                                    echo '</td>';
                                    echo '<td>';
                                    echo '<a class="btn btn-primary" href="' . route('admin.menu.edit', ['id' => $data['id']]) . '">Sửa</a>';
                                    echo '</td>';
                                    echo '<td>';
                                    echo '<a class="btn btn-danger" href="' . route('admin.menu.delete', ['id' => $data['id']]) . '">Xoá</a>';
                                    echo '</td>';
                                    echo '</tr>';
                                    renderDropdownForMenuEdit($data['elements'], $role);
                                } else {
                                    for ($i = 0; $i < count($data); $i++) {
                                        if ($data[$i]['slug'] === 'link') {
                                            echo '<tr>';
//                                            echo '<td>';
//                                            echo '<i class="cil-arrow-thick-to-right"></i>';
//                                            echo '</td>';
                                            echo '<td class="icon_container"></td>';
                                            echo '<td>' . $data[$i]['slug'] . '</td>';
                                            echo '<td>' . $data[$i]['name'] . '</td>';
                                            echo '<td>' . $data[$i]['href'] . '</td>';
                                            echo '<td>' . $data[$i]['sequence'] . '</td>';
                                            echo '<td>';
                                            echo '<a class="btn btn-success" href="' . route('admin.menu.up', ['id' => $data[$i]['id']]) . '"><i class="cil-arrow-thick-top"></i></a>';
                                            echo '</td>';
                                            echo '<td>';
                                            echo '<a class="btn btn-success" href="' . route('admin.menu.down', ['id' => $data[$i]['id']]) . '"><i class="cil-arrow-thick-bottom"></i></a>';
                                            echo '</td>';
                                            echo '<td>';
                                            echo '<a class="btn btn-primary" href="' . route('admin.menu.show', ['id' => $data[$i]['id']]) . '">Xem</a>';
                                            echo '</td>';
                                            echo '<td>';
                                            echo '<a class="btn btn-primary" href="' . route('admin.menu.edit', ['id' => $data[$i]['id']]) . '">Sửa</a>';
                                            echo '</td>';
                                            echo '<td>';
                                            echo '<a class="btn btn-danger" href="' . route('admin.menu.delete', ['id' => $data[$i]['id']]) . '">Xoá</a>';
                                            echo '</td>';
                                            echo '</tr>';
                                        } elseif ($data[$i]['slug'] === 'dropdown') {
                                            renderDropdownForMenuEdit($data[$i], $role);
                                        }
                                    }
                                }
                            }

                            ?>


                            <table class="table table-striped table-bordered datatable">
                                <thead>
                                <tr>
                                    <th></th>
                                    <th>Phân loại</th>
                                    <th>Tên</th>
                                    <th>Đường dẫn</th>
                                    <th>Thứ tự</th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>

                                @foreach($menuToEdit as $menuel)
                                    @if($menuel['slug'] === 'link')
                                        <tr>
                                            <td class="icon_container">
                                                @if($menuel['hasIcon'] === true)
                                                    @if($menuel['iconType'] === 'coreui')
                                                        <svg class="c-nav-icon edit-menu-icon">
                                                            <use xlink:href="/assets/icons/coreui/free-symbol-defs.svg#{{ $menuel['icon'] }}"></use>
                                                        </svg>
                                                        <i class="{{ $menuel['icon'] }}"></i>
                                                    @endif
                                                @endif
                                            </td>
                                            <td>
                                                {{ $menuel['slug'] }}
                                            </td>
                                            <td>
                                                {{ $menuel['name'] }}
                                            </td>
                                            <td>
                                                {{ $menuel['href'] }}
                                            </td>
                                            <td>
                                                {{ $menuel['sequence'] }}
                                            </td>
                                            <td>
                                                <a class="btn btn-success"
                                                   href="{{ route('admin.menu.up', ['id' => $menuel['id']]) }}">
                                                    <i class="cil-arrow-thick-top"></i>
                                                </a>
                                            </td>
                                            <td>
                                                <a class="btn btn-success"
                                                   href="{{ route('admin.menu.down', ['id' => $menuel['id']]) }}">
                                                    <i class="cil-arrow-thick-bottom"></i>
                                                </a>
                                            </td>
                                            <td>
                                                <a class="btn btn-primary"
                                                   href="{{ route('admin.menu.show', ['id' => $menuel['id']]) }}">Xem</a>
                                            </td>
                                            <td>
                                                <a class="btn btn-primary"
                                                   href="{{ route('admin.menu.edit', ['id' => $menuel['id']]) }}">Sửa</a>
                                            </td>
                                            <td>
                                                <a class="btn btn-danger"
                                                   href="{{ route('admin.menu.delete', ['id' => $menuel['id']]) }}">Xoá</a>
                                            </td>
                                        </tr>
                                    @elseif($menuel['slug'] === 'dropdown')
                                        <?php renderDropdownForMenuEdit($menuel, $role) ?>
                                    @elseif($menuel['slug'] === 'title')
                                        <tr>
                                            <td class="icon_container">
                                                @if($menuel['hasIcon'] === true)
                                                    @if($menuel['iconType'] === 'coreui')
                                                        <svg class="c-nav-icon edit-menu-icon">
                                                            <use xlink:href="/assets/icons/coreui/free-symbol-defs.svg#{{ $menuel['icon'] }}"></use>
                                                        </svg>
                                                        <i class="{{ $menuel['icon'] }}"></i>
                                                    @endif
                                                @endif
                                            </td>
                                            <td>
                                                {{ $menuel['slug'] }}
                                            </td>
                                            <td>
                                                {{ $menuel['name'] }}
                                            </td>
                                            <td>

                                            </td>
                                            <td>
                                                {{ $menuel['sequence'] }}
                                            </td>
                                            <td>
                                                <a class="btn btn-success"
                                                   href="{{ route('admin.menu.up', ['id' => $menuel['id']]) }}">
                                                    <i class="cil-arrow-thick-top"></i>
                                                </a>
                                            </td>
                                            <td>
                                                <a class="btn btn-success"
                                                   href="{{ route('admin.menu.down', ['id' => $menuel['id']]) }}">
                                                    <i class="cil-arrow-thick-bottom"></i>
                                                </a>
                                            </td>
                                            <td>
                                                <a class="btn btn-primary"
                                                   href="{{ route('admin.menu.show', ['id' => $menuel['id']]) }}">Xem</a>
                                            </td>
                                            <td>
                                                <a class="btn btn-primary"
                                                   href="{{ route('admin.menu.edit', ['id' => $menuel['id']]) }}">Sửa</a>
                                            </td>
                                            <td>
                                                <a class="btn btn-danger"
                                                   href="{{ route('admin.menu.delete', ['id' => $menuel['id']]) }}">Xoá</a>
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach

                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('javascript')

@endsection
