@extends('layouts.base')

@section('content')

        <div class="container-fluid">
          <div class="animated fadeIn">
            <div class="row">
              <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                <div class="card">
                    <div class="card-header">
                      <i class="fa fa-align-justify"></i> Quản lý mẫu mail</div>
                    <div class="card-body">
                        @if(Session::has('message'))
                            <div class="row">
                                <div class="col-12">
                                    <div class="alert alert-success" role="alert">{{ Session::get('message') }}</div>
                                </div>
                            </div>
                         @endif
                        <div class="row">
                          <a href="{{ route('admin.mail.create') }}" class="btn btn-primary m-2">Tạo mới mẫu mail</a>
                        </div>
                        <br>
                        <table class="table table-responsive-sm table-responsive-md table-responsive-lg table-striped">
                        <thead>
                          <tr>
                            <th style="width:20%">Tên</th>
                            <th style="width:60%">Tiêu đề</th>
                            <th></th>
                          </tr>
                        </thead>
                        <tbody>
                          @foreach($emailTemplates as $mail)
                            <tr>
                              <td><strong>{{ $mail->name }}</strong></td>
                              <td><strong>{{ $mail->subject }}</strong></td>
                              <td class="box-actions">
                                <a href="{{ route('admin.prepareSend', ['id' => $mail->id] ) }}" class="btn btn-warning float-left mr-2">Gửi</a>
                                <a href="{{ route('admin.mail.show', array('mail' => $mail->id)) }}" class="btn btn-primary float-left mr-2">Xem</a>
                                <a href="{{ route('admin.mail.edit', array('mail' => $mail->id)) }}" class="btn btn-primary float-left mr-2">Sửa</a>
                                <form action="{{ route('admin.mail.destroy', $mail->id ) }}" method="POST" class="float-left mr-2">
                                    @method('DELETE')
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                                    <button class="btn btn-danger">Xóa</button>
                                </form>
                              </td>
                            </tr>
                          @endforeach
                        </tbody>
                      </table>
                      {{ $emailTemplates->links() }}
                    </div>
                </div>
              </div>
            </div>
          </div>
        </div>

@endsection


@section('javascript')

@endsection
