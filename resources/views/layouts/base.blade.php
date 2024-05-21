<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>design plus</title>
   
    <link rel="icon" href="{{ asset('images/logoCircle.ico') }}" type="image/x-icon">

    <link href="{{ asset('bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('fontawesome-free-6.5.1-web/css/all.min.css') }}">
</head>
<body>
    <header>
        <div class="bg-header">
            <div class="header container container-dp">
                <div class="d-flex align-items-center justify-content-between">
                    <img src="{{ asset('images/logo-dp.png') }}" alt="" class="logo-dp" width="200" height="25">
                    <div class="d-none d-md-flex">
                        <div class=" dropdown">
                            <div class="nav-link dropdown-toggle text-dark btn-login" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                
                                @if (auth()->check())
                                    {{ auth()->user()->full_name }}
                                @else  
                                    Tài khoản
                                @endif
                            </div>
                            <ul class="dropdown-menu">
                                @if (auth()->check())
                                    <li><a class="dropdown-item" href="{{ route('logout') }}">Đăng xuất</a></li>
                                @else
                                    <li><a class="dropdown-item" href="{{ route('login') }}">Đăng nhập</a></li>
                                @endif
                            </ul>
                        </div>
                    </div>
                    <div class="d-md-none" >
                        <input type="checkbox" id="drawer-toggle" name="drawer-toggle"/>
                        <label for="drawer-toggle" id="drawer-toggle-label" class=""><i class="fa-solid fa-bars fa-xl"></i></label>
                        <nav id="drawer">
                            <div class="text-end">
                                <label for=""><i class="fa-solid fa-xmark fa-xl text-white closed"></i></label>
                            </div>
                            <ul class="d-flex  flex-column align-items-center pt-5 ps-0">
                                @if (auth()->user()->role == 0)
                                    <li><a class="item {{ Route::currentRouteName() == 'home' || Route::currentRouteName() == 'posts.allPosts' || Route::currentRouteName() == 'posts.edit' ? 'active' : '' }}" href="{{ route('home') }}">Phóng viên</a></li>
                                @endif
                                @if (auth()->user()->role == 1)
                                    <li><a class="item {{ Route::currentRouteName() == 'approve.index' ? 'active' : '' }}" href="{{ route('approve.index') }}">Phê duyệt</a></li>
                                    <li><a href="{{ route('dashboard.index') }}" class="item ">Dashboard</a></li>
                                @endif
                               
                                @if (auth()->user()->role == 2)
                                    <li><a class="item {{ Route::currentRouteName() == 'get.posts' ? 'active' : '' }}" href="{{ route('get.posts') }}">Lấy bài</a></li>
                                    <li><a class="item {{ Route::currentRouteName() == 'dashboard.affiliate' ? 'active' : '' }}" href="{{ route('dashboard.affiliate') }}">Dashboard</a></li>
                                @endif
                                @if (auth()->user()->role == 0 )
                                    <li><a class="item" >Hỗ trợ</a></li>
                                @endif
                            </ul>
                        </nav>
                    </div>
                </div>
                <div class="menu-desktop d-none d-md-flex pt-4 justify-content-around">
                    <ul class="d-flex align-items-center no-bullet">
                        @if (auth()->user()->role == 0)
                        <li>
                            <a href="{{ route('home') }}" class="item text-dark px-4 {{ Route::currentRouteName() == 'home' || Route::currentRouteName() == 'posts.allPosts' || Route::currentRouteName() == 'posts.edit' ? 'active' : '' }}">
                                Phóng viên
                            </a>
                        </li>
                        @endif
                        @if (auth()->user()->role == 1)
                        <li>
                            <a href="{{ route('approve.index') }}" class="item text-dark px-4 {{ Route::currentRouteName() == 'approve.index' ? 'active' : '' }}">
                                Phê duyệt
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('dashboard.index') }}" class="item text-dark px-4">
                                Dashboard
                            </a>
                        </li>
                        @endif
                        @if (auth()->user()->role == 2 )
                            <li><a href="{{ route('get.posts') }}" class="item text-dark px-4 {{ Route::currentRouteName() == 'get.posts' ? 'active' : '' }}">Lấy bài</a></li>
                            <li><a href="{{ route('dashboard.affiliate') }}" class="item text-dark px-4 {{ Route::currentRouteName() == 'dashboard.affiliate' ? 'active' : '' }}">Dashboard</a></li>
                        @endif
                        @if (auth()->user()->role == 0 )
                            <li><a class="item text-dark px-4 support">Hỗ trợ</a></li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </header>
    <main class="container container-dp">
        @yield('content')
        <a style="background: #ffd6ea" class=" text-dark btn btn-lg btn-lg-square back-to-top"><i class="fas fa-chevron-up"></i></a>
    </main>
    
    <footer class="py-5 ">
        <div class="container container-lv2-dp">
            <div class="row">
                <div class="col-12 col-xxl-6 text-white d-flex flex-column align-items-center d-xxl-block">
                    <h1 class="text-white pb-3">CONTACT</h1>
                    <p class="d-flex pb-3"><span class="pe-2">info@my-domain.com</span> / <span class="ps-2">123-456-7890</span></p>
                    <div class="info-network pb-3">
                        <a href="#" class="text-white"><i class="fab fa-instagram fa-xl"></i></a>
                        <a href="#" class="text-white"><i class="fab fa-facebook-f fa-xl"></i></a>
                        <a href="#" class="text-white"><i class="fa-brands fa-pinterest-p fa-xl"></i></a>
                        <a href="#" class="text-white"><i class="fa-brands fa-pinterest-p fa-xl"></i></a>
                    </div>
                </div>
                <div class="col-12 col-xxl-6">
                    <form action="#" method="post">
                        <div class="d-flex flex-column align-items-end">
                            <input type="text"  class="form-control mb-1" name="name" placeholder="Name">
                            <input type="email" class="form-control mb-1" name="email" placeholder="Email">
                            <input type="text" class="form-control mb-1" name="subject" placeholder="Subject">
                            <textarea name="message" class="form-control mb-1" id="" cols="20" rows="5" placeholder="Message"></textarea>
                            <input class="btn-submit-footer" type="submit" placeholder="SUBMIT">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </footer>
    
    <script src="{{ asset('bootstrap/js/@500bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('bootstrap/js/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ asset('bootstrap/js/@502bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('bootstrap/js/popper.min.js') }}"></script>
    <script src="{{ asset('bootstrap/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('js/app.js') }}"></script>
    @vite('resources/js/app.js')
    @stack('scripts')
    <script>
        $(document).ready(function(){
            $('.support').click(function(e){
                e.preventDefault();
                $('html, body').animate({scrollTop:$(document).height()}, 'slow');
            });

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
