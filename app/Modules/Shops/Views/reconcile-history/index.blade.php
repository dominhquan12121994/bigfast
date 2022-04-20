@extends('layouts.baseShop')

@section('css')
    <link href="{{ asset('libs/datatables/jquery.dataTables.min.css') }}" rel="stylesheet">
    <style type="text/css">
        select + .select2-container {
            width: 200px !important;
        }
        .scroll {
            overflow: auto;
            max-height: 350px;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="fade-in">
            <div class="row">
                <div class="col-12 col-xl-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between">
                            <div class="float-left">
                                <span class="badge badge-success ">Shop</span> {{ $shopInfo->name }} - {{ $shopInfo->phone }}<br>
                                {{ $shopInfo->address }}
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row" id="tbl-reconcile">
                                <div class="col pt-3">
                                    <table class="table table-responsive-sm table-responsive-md table-responsive-lg table-striped" id="table_shops">
                                        <thead>
                                            <th>STT</th>
                                            <th>Shop</th>
                                            <th>Ngày đối soát</th>
                                            <th>Tổng phí</th>
                                            <th>Tổng CoD</th>
                                            <th>Tổng dư</th>
                                        </thead>
                                        <tbody>
                                        @foreach($arrShop as $key => $value)
                                            <tr>
                                                <td>{{ number_format($key + 1) }}</td>
                                                <td>{{ $value->shopInfo->name }}</td>
                                                <td>{{ $value->end_date }}</td>
                                                <td>{{ number_format($value->total_fee) . ' vnđ' }}</td>
                                                <td>{{ number_format($value->total_cod) . ' vnđ' }}</td>
                                                <td>{{ number_format($value->total_du) . ' vnđ' }}</td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-sm-12">
                                    @if ($errors->any())
                                    <div class="alert alert-danger">
                                        <ul>
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('javascript')
<script src="{{ asset('libs/datatables/jquery.dataTables.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('libs/moment/moment.min.js') }}"></script>

<script type="application/javascript">
    $(document).ready(function() {
        console.log('loaded');
        // bảng hiển thị dữ liệu
        let table = $('#table_shops').DataTable({
            "language": {
                "lengthMenu": "Hiển thị _MENU_ bản ghi mỗi trang",
                "zeroRecords": "Không tìm thấy dữ liệu",
                "info": "_PAGE_/_PAGES_ trang",
                "infoEmpty": "Không tìm thấy dữ liệu",
                "infoFiltered": "(tìm kiếm trong tổng số _MAX_ bản ghi)",
                "decimal":        "",
                "emptyTable":     "Không tìm thấy dữ liệu",
                "infoPostFix":    "",
                "thousands":      ",",
                "loadingRecords": "Đang tải...",
                "processing":     "Đang tải...",
                "search":         "Tìm kiếm:",
                "paginate": {
                    "first":      "Đầu",
                    "last":       "Cuối",
                    "next":       "Sau",
                    "previous":   "Trước"
                },
                "aria": {
                    "sortAscending":  ": xếp tăng dần",
                    "sortDescending": ": xếp giảm dần"
                }
            },
            stateSave: true,
        });
        $('#table_shops tbody').on( 'click', 'tr', function () {
            if ( $(this).hasClass('selected') ) {
                $(this).removeClass('selected');
            }
            else {
                table.$('tr.selected').removeClass('selected');
                $(this).addClass('selected');
            }
        } );
    });
</script>
@endsection
