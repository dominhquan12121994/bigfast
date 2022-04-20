<style>
    .wrap-text {
        color: red;
    }
</style>
<table class="table table-responsive-sm table-responsive-md table-responsive-lg table-striped" id="table1">
    <thead>
        <tr>
            <th>Người xử lý</th>
            <th>Hoạt động</th>
            <th>Ngày</th>
        </tr>
    </thead>
    <tbody>
        @foreach ( $history as $index1 => $item1)
            <tr>
                <td>{{ $item1->name }}</td>
                <td>
                    @if ($item1->action == 'create')
                        Thêm mới trợ giúp
                    @elseif ($item1->action == 'update')
                        @if ($item1->column == 'assign_id')
                            Gán người trợ giúp cho {{ $item1->new }}
                        @elseif ( $item1->column == 'file_path' )
                            Đã thay đổi file tải lên
                        @elseif ( $item1->column == 'status' )
                            @if ($item1->new == 3)
                                <span class="wrap-text" data-toggle="tooltip" data-html="true"  data-placement="top" title="{{ $item1->refuses }}"> {{ $status[$item1->new] }} yêu cầu </span>
                            @else 
                                {{ $status[$item1->new] }} yêu cầu
                            @endif
                        @else
                            Sửa trường {{ $item1->column }} từ '{{$item1->old}}' thành '{{$item1->new}}'
                        @endif
                    @else
                        Xóa trợ giúp
                    @endif
                </td>
                <td> {{ date('d-m-Y H:i', strtotime($item1->created_at)) }}</td>
            </tr>  
        @endforeach
    </tbody>
</table>

<script type="text/javascript" src="{{ asset('js/tooltips.js') }}"></script>
