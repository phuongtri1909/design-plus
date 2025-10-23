@extends('admin.layouts.app')
<style>
    ol.gradient-list {
        counter-reset: gradient-counter;
        list-style: none;
        margin: 1.75rem 0;
        padding-left: 1rem;
    }

    ol.gradient-list>li {
        background: white;
        border-radius: 0 0.5rem 0.5rem 0.5rem;
        box-shadow: 0.25rem 0.25rem 0.6rem rgba(0, 0, 0, 0.05), 0 0.5rem 1.125rem rgba(75, 0, 0, 0.05);
        counter-increment: gradient-counter;
        margin-top: 1rem;
        min-height: 3rem;
        padding: 0rem 1rem 0rem 3rem;
        position: relative;
    }

    ol.gradient-list>li::before,
    ol.gradient-list>li::after {
        background: linear-gradient(135deg, #d6d6d6 0%, #626262 100%);
        border-radius: 1rem 1rem 0 1rem;
        content: '';
        height: 3rem;
        left: -1rem;
        overflow: hidden;
        position: absolute;
        top: -1rem;
        width: 3rem;
    }

    ol.gradient-list>li::before {
        align-items: flex-end;
        box-shadow: 0.25rem 0.25rem 0.6rem rgba(0, 0, 0, 0.05), 0 0.5rem 1.125rem rgba(75, 0, 0, 0.05);
        content: counter(gradient-counter);
        color: #1d1f20;
        display: flex;
        font: 900 1.5em/1 'Montserrat';
        justify-content: flex-end;
        padding: 0.125em 0.25em;
        z-index: 1;
    }

    ol.gradient-list>li+li {
        margin-top: 2rem;
    }

    /* Dynamic background colors for ::before based on nth-child */
    ol.gradient-list>li:nth-child(10n+1)::before,
    ol.gradient-list>li:nth-child(10n+2)::before,
    ol.gradient-list>li:nth-child(10n+3)::before,
    ol.gradient-list>li:nth-child(10n+4)::before,
    ol.gradient-list>li:nth-child(10n+5)::before {
        background: linear-gradient(135deg, rgba(162, 237, 86, 0.2) 0%, rgba(253, 220, 50, 0.2) 100%);
    }

    ol.gradient-list>li:nth-child(10n+6)::before,
    ol.gradient-list>li:nth-child(10n+7)::before,
    ol.gradient-list>li:nth-child(10n+8)::before,
    ol.gradient-list>li:nth-child(10n+9)::before,
    ol.gradient-list>li:nth-child(10n+10)::before {
        background: linear-gradient(135deg, rgba(73, 73, 73, 0.8) 0%, rgba(255, 245, 196, 0.8) 100%);
    }

    /* Status badge styling */
    .status-badge {
        margin-left: 10px;
    }

    .status-badge .badge {
        font-size: 0.75rem;
        padding: 0.375rem 0.75rem;
        border-radius: 0.375rem;
        font-weight: 500;
    }

    .gradient-list li {
        display: flex;
        flex-direction: column;
    }

    .gradient-list li a {
        text-decoration: none;
        color: inherit;
    }

    .gradient-list li a:hover {
        text-decoration: underline;
    }
</style>
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-offset-1 col-md-12">
                <div class="panel pb-2">
                    <div class="panel-heading">
                        <div>
                            <a href="{{ route('reporter.index') }}" class="dp-color"><i class="fa-solid fa-arrow-left "></i>
                                Trở lại</a>
                        </div>
                        <div class="card" style="border-radius:10px">
                            <div class="card-body text-center">
                                <h4 class="card-title m-b-0">
                                    {{ $report == 'up' ? 'Các bài đã up lên ' : ($report == 'approval' ? 'Các bài đã duyệt' : 'Các bài đã đăng') }}
                                    của phóng viên: {{ $reporter->full_name }}
                                </h4>
                            </div>
                            <div class="infomation mx-1 mx-md-5">
                                <main>
                                    <ol class="gradient-list">
                                        @foreach ($posts as $post)
                                            <li class="text-dark">
                                                <a class="text-dark link-custom"
                                                    href="{{ route('posts.show', $post->slug) }}">
                                                    <p class="mb-1">{{ $post->title }}</p>
                                                </a>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span>
                                                        @if ($report == 'up')
                                                            {{ $post->send_post_at }}
                                                        @elseif ($report == 'approval')
                                                            {{ $post->approval_at }}
                                                        @else
                                                            {{ $post->get_post_at }}
                                                        @endif
                                                    </span>
                                                    <div class="status-badge">
                                                        @if ($post->send_approval == 0 || $post->send_approval == 1 && $post->status_approval == 2)
                                                            <span class="badge bg-warning text-white">Chưa gửi duyệt</span>
                                                        @elseif($post->send_approval == 1 && $post->status_approval == 0)
                                                            <span class="badge bg-info text-white">Đang chờ duyệt</span>
                                                        @elseif($post->send_approval == 1 && $post->status_approval == 1 && $post->status_get_post == 0)
                                                            <span class="badge bg-primary text-white">Đang đăng bài</span>
                                                        @elseif($post->send_approval == 1 && $post->status_approval == 1 && $post->status_get_post == 1)
                                                            @if ($post->link != null)
                                                                <span class="badge bg-success text-white">Đã đăng</span>
                                                            @else
                                                                <span class="badge bg-success text-white">Đã đăng</span>
                                                            @endif
                                                        @elseif($post->send_approval == 1 && $post->status_approval == 2)
                                                            <span class="badge bg-danger text-white">Không đạt</span>
                                                        @else
                                                            <span class="badge bg-secondary text-white">Không xác định</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ol>
                                    <div id="pagination">
                                        {{ $posts->links('vendor.pagination.custom') }}
                                    </div>
                                </main>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
