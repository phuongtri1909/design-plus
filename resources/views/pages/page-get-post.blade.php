@extends('layouts.base')

@section('content')
    <div id="page-approve">
        <div class="container">
            <div class="filter mt-5 mb-5">
                <p class="ps-1 mb-1">Bộ lọc & phân loại</p>
                <div class="d-sm-flex">
                    <select class="mb-2 mb-sm-0 form-select form-select-sm selected-action selected-reporter" aria-label="Small select example" name="reporter">
                        <option selected value="" disabled>Tác giả</option>
                        @foreach ($reporters as $reporter)
                            <option value="{{ $reporter->id }}">{{ $reporter->full_name }}</option>
                        @endforeach
                    </select>
                    <select class="mb-2 mb-sm-0 form-select form-select-sm selected-action selected-time" aria-label="Small select example" name="duration">
                        <option selected value="" disabled>Thời gian</option>
                        <option value="1">Trong tháng này</option>
                        <option value="3">3 tháng trước</option>
                        <option value="6">6 tháng trước</option>
                    </select>
                    <button class="btn-ok btn-classify btn-sm">LỌC</button>
                </div>
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
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="table-approve table-responsive">
                <table class="table">
                    <thead class="table-light">
                      <th>Danh sách bài</th>
                      <th>Tác giả</th>
                      <th>Tình trạng</th>
                      <th>Ngày</th>
                      <th></th>
                      <th>Thể loạ<i></i></th>
                      
                    </thead>
                    <tbody class="t-body-get-post">
                        @foreach ($list_posts as $post)
                            <tr>
                                <td> <a href="{{ route('posts.show', ['slug'=> $post->slug]) }}">{{ $post->title }}</a></td>
                                <td><a class="no-href">{{ $post->user->full_name }}</a></td>
                                <td>
                                    @if ($post->status_get_post == 0)
                                        <span class="text-warning">Chưa đăng</span>
                                    @elseif ($post->status_get_post == 1)
                                        <span class="text-success">Đã đăng</span>
                                    @endif
                                </td>
                                @if ($post->status_approval == 1 && $post->status_get_post == 1)
                                    <td>{{$post->get_post_at}}</td>
                                @else
                                    <td></td>
                                @endif
                                <td>
                                    @if ($post->status_approval == 1 && $post->status_get_post == 1 && $post->link != null)
                                        <a href="{{ $post->link }}" target="_blank"  class="">Link bài</a><span style="color: #f26bac">|</span><a class="no-href edit-link" data-bs-toggle="modal" data-bs-target="#linkModal" data-post-id="{{ $post->id }}" data-post-link="{{ $post->link }}">Edit</a>
                                    @endif
                                </td>
                                <td>{{ $post->category->name }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div id="pagination">
                    {{ $list_posts->links('vendor.pagination.custom') }}
                </div>
            </div>
        </div>
        <div class="modal fade" id="linkModal" tabindex="-1" aria-labelledby="linkModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
              <div class="modal-content" style="background-color: #cccccc ">
                <div class="modal-header border-bottom-0">
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('save.link') }}" method="POST" id="web-link-form" class="text-center">
                        @csrf
                        <input type="hidden" name="post_id">
                        <div class="mb-3">
                            <input type="text" class="form-control" id="web-link" name="web_link" placeholder="Link">
                        </div>
                        <button type="submit" class="btn-sm btn-save-link">Lưu weblink</button>
                    </form>
                </div>
              </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
<script>
    $(document).ready(function() {

        $('.btn-classify').click(function(e) {
            e.preventDefault();
            var selectedReporter = $('.selected-reporter').val();
            var selectedTime = $('.selected-time').val();
            if (selectedReporter || selectedTime) {
                $.ajax({
                    url: 'getPost_classify',
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    data: JSON.stringify({
                        selected_reporter: selectedReporter,
                        selected_time: selectedTime
                    }),
                    success: function(data) {
                        let tbody = $('tbody.t-body-get-post');
                        tbody.empty();
                       
                        
                        $.each(data.data, function(index, post) {
                            createBody(post, tbody);
                        });
                        $('#pagination').html(data.links);

                        $(document).on('click', '.pagination a', function(event){
                            event.preventDefault(); 
                            var page = $(this).attr('href').split('page=')[1];
                            fetch_data(page);
                        });
                    
                        function fetch_data(page){
                            $.ajax({
                                url:"/getPost_classify?page="+page,
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                data: JSON.stringify({
                                    selected_reporter: selectedReporter,
                                    selected_time: selectedTime
                                }),
                                success:function(data){
                                    let tbody = $('tbody.t-body-get-post');
                                    tbody.empty();
                                   
                                    
                                    $.each(data.data, function(index, post) {
                                        createBody(post, tbody);
                                    });
                                    $('#pagination').html(data.links);
                                }
                            });
                        }

                    },
                    error: function(error) {
                        $('.toast-body').removeClass('toast-success toast-error toast-warning')
                            .addClass(`toast-error`)
                            .text(error.responseJSON.error);
                        $('.toast').toast('show');
                    }
                });
            }
        });

        function createBody(post,tbody) {
            let status = '';
            if (post.status_get_post == 0) {
                status = '<span class="text-warning">Chưa đăng</span>';
            } else if (post.status_get_post == 1) {
                status = '<span class="text-success">Đã đăng</span>';
            }

            let get_post_at = '';
            if (post.status_approval == 1 && post.status_get_post == 1) {
                get_post_at = post.get_post_at;
            }

            let link = '';
            if (post.status_approval == 1 && post.status_get_post == 1 && post.link != null) {
                link = `<a href="${post.link}" target="_blank" class="">Link bài</a><span style="color: #f26bac">|</span><a class="no-href edit-link" data-bs-toggle="modal" data-bs-target="#linkModal" data-post-id="${post.id}" data-post-link="${post.link}">Edit</a>`;
            }

            let row = `
            <tr>
                <td><a href="/posts/${post.slug}">${post.title}</a></td>
                <td><a class="no-href">${post.user.full_name}</a></td>
                <td>${status}</td>
                <td>${get_post_at}</td>
                <td>${link}</td>
                </tr>
             `;

            tbody.append(row);
        }

        $(document).ready(function() {

            $('body').on('click', '.edit-link', function() {
                var postId = $(this).data('post-id');
                var postLink = $(this).data('post-link');
                $('#web-link-form').find('input[name="post_id"]').val(postId);
                $('#web-link-form').find('input[name="web_link"]').val(postLink);
            });
        });
    });
    </script>
@endpush