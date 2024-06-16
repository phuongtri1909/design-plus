<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Dashboard Design plus</title>
    <link rel="icon" href="{{ asset('images/logoCircle.ico') }}" type="image/x-icon">
    
    <link rel="stylesheet" href="{{ asset('fontawesome-free-6.5.1-web/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/table.css') }}">
</head>
<body>
    <div id="app">
        <div class="container-xxl position-relative bg-white d-flex p-0">
        
            <!-- Sidebar Start -->
            <div class="sidebar pe-4 pb-3">
                <nav class="navbar bg-light navbar-light">
                    <a href="{{ route('dashboard.index') }}" class="navbar-brand me-4 mb-3">
                        <img src="{{ asset('images/logo-dp.png') }}" alt="" width="200" height="25">
                    </a>
                    <div class="d-flex align-items-center ms-4 mb-4">
                        <div class="position-relative">
                            <img class="rounded-circle" src="{{ asset('images/logoCircle.png') }}" alt="" style="width: 40px; height: 40px;">
                            <div class="bg-success rounded-circle border border-2 border-white position-absolute end-0 bottom-0 p-1"></div>
                        </div>
                        <div class="ms-3">
                            <h6 class="mb-0 text-dark">{{ auth()->user()->full_name }}</h6>
                        </div>
                    </div>
                    <div class="navbar-nav w-100">
                        @if(auth()->user()->role == '1')
                            <a href="{{ route('dashboard.index') }}" class="nav-item nav-link {{ Route::currentRouteNamed('dashboard.index') ? 'active' : '' }}"><i class="fa fa-tachometer-alt me-2"></i>Dashboard</a>
                            <a href="/categories" class="nav-item nav-link {{ Route::is('categories.*') ? 'active' : '' }}"><i class="fa-solid fa-list"></i>Thể loại bài viết</a>
                            <a href="{{ route('reporter.index') }}" class="nav-item nav-link {{ Route::currentRouteNamed('reporter.index','show.user','reporter.report') ? 'active' : '' }}"><i class="fas fa-users"></i>Phóng viên</a>
                            <a href="{{ route('list.user.approval') }}" class="nav-item nav-link {{ Route::currentRouteNamed('list.user.approval','show.user.approval') ? 'active' : '' }}"><i class="fa-solid fa-check"></i>Người duyệt bài</a>
                            <a href="{{ route('user.post.index') }}" class="nav-item nav-link {{ Route::currentRouteNamed('user.post.index','user.post.show') ? 'active' : '' }}"><i class="fa-solid fa-user-group"></i>Người lấy bài</a>
                        @elseif(auth()->user()->role == '2')
                            <a href="{{ route('dashboard.affiliate') }}" class="nav-item nav-link {{ Route::currentRouteNamed('dashboard.affiliate') ? 'active' : '' }}"><i class="fa fa-tachometer-alt me-2"></i>Quản lý lấy bài</a>
                        @elseif(auth()->user()->role == '3')
                            <a href="{{ route('dashboard.approver') }}" class="nav-item nav-link {{ Route::currentRouteNamed('dashboard.approver') ? 'active' : '' }}"><i class="fa fa-tachometer-alt me-2"></i>Quản lý duyệt bài</a>
                        @endif
                    </div>
                </nav>
            </div>
            <!-- Sidebar End -->
    
    
            <!-- Content Start -->
            <div class="content">
                <!-- Navbar Start -->
                <nav class="navbar navbar-expand bg-light navbar-light sticky-top px-4 py-0 d-flex justify-content-between" style="background: #F3F6F9 ">
                    <div class="d-flex">
                        <a href="{{ route('dashboard.index') }}" class="navbar-brand d-flex d-lg-none me-4">
                            <img class="rounded-circle" src="{{ asset('images/logoCircle.png') }}" alt="" style="width: 40px; height: 40px;">
                        </a>
                        <a href="#" class="sidebar-toggler flex-shrink-0">
                            <i class="fa fa-bars text-dark"></i>
                        </a>
                    </div>
                    <div class="navbar-nav align-items-center ms-auto">
                      
                        <div class="nav-item dropdown">
                            <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                                <img class="rounded-circle me-lg-2" src="{{ asset('images/logoCircle.png') }}" alt="" style="width: 40px; height: 40px;">
                                <span class="d-none d-lg-inline-flex">{{ auth()->user()->name }}</span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end bg-light border-0 rounded-0 rounded-bottom m-0">
                                @if (auth()->user()->role == 1)
                                    <a class="dropdown-item hover-dp" href="{{ route('approve.index') }}">
                                        Trang chủ
                                    </a>
                                @endif
                                @if (auth()->user()->role == 2)
                                    <a class="dropdown-item hover-dp" href="{{ route('get.posts') }}">
                                        Trang chủ
                                    </a>
                                @endif
                                <a class="dropdown-item hover-dp" href="{{ route('logout') }}"
                                    onclick="event.preventDefault();
                                    document.getElementById('logout-form').submit();">
                                    Logout
                                </a>

                                <form id="logout-form" action="{{ route('logout') }}" method="get" style="display: none;">
                                        @csrf
                                </form>
                            </div>
                        </div>
                    </div>
                </nav>
                <!-- Navbar End -->

                <main class="py-4">
                    @yield('content')
                </main>

            </div>
            <!-- Content End -->
    
    
            <!-- Back to Top -->
            <a style="background: #ffd6ea" class=" text-dark btn btn-lg btn-lg-square back-to-top"><i class="fas fa-chevron-up"></i></a>
        </div>
    </div>
    <script src="{{ asset('bootstrap/js/jquery-3.6.0.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <script src="{{ asset('js/main.js') }}"></script>
    <script src="{{ asset('lib/chart/chart.min.js') }}"></script>
    @stack('script-admin')
    <script>
        $(document).ready(function(){
            checkScroll();
            $(window).scroll(function(){
                checkScroll();
            });

            $('.back-to-top').click(function(e){
                e.preventDefault();
                $('html, body').animate({scrollTop:0}, 'slow');
            });

            function checkScroll() {
                if ($(window).scrollTop() > 20) {
                    $('.back-to-top').fadeIn();
                } else {
                    $('.back-to-top').fadeOut();
                }
            }
        });
    </script>
</body>
</html>