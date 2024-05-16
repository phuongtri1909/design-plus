@extends('admin.layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-offset-1 col-md-12">
                <div class="panel pb-2">
                    <div class="panel-heading">
                        <div>
                            @if($user->role == '0')
                                <a href="{{ route('reporter.index') }}" class="dp-color"><i class="fa-solid fa-arrow-left "></i> Trở lại</a>
                            @else
                                <a href="{{ route('user.post.index') }}" class="dp-color"><i class="fa-solid fa-arrow-left "></i> Trở lại</a>
                            @endif
                        </div>
                        <div class="row">
                            <div class="col-9 text-left">
                                <h4 class="title">Sửa Tài khoản: {{ $user->username }}</h4>
                            </div>
                            <div class="btn-action col-3  text-right">
                                <button class="btn btn-default btn-save" title="Save"><i class="fa-regular fa-floppy-disk"></i></button>
                            </div>
                        </div>
                    </div>
                    <form id="formEditUser" action="{{ route('update.user',$user->id) }}" method="POST" enctype="multipart/form-data" class="mb-3">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            {{-- <div class="form-group mb-2 col-12 col-md-6">
                                <div class="mx-2">
                                    <select id="role" class="text-left form-control input-create @error('role') is-invalid @enderror" name="role" >
                                        <option value="" disabled selected>Loại tài khoản</option>
                                        <option {{ old('role',$user->role)  == '0' ? 'selected' : '' }} value="0">Phóng viên</option>
                                        <option {{ old('role',$user->role)  == '1' ? 'selected' : '' }} value="1">Admin</option>
                                        <option {{ old('role',$user->role)  == '2' ? 'selected' : '' }} value="2">Người lấy bài</option>
                                       
                                    </select>
    
                                    @error('role')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div> --}}

                            <div class="form-group mb-2 col-12 col-md-6">
                                <div class="mx-2">
                                    <input id="full_name" type="text" class="input-create form-control @error('full_name') is-invalid @enderror" name="full_name" value="{{old('full_name',$user->full_name)}}" autocomplete="full_name" placeholder="Họ và tên">
    
                                    @error('full_name')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group mb-2 col-12 col-md-6">
                                <div class="mx-2">
                                    <select id="status" class="text-left form-control input-create @error('status') is-invalid @enderror" name="status" >
                                        <option value="" disabled selected>Trạng thái tài khoản</option>
                                        <option {{ old('status',$user->status)  == 'active' ? 'selected' : '' }} value="active">Kích hoạt</option>
                                        <option {{ old('status',$user->status)  == 'inactive' ? 'selected' : '' }} value="inactive">Không kích hoạt</option>
                                    </select>
    
                                    @error('status')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group mb-2 col-12 col-md-6">
                                <div class="mx-2">
                                    <div style="position: relative;">
                                        <input id="password" type="password" class="input-create form-control @error('password') is-invalid @enderror" name="password" value="{{old('password')}}" autocomplete="password" placeholder="Mật khẩu">
                                        <i id="togglePassword" class="fa fa-eye" style="position: absolute; top: 17px; right: 10px; cursor: pointer;" onclick="var x = document.getElementById('password'); if (x.type === 'password') { x.type = 'text'; this.classList.remove('fa-eye'); this.classList.add('fa-eye-slash'); } else { x.type = 'password'; this.classList.remove('fa-eye-slash'); this.classList.add('fa-eye'); }"></i>
                                    </div>
    
                                    @error('password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
 
                        </div>
                    </form>
                </div>
            </div>

            <div class="modal fade" id="messageModal" tabindex="-1" aria-labelledby="messageModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                    <h5 class="modal-title dp-color" id="messageModalLabel">Thông báo</h5>
                    
                    </div>
                    <div class="modal-body" id="messageContent">
                        <!-- Message will be inserted here -->
                    </div>
                </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script-admin')
    <script>
        $(document).ready(function() {
            $('.btn-save').click(function(e) {
                e.preventDefault();
                $('#formEditUser').submit();
            });
        });
        $(document).ready(function() {
            @if (session('success'))
                $('#messageContent').text('{{ session('success') }}');
                $('#messageModal').modal('show');
            @endif
            @if (session('error'))
                $('#messageContent').text('{{ session('error') }}');
                $('#messageModal').modal('show');
            @endif
        });
    </script>
@endpush