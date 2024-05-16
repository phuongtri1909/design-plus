@extends('admin.layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-offset-1 col-md-12">
                <div class="panel">
                    <div class="panel-heading">
                        <div class="row">
                            <div class="col col-md-6 col-xs-12">
                                <h4 class="title">Danh sách phóng viên</h4>
                            </div>
                            <div class="col-md-6 col-xs-12 text-right">
                                <div class="row">
                                    <input type="text" id="search-reporter" class="form-control input-search col-10 ml-2" placeholder="Tìm kiếm phóng viên">
                                    <a href="{{ route('create.user') }}" class="btn btn-default ml-2" title="Thêm tài khoản"><i class="fa-solid fa-plus"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="panel-body table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th class="d-none d-md-table-cell">ID</th>
                                    <th>Họ & Tên</th>
                                    <th class="d-none d-md-table-cell">Tài khoản</th>
                                    <th>Thông tin</th>
                                    <th class="d-none d-md-table-cell">Ngày</th>
                                    <th>Trạng thái</th>
                                    <th>action</th>
                                </tr>
                            </thead>
                            <tbody class="reporter-body">
                                @foreach ($reporters as $reporter)
                                    <tr>
                                        <td class="d-none d-md-table-cell">{{ $reporter->id }}</td>
                                        <td>{{ $reporter->full_name }}</td>
                                        <td class="d-none d-md-table-cell">{{ $reporter->username }}</td>
                                        <td>
                                            <p><span class="font-weight-bold">Chưa duyệt:</span>{{ $reporter->count_no_approval }}</p>
                                           
                                            <p><span class="font-weight-bold">Đã đăng:</span>{{ $reporter->count_push_post }}/{{ $reporter->count_approval }}</p>
                                            <hr class="my-0">
                                            <p><span class="font-weight-bold">Tổng:</span> {{ $reporter->total_posts }}</p>
                                        
                                        </td>
                                        <td class="d-none d-md-table-cell">
                                            <p class="my-0"><span class="font-weight-bold">Thêm    :</span>  {{ $reporter->created_at }}</p>
                                            <p class="my-0"><span class="font-weight-bold">Cập nhật    :</span>  {{ $reporter->updated_at }}</p>
                                        </td>
                                        <td>
                                            @if ($reporter->status == 'active')
                                                <span class="badge badge-success">active</span>
                                            @else
                                                <span class="badge badge-danger">inactive</span>
                                            @endif
                                        </td>
                                        <td>
                                            <ul class="action-list">
                                                <li><a href="{{ route('edit.user',$reporter->id) }}" class="edit-user" data-tip="Sửa"><i class="fa fa-edit text-info"></i></a></li>
                                                <li><a href="#" class="delete-user" data-id="{{ $reporter->id }}" data-tip="Xóa"><i class="fa fa-trash text-danger"></i></a></li>
                                                <li class="d-md-none"><a href="{{ route('show.user',$reporter->id) }}" data-tip="Xem"><i class="fa-regular fa-eye text-success"></i></a></li>
                                            </ul>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    
                    </div>
                    <div class="panel-footer">
                        <div class="row">
                            
                            <div class="col col-sm-6 col-xs-6">
                                Hiển thị <b>{{ $reporters->count() }}</b> trên <b>{{ $reporters->total() }}</b> thể loại
                            </div>
                            <div class="col-sm-6 col-xs-6">
                                <div id="pagination">
                                    {{ $reporters->links('vendor.pagination.custom') }}
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
                    <!-- Message will be inserted here -->
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title dp-color" id="deleteModalLabel">Xác nhận xóa</h5>
            </div>
            <div class="modal-body">
                Bạn có chắc chắn muốn xóa tài khoản này không?
            </div>
            <div class="modal-footer">
               
                <form id="deleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-dp btn-apply text-light">Xóa</button>
                </form>
            
            </div>
          </div>
        </div>
    </div>
@endsection

@push('script-admin')
    <script>
        $(document).ready(function() {
            $('body').on('click', '.delete-user', function(e) {
                e.preventDefault();
                var userId = $(this).data('id');
                var url = '/delete-user/' + userId;
                $('#deleteForm').attr('action', url);
                $('#deleteModal').modal('show');
            });
        });
        $(document).ready(function() {
            @if (session('success'))
                $('#messageContent').text('{{ session('success') }}');
                $('#messageModal').modal('show');
            @endif
            @if (session('error'))
                $('#messageContent').text('{{ session('error') }}');
                $('#messageModal').modal('show');
            @endif
        });

        $(document).ready(function(){
            var timeout = null;
            $('#search-reporter').on('input', function(e){
                clearTimeout(timeout);
                var search = $(this).val();
                timeout = setTimeout(function() {
                    
                    $.ajax({
                        url: "{{ route('search.user') }}",
                        method: 'GET',
                        data: {search: search},
                        success: function(results) {
                            console.log(results.data.data);
                            var tbody = $('.reporter-body');
                            tbody.empty();
                            $.each(results.data.data, function(index, reporter) {
                                createBody(reporter,tbody);
                            });
                            $('#pagination').html(results.links);

                            $(document).on('click', '.pagination a', function(event){
                                event.preventDefault(); 
                                var page = $(this).attr('href').split('page=')[1];
                                fetch_data(page);
                            });
                        
                            function fetch_data(page){
                                $.ajax({
                                    url:"/search-user?page="+page,
                                    method: 'get',
                                    data: {search: search},
                                    success:function(results){
                                        var tbody = $('.reporter-body');
                                        tbody.empty();
                                        $.each(results.data.data, function(index, reporter) {
                                            createBody(reporter,tbody);
                                        });
                                        $('#pagination').html(results.links);
                                    }
                                });
                            };
                        }
                    });
                }, 1000);
            });

            function formatDate(date) {
                return date.getFullYear() + '-' +
                    ('0' + (date.getMonth()+1)).slice(-2) + '-' +
                    ('0' + date.getDate()).slice(-2) + ' ' +
                    ('0' + date.getHours()).slice(-2) + ':' +
                    ('0' + date.getMinutes()).slice(-2) + ':' +
                    ('0' + date.getSeconds()).slice(-2);
            }
            function createBody(reporter,tbody)
            {

                var createdAtDate = new Date(reporter.created_at);
                var created_at = formatDate(createdAtDate);

                var updatedAtDate = new Date(reporter.updated_at);
                var updated_at = formatDate(updatedAtDate);

                let statusBadge = reporter.status === 'active' 
                ? '<span class="badge badge-success">active</span>' 
                : '<span class="badge badge-danger">inactive</span>';

            let row = `
                <tr>
                    <td class="d-none d-md-table-cell">${reporter.id}</td>
                    <td>${reporter.full_name}</td>
                    <td class="d-none d-md-table-cell">${reporter.username}</td>
                    <td>
                        <p><span class="font-weight-bold">Chưa duyệt:</span>${reporter.count_no_approval}</p>
                        <p><span class="font-weight-bold">Đã đăng:</span>${reporter.count_push_post}/${reporter.count_approval}</p>
                        <hr class="my-0">
                        <p><span class="font-weight-bold">Tổng:</span> ${reporter.total_posts}</p>
                    </td>
                    <td class="d-none d-md-table-cell">
                        <p class="my-0"><span class="font-weight-bold">Thêm    :</span>  ${created_at}</p>
                        <p class="my-0"><span class="font-weight-bold">Cập nhật    :</span>  ${updated_at}</p>
                    </td>
                    <td>
                        ${statusBadge}
                    </td>
                    <td>
                        <ul class="action-list">
                            <li><a href="edit-user/${reporter.id}" class="edit-user" data-tip="Sửa"><i class="fa fa-edit text-info"></i></a></li>
                            <li><a href="#" class="delete-user" data-id="${reporter.id}" data-tip="Xóa"><i class="fa fa-trash text-danger"></i></a></li>
                            <li class="d-md-none"><a href="show-user/${reporter.id}" data-tip="Xem"><i class="fa-regular fa-eye text-success"></i></a></li>
                        </ul>
                    </td>
                </tr>
            `;

                tbody.append(row);
            }
        });
    </script>
@endpush