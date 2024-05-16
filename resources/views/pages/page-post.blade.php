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

            <div class="row mb-4">
                <h5 class="col-12 col-md-9">{{ $post->title }}</h5>
                <div class="col-12 col-md-3">
                    @if ($post->status_approval == 0 && auth()->user()->role == 1 || auth()->user()->role == 2 && $post->status_approval == 1 || auth()->user()->role == 1)
                        <form action="{{ route('approve.handleApproveAction') }}" method="POST">
                            @csrf
                            <input type="hidden" name="post_slug" value="{{ $post->slug }}">

                            @if ($post->status_approval == 0 && auth()->user()->role == 1)
                                <button  class="btn-sm btn-primary btn-action" name="action" value="approve">PHÊ DUYỆT</button>
                                <button class="btn-sm btn-not-achieved btn-action" name="action" value="notAchieved">KHÔNG ĐẠT</button>
                            @elseif (auth()->user()->role == 2 && $post->status_approval == 1 )
                                @if ($post->status_get_post == 0)
                                    <a class="btn-sm btn-success text-decoration-none insert-link " data-bs-toggle="modal" data-bs-target="#linkModal" data-post-id="{{ $post->id }}">ĐĂNG BÀI</a>
                                @else
                                    @if ($post->link != null)
                                        <a href="{{ $post->link}}" target="_blank" class="btn-sm btn-warning text-decoration-none ">ĐÃ ĐĂNG BÀI</a>
                                    @else
                                        <span class="btn-sm btn-warning">ĐÃ ĐĂNG BÀI</span>
                                    @endif
                                    
                                @endif
                            @elseif (auth()->user()->role != 2 && $post->status_approval == 1 )
                            
                                @if ($post->status_get_post == 0)
                                    <span class="btn-sm btn-warning d-flex">BÀI CHƯA ĐĂNG</span>
                                @else
                                    @if ($post->link != null)
                                        <a href="{{ $post->link}}" target="_blank" class="btn-sm btn-warning text-decoration-none ">ĐÃ ĐĂNG BÀI</a>
                                    @else
                                        <span class="btn-sm btn-warning">ĐÃ ĐĂNG BÀI</span>
                                    @endif
                                    
                                @endif
                            
                            @elseif ($post->status_approval == 2)
                                <span class="btn-sm btn-danger">BÀI VIẾT KHÔNG ĐẠT</span>
                            @endif
                        </form>
                    @endif
                </div>
            </div>
            <p class="fw-bold fst-italic mb-4">{{ $post->brief_intro }}</p>
            <p class="mb-5">{{ $post->content }}</p>
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
    </div>
@endsection
@push('scripts')
    <script>
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
            @if (session('success'))
                $('#messageContent').text('{{ session('success') }}');
                $('#messageModal').modal('show');
            @elseif (session('error'))
                $('#messageContent').text('{{ session('error') }}');
                $('#messageModal').modal('show');
            @endif
        });
    </script>
@endpush