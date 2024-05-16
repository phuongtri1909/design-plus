@extends('layouts.base')

@section('content')
    <div id="page-approve">
        <div class="container">
            <div class="operation mt-5 d-flex">
                <select class="form-select form-select-sm selected-approve selected-action" aria-label="Small select example" name="operation">
                    <option selected value="" disabled>Thao tác</option>
                    <option value="1">Không duyệt bài</option>
                    <option value="2">Duyệt bài, chuyển đi</option>
                </select>
                <button class="btn-operation btn-ok btn-sm">Ok</button>
            </div>
            <div class="filter mt-3 mb-5">
                <p class="ps-1 mb-1">Bộ lọc & phân loại</p>
                <div class="d-flex flex-column flex-md-row">
                
                        <select class="form-select form-select-sm selected-action selected-reporter" aria-label="Small select example" name="reporter">
                            <option selected value="" disabled>Phóng viên</option>
                            @foreach ($reporters as $reporter)
                                <option value="{{ $reporter->id }}">{{ $reporter->full_name }}</option>
                            @endforeach
                        </select>

                        <select class="form-select form-select-sm selected-action selected-status mt-1 mt-md-0" aria-label="Small select example" name="status">
                            <option selected value="" disabled>Tình trạng</option>
                            <option value="1">Bài đã duyệt</option>
                            <option value="0">Bài chưa duyệt</option>
                            <option value="2">Bài không đạt</option>
                        </select>

                        <div class="d-flex">
                            <select class="form-select form-select-sm selected-action selected-time" aria-label="Small select example" name="duration">
                                <option selected value="" disabled>Thời gian</option>
                                <option value="1">Trong tháng này</option>
                                <option value="3">3 tháng trước</option>
                                <option value="6">6 tháng trước</option>
                            </select>

                            <button class="btn-ok btn-classify btn-sm mt-1 mt-md-0 ">LỌC</button>
                        </div>
                </div>
            </div>

            <div class="table-approve">
                <table class="table">
                    <thead class="table-light">
                        <th></th>
                      <th>Danh sách bài</th>
                      <th></th>
                      <th>Tình trạng</th>
                      <th>Ngày</th>
                    </thead>
                    <tbody class="t-body-approve">
                        @foreach ($list_posts as $post)
                            <tr>
                                <td><input type="checkbox" name="posts[]" value="{{ $post->id }}"></td>
                                <td>
                                    @if($post->count_no_approval != null)
                                        ({{ $post->count_no_approval }}) 
                                        - 
                                    @endif
                                    
                                    {{ $post->title }} <br> <i>{{ $post->user->full_name }}</i></td>
                                <td><a href="{{ route('posts.show', ['slug'=> $post->slug]) }}">Xem bài</a></td>
                                <td>
                                    @if ($post->status_approval == 0)
                                        <span class="text-warning">Chưa duyệt</span>
                                    @elseif ($post->status_approval == 1)
                                        <span class="text-success">Đã duyệt</span>
                                    @else
                                        <span class="text-danger">Không đạt</span>
                                    @endif
                                </td>
                                @if ($post->approval_at != null)
                                    <td>{{$post->approval_at}}</td>
                                @else
                                    <td></td>
                                @endif
                                
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div id="pagination">
                    {{ $list_posts->links('vendor.pagination.custom') }}
                </div>
            </div>
        </div>

        <div class="modal fade" id="notificationModal" tabindex="-1" aria-labelledby="notificationModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title dp-color" id="notificationModalLabel">Thông báo</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Bạn có chắc chắn muốn thực hiện hành động này không?
                </div> 
              </div>
            </div>
        </div>

    </div>
