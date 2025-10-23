@extends('layouts.base')

@section('content')
    <div id="detail-post">
        <div class="container mt-5">

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

            <div class="d-flex justify-content-between mb-5">
                <div class="d-flex align-items-center">
                    <p class="mb-0 fw-bold fs-4 me-3">Thể loại: 
                        @if(auth()->user()->role == 3)
                            <span id="category-display" class="text-primary" style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#categoryModal">
                                {{ $post->category->name }} <i class="fa-solid fa-edit ms-1"></i>
                            </span>
                        @else
                            {{ $post->category->name }}
                        @endif
                    </p>
                </div>
                <p class="mb-0">Loại tin: {{ $post->post_type }}</p>
            </div>
            <div class="row mb-4">
                <h5 class="col-12 col-md-9">{{ $post->title }}</h5>
                <div class="col-12 col-md-3 px-0" style="text-align: right;
                padding-right: 13px !important;">
                    @if ($post->status_save_draft == '0' && $post->send_approval == '1')
                        <form action="{{ route('approve.handleApproveAction') }}" method="POST">
                            @csrf
                            <input type="hidden" name="post_slug" value="{{ $post->slug }}">

                            @if ($post->status_approval == 0 )
                                @if(auth()->user()->role == 1 || auth()->user()->role == 3)
                                    <button  class="btn-sm btn-primary btn-action" name="action" value="approve">PHÊ DUYỆT</button>
                                    <button class="btn-sm btn-not-achieved btn-action" name="action" value="notAchieved">KHÔNG ĐẠT</button>
                                @endif
                            @endif

                            @if ( $post->status_approval == 1 )
                                @if(auth()->user()->role == 2)
                                    @if ($post->status_get_post == 0)
                                        <a class="btn-sm btn-success text-decoration-none insert-link " data-bs-toggle="modal" data-bs-target="#linkModal" data-post-id="{{ $post->id }}">ĐĂNG BÀI</a>
                                    @else
                                        @if ($post->link != null)
                                            <a href="{{ $post->link}}" target="_blank" class="btn-sm btn-warning text-decoration-none ">ĐÃ ĐĂNG BÀI</a>
                                        @else
                                            <span class="btn-sm btn-warning">ĐÃ ĐĂNG BÀI</span>
                                        @endif
                                    @endif
                                @elseif (auth()->user()->role != 2)
                                    @if ($post->status_get_post == 0)
                                            <span class="btn-sm btn-warning d-flex">BÀI CHƯA ĐĂNG</span>
                                    @else
                                        @if ($post->link != null)
                                            <a href="{{ $post->link}}" target="_blank" class="btn-sm btn-warning text-decoration-none ">ĐÃ ĐĂNG BÀI</a>
                                        @else
                                            <span class="btn-sm btn-warning">ĐÃ ĐĂNG BÀI</span>
                                        @endif
                                            
                                    @endif
                                @endif
                            @endif

                            @if ($post->status_approval == 2)
                                <span class="btn-sm btn-danger">BÀI VIẾT KHÔNG ĐẠT</span>
                            @endif
                        </form>
                    @endif
                </div>
            </div>

            <p class="fw-bold fst-italic mb-4">{{ $post->brief_intro }}</p>
            {!! nl2br(e($post->content)) !!}

            <div id="gallery" class="row" style="border-radius: 12px; background: #ece7e7;">
                @foreach($PostImages as $image)
                    <div class="col-md-4 col-sm-6 mb-4 gallery-item position-relative">
                        <img src="{{ Storage::url($image->image) }}" class="img-fluid" alt="Image">
                    </div>
                @endforeach
                <button id="downloadAll" class="btn btn-secondary" data-post-id="{{ $post->id }}">Download All Images</button>
            </div>

            <div class="d-flex justify-content-between mb-5">
                <p class="mb-0">Tác giả: {{ $post->user->full_name }}</p>
                <p class="mb-0">Ngày đăng: {{ $post->created_at }}</p>
            </div>
        </div>

        <div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title dp-color" id="confirmModalLabel">Thông báo</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Bạn có chắc chắn muốn thực hiện hành động này không?
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-light" data-bs-dismiss="modal">Đóng</button>
                  <button type="button" class="btn btn-dp" id="confirmAction">Thực hiện</button>
                </div>
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
                    <form action="{{ route('push.post') }}" method="POST" id="link-form" class="text-center">
                        @csrf
                        <input type="hidden" name="post_id">
                        <div class="mb-3">
                            <input type="text" class="form-control" id="link" name="link" placeholder="Link">
                        </div>
                        <button type="submit" class="btn-sm btn-save-link">Đăng bài</button>
                    </form>
                </div>
              </div>
            </div>
        </div>

        <!-- Category Edit Modal -->
        <div class="modal fade" id="categoryModal" tabindex="-1" aria-labelledby="categoryModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title dp-color" id="categoryModalLabel">Chỉnh sửa thể loại</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="category-form">
                        @csrf
                        <input type="hidden" name="post_slug" value="{{ $post->slug }}">
                        <div class="mb-3">
                            <label for="category-select" class="form-label">Chọn thể loại mới:</label>
                            <select class="form-select" id="category-select" name="category_id" required>
                                <option value="">-- Chọn thể loại --</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" 
                                        {{ $post->category_id == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="alert alert-info">
                            <i class="fa-solid fa-info-circle"></i>
                            <strong>Lưu ý:</strong> Thay đổi thể loại sẽ ảnh hưởng đến phân loại bài viết.
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                  <button type="button" class="btn btn-primary" id="save-category">Lưu thay đổi</button>
                </div>
              </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        $(document).ready(function() {
            $('#downloadAll').click(function() {
                var postId = $(this).data('post-id');
                $('#gallery .gallery-item img').each(function(index) {
                    var link = document.createElement('a');
                    link.href = $(this).attr('src');
                    link.download = postId + '_image' + (index + 1) + '.jpg';
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                });
            });
        });

        $('body').on('click', '.insert-link', function() {
            var postId = $(this).data('post-id');
            $('#link-form').find('input[name="post_id"]').val(postId);
        });

        $(document).ready(function() {
            $('form .btn-action').on('click', function(e) {
                e.preventDefault();

                var form = $(this).closest('form');
                var action = $(this).val();

                $('#confirmModal').modal('show');

                $('#confirmAction').on('click', function() {
                    form.append('<input type="hidden" name="action" value="' + action + '" />');
                    form.submit();
                });
            });
        });

        $(document).ready(function() {
            @if ($errors->any())
                var errorMessages = '';
                @if ($errors->has('action'))
                    errorMessages += '<p>{{ $errors->first('action') }}</p>';
                @endif
                @if ($errors->has('post_slug'))
                    errorMessages += '<p>{{ $errors->first('post_slug') }}</p>';
                @endif
                @if ($errors->has('error'))
                    errorMessages += '<p>{{ $errors->first('error') }}</p>';
                @endif
                $('#messageContent').html(errorMessages);
                $('#messageModal').modal('show');
            @endif
            
            // Category change functionality
            $('#save-category').click(function() {
                var selectedCategoryId = $('#category-select').val();
                var selectedCategoryName = $('#category-select option:selected').text();
                var currentCategoryName = '{{ $post->category->name }}';
                
                if (!selectedCategoryId) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Chưa chọn thể loại!',
                        text: 'Vui lòng chọn một thể loại mới.',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#3085d6'
                    });
                    return;
                }
                
                if (selectedCategoryId == '{{ $post->category_id }}') {
                    Swal.fire({
                        icon: 'info',
                        title: 'Không có thay đổi!',
                        text: 'Thể loại hiện tại đã được chọn.',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#3085d6'
                    });
                    return;
                }
                
                // Show confirmation with SweetAlert2
                Swal.fire({
                    title: 'Xác nhận thay đổi thể loại',
                    html: `
                        <div class="text-start">
                            <p><strong>Thể loại hiện tại:</strong> <span class="text-primary">${currentCategoryName}</span></p>
                            <p><strong>Thể loại mới:</strong> <span class="text-success">${selectedCategoryName}</span></p>
                            <hr>
                            <p class="text-muted">Bạn có chắc chắn muốn thay đổi thể loại này không?</p>
                        </div>
                    `,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Xác nhận thay đổi',
                    cancelButtonText: 'Hủy bỏ',
                    width: '500px'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Show loading
                        Swal.fire({
                            title: 'Đang xử lý...',
                            text: 'Vui lòng chờ trong giây lát',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                        
                        // Submit form
                        $.ajax({
                            url: '{{ route("posts.updateCategory") }}',
                            method: 'POST',
                            data: {
                                _token: $('meta[name="csrf-token"]').attr('content'),
                                post_slug: '{{ $post->slug }}',
                                category_id: selectedCategoryId
                            },
                            success: function(response) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Thành công!',
                                    text: 'Thể loại đã được cập nhật thành công.',
                                    confirmButtonText: 'OK',
                                    confirmButtonColor: '#28a745'
                                }).then(() => {
                                    // Update display
                                    $('#category-display').html(selectedCategoryName + ' <i class="fa-solid fa-edit ms-1"></i>');
                                    // Close modal
                                    $('#categoryModal').modal('hide');
                                    // Reload page to reflect changes
                                    location.reload();
                                });
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Lỗi!',
                                    text: 'Có lỗi xảy ra khi cập nhật thể loại. Vui lòng thử lại.',
                                    confirmButtonText: 'OK',
                                    confirmButtonColor: '#d33'
                                });
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush