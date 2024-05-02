@extends('layouts.base')

@section('content')
    <div id="detail-post">
        <div class="container mt-5">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @if ($errors->has('action'))
                            <li>{{ $errors->first('action') }}</li>
                        @endif
                        @if ($errors->has('post_slug'))
                            <li>{{ $errors->first('post_slug') }}</li>
                        @endif
                        @if ($errors->has('error'))
                            <li>{{ $errors->first('error') }}</li>
                        @endif
                    </ul>
                </div>
            @endif
            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
            <div class="d-flex flex-column flex-md-row align-items-start justify-content-md-between mb-4">
                <h5>{{ $post->title }}</h5>
                <div class="">
                    @if ($post->status_approval == 0 && auth()->user()->role == 1 || auth()->user()->role == 2 && $post->status_approval == 1 || auth()->user()->role == 1)
                        
                        <form action="{{ route('approve.handleApproveAction') }}" method="POST">
                            @csrf
                            <input type="hidden" name="post_slug" value="{{ $post->slug }}">

                            @if ($post->status_approval == 0 && auth()->user()->role == 1)
                                <button class="btn-sm btn-primary" name="action" value="approve">PHÊ DUYỆT</button>
                                <button class="btn-sm btn-not-achieved" name="action" value="notAchieved">KHÔNG ĐẠT</button>
                            @elseif (auth()->user()->role == 2 && $post->status_approval == 1 || auth()->user()->role == 1 && $post->status_approval == 1)
                                @if ($post->status_get_post == 0)
                                    <button class="btn-sm btn-success" name="action" value="post">ĐĂNG BÀI</button>
                                @else
                                    @if ($post->link != null)
                                        <a href="{{ $post->link}}" class="btn-sm btn-warning text-decoration-none ">ĐÃ ĐĂNG BÀI</a>
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

        </div>

        <div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="confirmModalLabel">Thông báo</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Bạn có chắc chắn muốn thực hiện hành động này không?
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                  <button type="button" class="btn btn-primary" id="confirmAction">Thực hiện</button>
                </div>
              </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        $(document).ready(function() {
            $('form button').on('click', function(e) {
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
    </script>
@endpush