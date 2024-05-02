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
                <div class="classify d-flex justify-content-end mb-4">
                    <select class="form-select text-center" style="width:150px" name="classify" id="classify">
                        <option value="" selected disabled>Phân loại</option>
                        <option value="0">Các bài chưa chuyển</option>
                        <option value="1">Các bài đã chuyển</option>
                        <option value="2">Các bài đã đăng</option>
                        <option value="3">Tất cả</option>
                    </select>
                </div>
                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif
                
                @if (session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif
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

        <div class="toast align-items-center position-fixed end-0" style="top: 17%" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body toast-success">
                    Hello, world! This is a toast message.
                </div>
                <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
        
        <div class="modal fade" id="Modal-post" tabindex="-1" aria-labelledby="Modal-postLabel" aria-hidden="true">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="Modal-postLabel">Modal title</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Bạn có chắc chắn muốn xóa bài viết này không?
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-primary">Đồng ý</button>
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
        const modalPostPrimaryBtn = modalPost.find('.btn-primary');
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
            var selectedValue = $(this).val();

            $.ajax({
                url: '/classify/' + selectedValue,
                type: 'GET',
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
                        var classifyValue = $('#classify').val();
                        fetch_data(page,classifyValue);
                    });
                   
                    function fetch_data(page,classifyValue){
                        $.ajax({
                            url:"/classify/"+classifyValue+"?page="+page,
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

            $('#classify').change(handleClassifyChange);

            $(document).on('click', '.delete-post, .recall-post, .send-post, .resend-post', handlePostActionClick);

            modalPostPrimaryBtn.click(handleModalPrimaryBtnClick);
        });
    </script>
@endpush