<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use App\Models\UserGetPost;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        $count_reporter = User::where('role', '0')->count();
        $count_get_post = User::where('role', '2')->count();
        $count_post = Post::count();
        $count_post_no_approval = Post::where('status_save_draft', '0')->where('send_approval', '1')->where('status_approval', '0')->count();
        $count_post_approval = Post::where('status_save_draft', '0')->where('send_approval', '1')->where('status_approval', '1')->count();
        $count_post_push = Post::where('status_save_draft', '0')->where('send_approval', '1')->where('status_approval', '1')->where('status_get_post', '1')->count();
        return view('admin.pages.dashboard')->with([
            'count_reporter' => $count_reporter,
            'count_get_post' => $count_get_post,
            'count_post' => $count_post,
            'count_post_no_approval' => $count_post_no_approval,
            'count_post_approval' => $count_post_approval,
            'count_post_push' => $count_post_push
        ]);
    }

    public function reporter_index()
    {
        $reporters = User::where('role', '0')
            ->withCount([
                'posts as total_posts',
                'posts as count_no_approval' => function ($query) {
                    $query->where('status_save_draft', '0')->where('send_approval', '1')->where('status_approval', '0');
                },
                'posts as count_approval' => function ($query) {
                    $query->where('status_save_draft', '0')->where('send_approval', '1')->where('status_approval', '1');
                },
                'posts as count_push_post' => function ($query) {
                    $query->where('status_save_draft', '0')->where('send_approval', '1')->where('status_approval', '1')->where('status_get_post', '1');
                }
            ])
            ->orderBy('created_at', 'desc')
            ->paginate(25);
        return view('admin.pages.reporter-index')->with('reporters', $reporters);
    }

    public function create_user()
    {
        return view('admin.pages.create-user');
    }

    public function store_user(Request $request)
    {
        $request->validate([
            'full_name' => 'required',
            'username' => 'required|unique:users',
            'password' => 'required|min:8',
            'status' => 'required|in:active,inactive',
            'role' => 'required|in:0,1,2'
        ], [
            'name.required' => 'Họ và tên không được để trống',
            'username.required' => 'Tài khoản không được để trống',
            'username.unique' => 'Tài khoản đã tồn tại',
            'password.required' => 'Mât khẩu không được để trống',
            'password.min' => 'Mật khẩu phải có ít nhất 8 ký tự',
            'status.required' => 'Trạng thái không được để trống',
            'status.in' => 'Trạng thái không hợp lệ',
            'role.required' => 'Role không được để trống',
            'role.in' => 'Role không hợp lệ'
        ]);

        try {
            $user = new User();
            $user->full_name = $request->full_name;
            $user->username = $request->username;
            $user->status = $request->status;
            $user->password = bcrypt($request->password);
            $user->role = $request->role;
            $user->save();
            if($user->role == 0)
            {
                return redirect()->route('reporter.index')->with('success', 'Tạo tài khoản phóng viên mới thành công.');
            }
            elseif($user->role == 2){
                return redirect()->route('user.post.index')->with('success', 'Tạo tài khoản người lấy bài mới thành công.');
            }
            else{
                return redirect()->route('dashboard.index')->with('success', 'Tạo tài khoản admin mới thành công.');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Có lỗi xảy ra, không thể tạo tài khoản mới, vui lòng thử lại sau.');
        }
    }

    public function edit_user($id)
    {
        $user = User::find($id);
        return view('admin.pages.edit-user')->with('user', $user);
    }

    public function update_user(Request $request, $id){
        $request->validate([
            'full_name' => 'required',
            'status'    => 'required|in:active,inactive',
            // 'role'      => 'required|in:0,1,2',
            'password'  => 'nullable|min:8'
        ], [
            'full_name.required' => 'Họ và tên không được để trống',
            'status.required' => 'Trạng thái không được để trống',
            'status.in' => 'Trạng thái không hợp lệ',
            'role.required' => 'Role không được để trống',
            'role.in' => 'Role không hợp lệ',
            'password.min' => 'Mật khẩu phải có ít nhất 8 ký tự'
        ]);

        try {
            $user = User::find($id);
            $user->full_name = $request->full_name;
            $user->status = $request->status;
            // $user->role = $request->role;
            if($request->password != null){
                $user->password = bcrypt($request->password);
            }
            $user->save();
            if($user->role == 0)
            {
                return redirect()->route('reporter.index')->with('success', 'Cập nhật tài khoản phóng viên ' . $user->username .' thành công.');
            }
            elseif($user->role == 2){
                return redirect()->route('user.post.index')->with('success', 'Cập nhật tài khoản người lấy bài ' . $user->username .' thành công.');
            }
            else{
                return redirect()->route('dashboard.index')->with('success', 'Cập nhật tài khoản admin ' . $user->username .' thành công.');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Có lỗi xảy ra, không thể cập nhật tài khoản, vui lòng thử lại sau.');
        }
    }

    public function show_user($id){
        $user = User::where('role','0')->find($id);

        if($user)
        {
            $user->loadCount([
                'posts as total_posts',
                'posts as count_no_approval' => function ($query) {
                    $query->where('status_save_draft', '0')->where('send_approval', '1')->where('status_approval', '0');
                },
                'posts as count_approval' => function ($query) {
                    $query->where('status_save_draft', '0')->where('send_approval', '1')->where('status_approval', '1');
                },
                'posts as count_push_post' => function ($query) {
                    $query->where('status_save_draft', '0')->where('send_approval', '1')->where('status_approval', '1')->where('status_get_post', '1');
                }
            ]);
        
            return view('admin.pages.show-user')->with('user', $user);
        }
        
        return redirect()->route('reporter.index')->with('error', 'Không tìm thấy tài khoản phóng viên.');
        
    }

    public function delete_user($id)
    {
       
        $user = User::find($id);
      
      
        if($user)
        {
            $user->loadCount('posts');
            if($user->role == '0' ){
                if($user->posts_count > 0){
                    return redirect()->route('reporter.index')->with('error', 'Không thể xóa tài khoản này khi đã có bài viết.');
                }
                else{
                    try {
                        $user->delete();
                        return redirect()->route('reporter.index')->with('success', 'Xóa tài khoản phóng viên ' . $user->username .' thành công.');
                    } catch (\Exception $th) {
                        return redirect()->back()->with('error', 'Có lỗi xảy ra, không thể xóa tài khoản phóng viên này, vui lòng thử lại sau.');
                    }  
                }
            }
          
            if($user->role == '2'){
                $userGetPost = UserGetPost::where('user_id', $user->id)->get();
               
                if(!$userGetPost->isEmpty())
                {
                    return redirect()->route('user.post.index')->with('error', 'Không thể xóa tài khoản người lấy bài khi đã lấy bài viết.');
                }
                else{
                    try {
                        $user->delete();
                        return redirect()->route('user.post.index')->with('success', 'Xóa tài khoản người lấy bài ' . $user->username .' thành công.');
                    } catch (\Exception $th) {
                        return redirect()->back()->with('error', 'Có lỗi xảy ra, không thể xóa tài khoản người lấy bài này, vui lòng thử lại sau.');
                    }  
                }
            }
        }
        return redirect()->back()->with('error', 'Không tìm thấy tài khoản.');
    }

    public function search_user(Request $request){
        $search = $request->search;
        $reporters = User::where('role', '0')
            ->where('full_name', 'like', '%'.$search.'%')
            ->withCount([
                'posts as total_posts',
                'posts as count_no_approval' => function ($query) {
                    $query->where('status_save_draft', '0')->where('send_approval', '1')->where('status_approval', '0');
                },
                'posts as count_approval' => function ($query) {
                    $query->where('status_save_draft', '0')->where('send_approval', '1')->where('status_approval', '1');
                },
                'posts as count_push_post' => function ($query) {
                    $query->where('status_save_draft', '0')->where('send_approval', '1')->where('status_approval', '1')->where('status_get_post', '1');
                }
            ])
            ->orderBy('created_at', 'desc')
            ->paginate(25);
        return response()->json([
            'data' => $reporters,
            'links' => $reporters->links('vendor.pagination.custom')->toHtml()
        ]);
    }

    public function user_post_index(){
        $userPost = User::where('role', '2')
            ->orderBy('created_at', 'desc')
            ->paginate(25);
        
        foreach ($userPost as $key => $user) {
           $user->count_post =  UserGetPost::with('user')->where('user_id', $user->id)->count();
        }

        return view('admin.pages.user-post-index')->with('userPost', $userPost);
    }

    public function user_post_show($id){
        $user = User::where('role','2')->find($id);

        if($user)
        {   
            $user->get_posts =  UserGetPost::where('user_id', $user->id)->paginate(30);
            foreach ($user->get_posts as $key => $get_post) {
                $get_post->post = $get_post->post;
                $get_post->post->user = $get_post->post->user;
            }
            
            return view('admin.pages.user-post-show')->with('user', $user);
        }
        
        return redirect()->route('user.post.index')->with('error', 'Không tìm thấy tài khoản người lấy bài.');
    }

    public function indexAffiliate()
    {
        $get_posts =  UserGetPost::where('user_id', auth()->user()->id)->paginate(30);
        foreach ($get_posts as $key => $get_post) {
            $get_post->post = $get_post->post;
            $get_post->post->user = $get_post->post->user;
        }    
            
        return view('admin.pages.dashboard-affiliate')->with('get_posts', $get_posts);
    }
}
