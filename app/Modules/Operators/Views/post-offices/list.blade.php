@extends('layouts.base')

@section('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
@endsection

@section('content')

        <div class="container-fluid">
          <div class="animated fadeIn">
            <div class="row">
              <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                <div class="card">
                    <div class="card-header">
                      <i class="fa fa-align-justify"></i> Quản lý bưu cục
                        @if($currentUser->can('action_post_offices_create'))
                          <span class="float-right">
                              <a href="{{ route('admin.post-offices.create') }}" class="btn btn-primary">Thêm mới</a>
                          </span>
                        @endif
                    </div>
                    <div class="card-body table-responsive">
                        <table class="table table-striped">
                        <thead>
                          <tr>
                            <th style="min-width: 100px">Tỉnh thành</th>
                            <th style="min-width: 120px">Quận huyện</th>
                            <th style="min-width: 100px">Phường xã</th>
                            <th style="min-width: 120px">Tên bưu cục</th>
                            {{--<th width="40px"></th>--}}
                            @if($currentUser->can('action_post_offices_update'))
                              <th width="40px"></th>
                            @endif
                            {{--<th width="40px"></th>--}}
                          </tr>
                        </thead>
                        <tbody>
                          @foreach($postOffices as $postOffice)
                            <tr>
                              <td>{{ $postOffice->provinces->name }}</td>
                              <td>{{ $postOffice->districts->name }}</td>
                              <td>{{ $postOffice->wards->name }}</td>
                              <td><strong>{{ $postOffice->name }}</strong></td>
                              {{--<td class="pl-0 pr-0">--}}
                                {{--<a href="{{ route('admin.post-offices.show', array('post_office' => $postOffice->id)) }}" class="btn btn-sm btn-block btn-info" title="View">Xem</a>--}}
                              {{--</td>--}}
                              @if($currentUser->can('action_post_offices_update'))
                                <td class="pl-1 pr-1">
                                  <a href="{{ route('admin.post-offices.edit', array('post_office' => $postOffice->id)) }}" class="btn btn-sm btn-block btn-primary" title="Edit">Sửa</a>
                                </td>
                              @endif
                              {{--<td class="pl-0 pr-0">--}}
                                {{--<form action="{{ route('admin.post-offices.destroy', $postOffice->id ) }}" method="POST">--}}
                                    {{--@method('DELETE')--}}
                                    {{--<input type="hidden" name="_token" value="{{ csrf_token() }}" />--}}
                                    {{--<button class="btn btn-sm btn-block btn-danger" title="Delete" onclick="return confirm('Thao tác này không thể hoàn tác! Bạn có chắc chắn xoá?');">Xoá</button>--}}
                                {{--</form>--}}
                              {{--</td>--}}
                            </tr>
                          @endforeach
                        </tbody>
                      </table>
                      {{ $postOffices->withQueryString()->links() }}
                    </div>
                </div>
              </div>
            </div>
          </div>
        </div>

@endsection


@section('javascript')

@endsection

