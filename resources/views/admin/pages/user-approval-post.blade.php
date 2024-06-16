@extends('admin.layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-offset-1 col-md-12">
                <div class="panel">
                    <div class="panel-heading">
                        <div class="row">
                            <div class="col-md-6">
                                <h4 class="title">Danh sách người duyệt bài</h4>
                            </div>
                            <div class="col-md-6 text-right">
                                <a href="{{ route('create.user') }}" class="btn btn-default ml-2" title="Thêm tài khoản"><i class="fa-solid fa-plus"></i></a>
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
                                @foreach ($userPost as $user)
                                    <tr>
                                        <td class="d-none d-md-table-cell">{{ $user->id }}</td>
                                        <td>{{ $user->full_name }}</td>
                                        <td class="d-none d-md-table-cell">{{ $user->username }}</td>
                                        <td>
                                            <p><span class="font-weight-bold">Đã duyệt:</span> <span class="badge bg-primary rounded-pill">{{ $user->count_post }}  bài</span></p>
                                        </td>
                                        <td class="d-none d-md-table-cell">
                                            <p class="my-0"><span class="font-weight-bold">Ngày tạo   :</span>  {{ $user->created_at }}</p>
                                            <p class="my-0"><span class="font-weight-bold">Cập nhật    :</span>  {{ $user->updated_at }}</p>
                                        </td>
                                        <td>
                                            @if ($user->status == 'active')
                                                <span class="badge badge-success">active</span>
                                            @else
                                                <span class="badge badge-danger">inactive</span>
                                            @endif
                                        </td>
                                        <td>
                                            <ul class="action-list">
                                                <li><a href="{{ route('edit.user',$user->id) }}" class="edit-user" data-tip="Sửa"><i class="fa fa-edit text-info"></i></a></li>
                                                <li><a href="#" class="delete-user" data-id="{{ $user->id }}" data-tip="Xóa"><i class="fa fa-trash text-danger"></i></a></li>
                                                <li><a href="{{ route('show.user.approval',$user->id) }}" data-tip="Xem"><i class="fa-regular fa-eye text-success"></i></a></li>
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
                                Hiển thị <b>{{ $userPost->count() }}</b> trên <b>{{ $userPost->total() }}</b> thể loại
                            </div>
                            <div class="col-sm-6 col-xs-6">
                                <div id="pagination">
                                    {{ $userPost->links('vendor.pagination.custom') }}
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

    </script>
@endpush