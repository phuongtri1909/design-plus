@extends('layouts.base')

@section('content')
<div>
    <div id="reporter">
        <div class="reporter">
            <div class="d-flex menu mb-1">
                <div>
                    <button id="createPostBtn" class="btn-menu me-2">Tạo bài viết mới</button>
                </div>
                <div>
                    <button id="viewAllBtn" class="btn-menu">Xem tất cả bài</button>
                </div>
            </div> 
            
            <div id="listReporter" class="list-reporter">
                <div class="classify row justify-content-end mb-4 mx-0">
                    <select class="form-select text-center col-6 me-2 mt-2 " style="width:150px" name="classify" id="classify">
                        <option value="" selected>Phân loại</option>
                        <option value="0">Các bài chưa chuyển</option>
                        <option value="1">Các bài đã chuyển</option>
                        <option value="2">Các bài đã đăng</option>
                        <option value="3">Tất cả</option>
                    </select>

                    <select class="form-select text-center col-6 mt-2" style="width:150px" name="status" id="status">
                        <option value="" selected>Tình trạng</option>
                        <option value="1">Bài đã duyệt</option>
                        <option value="0">Bài chưa duyệt</option>
                        <option value="2">Bài không đạt</option>
                    </select>

                    <select class="form-select text-center col-12 mt-2 ms-2" style="width:150px" name="duration" id="duration">
                        <option value="" selected>Thời gian</option>
                        <option value="1">Trong tháng này</option>
                        <option value="3">3 tháng trước</option>
                        <option value="6">6 tháng trước</option>
                    </select>
                </div>

                <div class="modal fade" id="messageModal" tabindex="-1" aria-labelledby="messageModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                      <div class="modal-content">
                        <div class="modal-header">
                          <h5 class="modal-title dp-color" id="messageModalLabel">Thông báo</h5>
                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body" id="messageContent">
                            <!-- Message will be inserted here -->
                        </div>
                      </div>
                    </div>
                </div>

                <div class="table-posts">
                    <table class="table-list-posts table table-striped">
                        <tbody>
                        @php
                            $index = 0 
                        @endphp
                            @foreach ($list_posts as $post)
                                @php
                                    $index ++;
                                @endphp
                                <tr class="tr-posts">
                                    <th scope="row">{{ $index }}</th>
                                    <td class="title">{{ $post->title }}</td>
                                    <td >
                                        @if ($post->send_approval == 0 || $post->send_approval == 1 && $post->status_approval == 2 )
                                            <a href="{{ route('posts.edit', ['slug'=> $post->slug]) }}">Xem & edit</a>
                                        @elseif($post->send_approval == 1 && $post->status_approval == 1 && $post->status_get_post == 1 || $post->status_approval == 1 && $post->status_get_post == 0 )
                                        
                                        @else
                                            <a href="{{ route('posts.show', ['slug'=> $post->slug]) }}">Xem</a>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($post->send_approval == 0 || $post->send_approval == 1 && $post->status_approval == 2)
                                            <a class="delete-post" data-id="{{ $post->id }}" data-action="delete" data-bs-toggle="modal" data-bs-target="#Modal-post">Xóa</a>
                                        @elseif($post->send_approval == 1 && $post->status_approval == 0 && $post->status_get_post == 0)
                                            <a class="recall-post" data-id="{{ $post->id }}" data-action="recall" data-bs-toggle="modal" data-bs-target="#Modal-post">Thu hồi</a>
                                        @elseif($post->send_approval == 1 && $post->status_approval == 1 && $post->status_get_post == 0)
                                            <a href="{{ route('posts.show', ['slug'=> $post->slug]) }}">Đang đăng bài</a>
                                        @else
                                            @if ($post->link != null)
                                                <a href="{{ $post->link }}">Đã đăng</a>
                                            @else
                                                <a class="no-href">Đã đăng</a>
                                            @endif
                                            
                                        @endif
                                    </td>
                                    <td> 
                                        @if ($post->send_approval == 1 && $post->status_approval == 0)
                                            <a class="no-href">Đang chờ duyệt</a>
                                        @elseif($post->send_approval == 1 && $post->status_approval == 2)
                                            <a class="resend-post" data-id="{{ $post->id }}" data-action="resend" data-bs-toggle="modal" data-bs-target="#Modal-post">Chuyển duyệt lại</a>
                                        @elseif($post->send_approval == 0 && $post->status_approval == 0  && $post->status_get_post == 0)
                                            <a class="send-post" data-id="{{ $post->id }}" data-action="send" data-bs-toggle="modal" data-bs-target="#Modal-post">Chuyển duyệt</a>
                                        @else
                                            
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                  
                    <div id="pagination">
                        {{ $list_posts->links('vendor.pagination.custom') }}
                    </div>
                </div>
            </div>
        </div>
        
        <div class="modal fade" id="Modal-post" tabindex="-1" aria-labelledby="Modal-postLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title dp-color" id="Modal-postLabel">Modal title</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Bạn có chắc chắn muốn xóa bài viết này không?
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-dp btn-apply">Đồng ý</button>
                </div>
              </div>
            </div>
        </div>

    </div>
