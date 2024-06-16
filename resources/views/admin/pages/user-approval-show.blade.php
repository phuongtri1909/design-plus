@extends('admin.layouts.app')
<style>
    .card {
        border-radius: 10px !important;
    }

    .limiter {
        width: 100%;
        margin: 0 auto;
    }
</style>
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-offset-1 col-md-12">
                <div class="panel pb-2">
                    <div class="panel-heading">
                        <div>
                            <a href="{{ route('list.user.approval') }}" class="dp-color"><i
                                    class="fa-solid fa-arrow-left "></i>
                                Trở lại</a>
                        </div>
                        <div class="card">
                            <div class="card-body text-center">
                                <h4 class="card-title m-b-0">Thông tin người duyệt bài: {{ $user->full_name }}</h4>
                            </div>
                            <div class="infomation mx-1 mx-md-5">
                                <p class="text-dark"><span class="font-weight-bold">Tài khoản:</span> {{ $user->username }}
                                </p>
                                <p class="text-dark"><Span class="font-weight-bold">Ngày đăng ký:</Span>
                                    {{ $user->created_at }}</p>
                                <p class="text-dark"><Span class="font-weight-bold">Ngày cập nhật:</Span>
                                    {{ $user->updated_at }}
                                </p>
                                <p class="text-dark"><Span class="font-weight-bold">Trạng thái:</Span>
                                    @if ($user->status == 'active')
                                        <span class="badge badge-success">active</span>
                                    @else
                                        <span class="badge badge-danger">inactive</span>
                                    @endif
                                </p>

                            </div>
                        </div>
                    </div>


                    <hr>
                    <div class="panel-heading">
                        <div class=" mt-3">
                            <div class="card">
                                <div class="card-body text-center pb-0">
                                    <h3 class="card-title m-b-0 font-weight-bold">Báo Cáo</h3>
                                </div>

                                <div class="mx-1 mx-md-5 text-center">
                                    <input class="form-control input-date" type="text" name="daterange"
                                        value="{{ now()->startOfMonth()->format('m/d/Y') }} - {{ now()->endOfMonth()->format('m/d/Y') }}" />
                                    <div class="limiter  mt-3">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th class="text-center">Số lượng</th>
                                                    <th class="text-center">Chi tiết</th>
                                                </tr>
                                            </thead>
                                            <tbody class="reporter-body">
                                               
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="messageModal" tabindex="-1" aria-labelledby="messageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title dp-color" id="messageModalLabel">Thông báo</h5>
            
            </div>
            <div class="modal-body" id="messageContent">
                
            </div>
        </div>
        </div>
    </div>
@endsection
@push('script-admin')

    <script>
        $(document).ready(function() {
            function fetchData(start, end) {
                $.ajax({
                    url: "{{ route('report.user.approval') }}",
                    method: 'POST',
                    data: {
                        user_id: {{ $user->id }},
                        start: start.format('YYYY-MM-DD 00:00:00'),
                        end: end.format('YYYY-MM-DD 23:59:59'),
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(results) {
                        if(results.error){
                            $('#messageModal').modal('show');
                            $('#messageContent').text(results.error);
                        }else{
                            var $tbody = $(".reporter-body");
                            $tbody.empty();
    
    
                            var $row = $("<tr></tr>");
                            $row.append($("<td class='text-center'></td>").text(results.totalPosts));
    
                            var $postsCell = $("<td></td>");
                            results.totalRecords.forEach(function(reporter) {
                                $postsCell.append($("<p></p>").html(
                                    "<span class='font-weight-bold'>" + reporter.reporter_name +
                                    ": </span>" + reporter.count + " bài"));
                            });
    
                            $row.append($postsCell);
                            $tbody.append($row);
                        }

                    }
                });
            }


            $('input[name="daterange"]').daterangepicker({
                opens: 'left'
            }, function(start, end, label) {
                fetchData(start, end);
            });

            var start = moment().startOf('month');
            var end = moment().endOf('month');
            fetchData(start, end);
        });
    </script>
@endpush
