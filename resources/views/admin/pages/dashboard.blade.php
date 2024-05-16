@extends('admin.layouts.app')

@section('content')
    <div class="container-fluid pt-4 px-4">
        <div class="row g-4">
            <div class="col-sm-6 col-xl-4 ">
                <div class="bg-light rounded d-flex align-items-center justify-content-between p-4">
                    <i class="fa-solid fa-users fa-2xl dp-color"></i>
                    <div class="ms-3 text-dark">
                        <p class="mb-2">Tổng phóng viên</p>
                        <h6 class="mb-0">{{ $count_reporter }}</h6>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-4 mt-3 mt-sm-0">
                <div class="bg-light rounded d-flex align-items-center justify-content-between p-4">
                    <i class="fa-solid fa-user-group fa-2xl dp-color"></i>
                    <div class="ms-3 text-dark">
                        <p class="mb-2">Tổng Người lấy bài</p>
                        <h6 class="mb-0">{{ $count_get_post }}</h6>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-4 mt-3 mt-sm-0">
                <div class="bg-light rounded d-flex align-items-center justify-content-between p-4">
                    <i class="fa-solid fa-file-contract fa-2xl dp-color"></i>
                    <div class="ms-3 text-dark">
                        <p class="mb-2">Tổng bài</p>
                        <h6 class="mb-0">{{ $count_post }}</h6>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-12 mt-4">
            <div class="bg-light rounded h-100 p-4">
                <h6 class="mb-4">Thông tin bài viết</h6>
                <canvas id="bar-chart"></canvas>
            </div>
        </div>
    </div>
@endsection
@push('script-admin')
    <script>
        $(document).ready(function(){
            var ctx = document.getElementById('bar-chart').getContext('2d');
            var chart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Bài viết chưa duyệt', 'Bài viết đã duyệt', 'Bài viết đã đăng'],
                    datasets: [{
                        label: 'Tổng',
                        data: [@json($count_post_no_approval), @json($count_post_approval), @json($count_post_push)],
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.2)',
                            'rgba(54, 162, 235, 0.2)',
                            'rgba(255, 206, 86, 0.2)',                   
                        ],
                        borderColor: [
                            'rgba(255, 99, 132, 1)',
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 206, 86, 1)',
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        });
    </script>
@endpush