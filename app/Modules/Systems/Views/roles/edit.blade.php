@extends('layouts.base')

@section('css')
    <style>
        .update_button {
            pointer-events: none;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="fade-in">
            <div class="row">
                <div class="col-sm-12">
                    <form method="POST" action="{{ route('admin.roles.update', $role->id) }}">
                        <div class="card">
                            <div class="card-header justify-content-between d-flex">
                                <div>
                                    <h4>Chỉnh sửa vai trò</h4>
                                </div>
                                <div>
                                    <button class="btn btn-primary disabled update_button" type="submit">Cập nhật
                                    </button>
                                    <a class="btn btn-primary" href="{{ route('admin.roles.index') }}">Quay lại</a>
                                </div>
                            </div>
                            <div class="card-body">
                                @if(Session::has('message'))
                                    <div class="alert alert-success" role="alert">{{ Session::get('message') }}</div>
                                @endif
                                <input type="hidden" name="_token" value="{{ csrf_token() }}"/>
                                @method('PUT')
                                <input type="hidden" name="id" value="{{ $role->id }}"/>
                                <table class="table table-bordered datatable">
                                    <tbody>
                                    <tr>
                                        <th>
                                            Tên vai trò
                                        </th>
                                        <td>
                                            <input class="form-control" name="name" value="{{ $role->name }}"
                                                   type="text"/>
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

@endsection
