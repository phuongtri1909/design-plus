@extends('admin.layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-offset-1 col-md-12">
                <div class="panel">
                    <div class="panel-heading">
                        <div class="row">
                            <div class="col col-md-6 col-xs-12">
                                <h4 class="title">Thể loại bài viết</h4>
                            </div>
                            <div class="col-md-6 col-xs-12 text-right">
                                <div class="row">
                                    <input type="text" id="search" class="form-control input-search col-9 ml-2" placeholder="Tìm kiếm thể loại">
                                    <a href="{{ route('categories.create') }}" class="btn btn-default ml-2" title="Thêm thể loại"><i class="fa-solid fa-plus"></i></a>
                                   {{-- <button class="btn btn-default" title="Pdf"><i class="fa fa-file-pdf"></i></button>
                                    <button class="btn btn-default" title="Excel"><i class="fas fa-file-excel"></i></button> --}}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="panel-body table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Tên</th>
                                    <th>Slug</th>
                                    <th class="d-none d-md-table-cell">Ngày tạo</th>
                                    <th class="d-none d-md-table-cell">Ngày cập nhật</th>
                                    <th>action</th>
                                </tr>
                            </thead>
                            <tbody class="category-body">
                                @foreach ($categories as $category)
                                    <tr>
                                        <td>{{ $category->id }}</td>
                                        <td>{{ $category->name }}</td>
                                        <td>{{ $category->slug }}</td>
                                        <td class="d-none d-md-table-cell">{{ $category->created_at }}</td>
                                        <td class="d-none d-md-table-cell">{{ $category->updated_at }}</td>
                                        <td>
                                            <ul class="action-list">
                                                <li><a href="{{ route('categories.edit',$category->id) }}" class="edit-category" data-tip="Sửa"><i class="fa fa-edit text-info"></i></a></li>
                                                <li><a href="#" class="delete-category" data-id="{{ $category->id }}" data-tip="Xóa"><i class="fa fa-trash text-danger"></i></a></li>
                                                <li class="d-md-none"><a href="{{ route('categories.show',$category->id) }}" data-tip="Xem"><i class="fa-regular fa-eye text-success"></i></a></li>
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
                                Hiển thị <b>{{ $categories->count() }}</b> trên <b>{{ $categories->total() }}</b> thể loại
                            </div>
                            <div class="col-sm-6 col-xs-6">
                                <div id="pagination">
                                    {{ $categories->links('vendor.pagination.custom') }}
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
                Bạn có chắc chắn muốn xóa thể loại này không?
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
            $('body').on('click', '.delete-category', function(e) {
                e.preventDefault();
                var categoryId = $(this).data('id');
                var url = '/categories/' + categoryId;
                $('#deleteForm').attr('action', url);
                $('#deleteModal').modal('show');
            });
        });

        $(document).ready(function() {
            $('.btn-save').click(function(e) {
                e.preventDefault();
                $('#formCreateCategory').submit();
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
            $('#search').on('input', function(e){
                clearTimeout(timeout);
                var search = $(this).val();
                timeout = setTimeout(function() {
                    
                    $.ajax({
                        url: "{{ route('categories.search') }}",
                        method: 'GET',
                        data: {search: search},
                        success: function(results) {
                            var tbody = $('.category-body');
                            tbody.empty();
                            $.each(results.data.data, function(index, category) {
                                createBody(category,tbody);
                            });
                            $('#pagination').html(results.links);

                            $(document).on('click', '.pagination a', function(event){
                                event.preventDefault(); 
                                var page = $(this).attr('href').split('page=')[1];
                                fetch_data(page);
                            });
                        
                            function fetch_data(page){
                                $.ajax({
                                    url:"/search?page="+page,
                                    method: 'get',
                                    data: {search: search},
                                    success:function(results){
                                        var tbody = $('.category-body');
                                        tbody.empty();
                                        $.each(results.data.data, function(index, category) {
                                            createBody(category,tbody);
                                        });
                                        $('#pagination').html(results.links);
                                    }
                                });
                            };
                        }
                    });
                }, 1000);
            });
        });
        function formatDate(date) {
            return date.getFullYear() + '-' +
                ('0' + (date.getMonth()+1)).slice(-2) + '-' +
                ('0' + date.getDate()).slice(-2) + ' ' +
                ('0' + date.getHours()).slice(-2) + ':' +
                ('0' + date.getMinutes()).slice(-2) + ':' +
                ('0' + date.getSeconds()).slice(-2);
        }
        function createBody(category,tbody)
        {

            var createdAtDate = new Date(category.created_at);
            var created_at = formatDate(createdAtDate);

            var updatedAtDate = new Date(category.updated_at);
            var updated_at = formatDate(updatedAtDate);
            var tr = $('<tr>');
            tr.append('<td>' + category.id + '</td>');
            tr.append('<td>' + category.name + '</td>');
            tr.append('<td>' + category.slug + '</td>');
            tr.append('<td class="d-none d-md-table-cell">' + created_at + '</td>');
            tr.append('<td class="d-none d-md-table-cell">' + updated_at + '</td>');
            tr.append(`
                <td>
                    <ul class="action-list">
                        <li><a href="categories/${category.id}/edit " class="edit-category" data-tip="Sửa"><i class="fa fa-edit text-info"></i></a></li>
                        <li><a href="#" class="delete-category" data-id="` + category.id + `" data-tip="Xóa"><i class="fa fa-trash text-danger"></i></a></li>
                        <li class="d-md-none"><a href="categories/${category.id}" data-tip="Xem"><i class="fa-regular fa-eye text-success"></i></a></li>
                    </ul>
                </td>
            `);
            tbody.append(tr);
        }


    </script>
@endpush