</div>
@endsection
@push('scripts')
    <script>
        const formReporter = $('#formReporter');
        const listReporter = $('#listReporter');
        const reporterMenu = $('.reporter .menu');
        const tableListPosts = $('.table-list-posts');
        const tablePosts = $('.table-posts')
        const modalPost = $('#Modal-post');
        const modalPostBody = modalPost.find('.modal-body');
        const modalPostTitle = modalPost.find('.modal-title');
        const modalPostOkBtn = modalPost.find('.btn-apply');
        const reporter = $('#reporter');
        const csrfToken = $('meta[name="csrf-token"]').attr('content');

        function createTableRow(post, index, table) {
            var tr = $('<tr>').addClass('tr-posts');
            tr.append($('<th>').attr('scope', 'row').text(index));
            tr.append($('<td>').addClass('title').text(post.title));

            var tdViewEdit = $('<td>');
            if (post.send_approval == 0 || post.send_approval == 1 && post.status_approval == 2) {
                tdViewEdit.append($('<a>').attr('href', '/posts/' + post.slug + '/edit').text('Xem & edit'));
            } else if (post.send_approval == 1 && post.status_approval == 1 && post.status_get_post == 1 || post.status_approval == 1 && post.status_get_post == 0) {
                // Do nothing
            } else {
                tdViewEdit.append($('<a>').attr('href', '/posts/' + post.slug).text('Xem'));
            }
            tr.append(tdViewEdit);

            var tdDeleteRecall = $('<td>');
            if (post.send_approval == 0 || post.send_approval == 1 && post.status_approval == 2) {
                tdDeleteRecall.append($('<a>').addClass('delete-post').attr('data-id', post.id).attr('data-action', 'delete').attr('data-bs-toggle', 'modal').attr('data-bs-target', '#Modal-post').text('Xóa'));
            } else if (post.send_approval == 1 && post.status_approval == 0 && post.status_get_post == 0) {
                tdDeleteRecall.append($('<a>').addClass('recall-post').attr('data-id', post.id).attr('data-action', 'recall').attr('data-bs-toggle', 'modal').attr('data-bs-target', '#Modal-post').text('Thu hồi'));
            } else if (post.send_approval == 1 && post.status_approval == 1 && post.status_get_post == 0) {
                tdDeleteRecall.append($('<a>').attr('href', '/posts/' + post.slug).text('Đang đăng bài'));
            } else {
                if(post.link != null){
                    tdDeleteRecall.append($('<a>').attr('href', post.link).text('Đã đăng'));
                }
                else{
                    tdDeleteRecall.append($('<a>').attr('class', 'no-href').text('Đã đăng'));
                }
              
            }
            tr.append(tdDeleteRecall);

            var tdApproval = $('<td>');
            if (post.send_approval == 1 && post.status_approval == 0) {
                tdApproval.append($('<a>').addClass('no-href').text('Đang chờ duyệt'));
            } else if (post.send_approval == 1 && post.status_approval == 2) {
                tdApproval.append($('<a>').addClass('resend-post').attr('data-id', post.id).attr('data-action', 'resend').attr('data-bs-toggle', 'modal').attr('data-bs-target', '#Modal-post').text('Chuyển duyệt lại'));
            } else if (post.send_approval == 0 && post.status_approval == 0  && post.status_get_post == 0) {
                tdApproval.append($('<a>').addClass('send-post').attr('data-id', post.id).attr('data-action', 'send').attr('data-bs-toggle', 'modal').attr('data-bs-target', '#Modal-post').text('Chuyển duyệt'));
            }
            tr.append(tdApproval);

            table.append(tr);
        }

        function handleClassifyChange() {
            var selectedClassify = $('#classify').val();
            var selectedStatus = $('#status').val();
            var selectedDuration = $('#duration').val();
           
            $.ajax({
                url: '/classify',
                type: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: JSON.stringify({
                    classify: selectedClassify,
                    status: selectedStatus,
                    duration: selectedDuration
                }),
                success: function(data) {
                    tableListPosts.find('tbody').empty();
                    $('#pagination').empty();
                    $.each(data.data, function(index, item) {
                        createTableRow(item, index + 1, tableListPosts);
                    });
                    $('#pagination').html(data.links);
                    $(document).on('click', '.pagination a', function(event){
                        event.preventDefault(); 
                        var page = $(this).attr('href').split('page=')[1];
                        
                        fetch_data(page,selectedClassify,selectedStatus,selectedDuration);
                    });
                   
                    function fetch_data(page,selectedClassify,selectedStatus,selectedDuration){
                        $.ajax({
                            url:"/classify?page="+page,
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            data: JSON.stringify({
                                classify: selectedClassify,
                                status: selectedStatus,
                                duration: selectedDuration
                            }),
                            success:function(data){
                                tableListPosts.find('tbody').empty();
                                $('#pagination').empty();
                                $.each(data.data, function(index, item) {
                                    createTableRow(item, index + 1, tableListPosts);
                                });
                                $('#pagination').html(data.links);
                            }
                        });
                    }
                },
                error: function(error) {
                    console.log(error);
                }
            });
        }

        function handlePostActionClick(e) {
            e.preventDefault();
            var postId = $(this).data('id');
            var action = $(this).data('action');
            var message = action === 'delete' ? 'Bạn có chắc chắn muốn xóa bài viết này không?' : 
                        action === 'recall' ? 'Bạn có chắc chắn muốn thu hồi bài viết này không?' : 
                        action === 'resend' ? 'Bạn có chắc chắn muốn chuyển duyệt lại bài viết này không?' :
                        'Bạn có chắc chắn muốn chuyển duyệt bài viết này không?';
            var title = action === 'delete' ? 'Xóa bài viết' : 
                        action === 'recall' ? 'Thu hồi bài viết' : 
                        action === 'resend' ? 'Chuyển duyệt lại bài viết' :
                        'Chuyển duyệt bài viết';
            modalPost.data('post-id', postId).data('action', action);
            modalPostBody.text(message);
            modalPostTitle.text(title);
        }

        function handleModalPrimaryBtnClick() {
            var postId = modalPost.data('post-id');
            var action = modalPost.data('action');
            var method = action === 'delete' ? 'DELETE' : 'PATCH';

            var form = $('<form></form>');
            form.attr('method', 'POST');
            form.attr('action', action === 'delete' ? '/posts/' + postId : 
                                action === 'recall' ? '/recall/' + postId : 
                                action === 'resend' ? '/resend/' + postId :
                                '/send/' + postId);
            var csrfInput = $('<input>').attr('type', 'hidden').attr('name', '_token').val(csrfToken);
            form.append(csrfInput);
            var methodInput = $('<input>').attr('type', 'hidden').attr('name', '_method').val(method);
            form.append(methodInput);
            reporter.append(form);
            form.submit();
        }

        $(document).ready(function() {
            $('#createPostBtn').click(function() {
                window.location.href = "{{ route('home') }}";
            });

            $('#viewAllBtn').click(function() {
                window.location.href = "{{ route('posts.allPosts') }}";
            });

            $('#classify, #status, #duration').change(handleClassifyChange);

            $(document).on('click', '.delete-post, .recall-post, .send-post, .resend-post', handlePostActionClick);

            modalPostOkBtn.click(handleModalPrimaryBtnClick);
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