@endsection
@push('scripts')
<script>
    $(document).ready(function() {
        $('.btn-operation').click(function(e) {
            e.preventDefault();

            var selectedOperation = $('.selected-approve').val();
            var selectedPosts = $('input[name="posts[]"]:checked').map(function() {
                return $(this).val();
            }).get();

            if (selectedOperation && selectedPosts.length > 0) {
                $.ajax({
                    url: 'approve_list',
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    data: JSON.stringify({
                        operation: selectedOperation,
                        posts: selectedPosts
                    }),
                    success: function(data) {
                        $('#notificationModal').modal('show');
                        $('#notificationModal .modal-body').text(data.message);

                    
                        let tbody = $('tbody.t-body-approve');
                        tbody.empty();
                       
                        $.each(data.data, function(index, item) {
                            createBody(item,tbody);
                        });
                        $('#pagination').html(data.links);

                        $(document).on('click', '.pagination a', function(event){
                            event.preventDefault(); 
                            var page = $(this).attr('href').split('page=')[1];
                            fetch_data(page);
                        });
                    
                        function fetch_data(page){
                            $.ajax({
                                url:"/approve_list?page="+page,
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                data: JSON.stringify({
                                    operation: selectedOperation,
                                    posts: selectedPosts
                                }),
                                success:function(data){
                                    let tbody = $('tbody.t-body-approve');
                                    tbody.empty();
                                    $.each(data.data, function(index, item) {
                                        createBody(item, tbody);
                                    });
                                    $('#pagination').html(data.links);
                                }
                            });
                        };
                      
                    },
                    error: function(error) {
                        $('#notificationModal .modal-body').text(error.responseJSON.error);
                    }
                });
            }
        });

        $('.btn-classify').click(function(e) {
            e.preventDefault();
            var selectedReporter = $('.selected-reporter').val();
            var selectedStatus = $('.selected-status').val();
            var selectedTime = $('.selected-time').val();
            if (selectedReporter || selectedStatus || selectedTime) {
                $.ajax({
                    url: 'approve_classify',
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    data: JSON.stringify({
                        selected_reporter: selectedReporter,
                        selected_status: selectedStatus,
                        selected_time: selectedTime
                    }),
                    success: function(data) {
                        let tbody = $('tbody.t-body-approve');
                        tbody.empty();
                       
                        $.each(data.data, function(index, item) {
                            createBody(item,tbody);
                        });
                        $('#pagination').html(data.links);

                        $(document).on('click', '.pagination a', function(event){
                            event.preventDefault(); 
                            var page = $(this).attr('href').split('page=')[1];
                            fetch_data(page);
                        });
                    
                        function fetch_data(page){
                            $.ajax({
                                url:"/approve_classify?page="+page,
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                data: JSON.stringify({
                                    selected_reporter: selectedReporter,
                                    selected_status: selectedStatus,
                                    selected_time: selectedTime
                                }),
                                success:function(data){
                                    let tbody = $('tbody.t-body-approve');
                                    tbody.empty();
                                    $.each(data.data, function(index, item) {
                                        createBody(item, tbody);
                                    });
                                    $('#pagination').html(data.links);
                                }
                            });
                        };
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
            let tr = $('<tr>');

            let tdCheckbox = $('<td>');
            let checkbox = $('<input>').attr('type', 'checkbox').attr('name', 'posts[]').val(post.id);
            tdCheckbox.append(checkbox);
            tr.append(tdCheckbox);

            let tdTitle = $('<td>').text(post.title);
            let iUser = $('<br><i>').text(post.user.full_name);
            tdTitle.append(iUser);
            tr.append(tdTitle);

            let tdViewPost = $('<td>');
            let aViewPost = $('<a>').attr('href', `/posts/${post.slug}`).text('Xem bài');
            tdViewPost.append(aViewPost);
            tr.append(tdViewPost);

            let tdStatus = $('<td>');
            if (post.status_approval == 0) {
                tdStatus.append($('<span>').addClass('text-warning').text('Chưa duyệt'));
            } else if (post.status_approval == 1) {
                tdStatus.append($('<span>').addClass('text-success').text('Đã duyệt'));
            } else {
                tdStatus.append($('<span>').addClass('text-danger').text('Không đạt'));
            }
            tr.append(tdStatus);

            let tdApprovalAt = $('<td>');
            if (post.status_approval == 1 || post.status_approval == 2) {
                tdApprovalAt.text(post.approval_at);
            }
            tr.append(tdApprovalAt);

            tbody.append(tr);
        }

    });
    </script>
@endpush