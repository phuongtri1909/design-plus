<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use App\Models\Category;
use App\Models\UserApprovalPost;
use App\Models\UserGetPost;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use League\CommonMark\Extension\CommonMark\Parser\Inline\BacktickParser;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categorys = Category::all();
        $post_draft = Post::where('user_id', auth()->id())
            ->where('status_save_draft', 1)
            ->first();
        return view('pages.page-reporter')->with('categorys', $categorys)->with('post_draft', $post_draft);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title'         => 'required|string',
            'post_type'     => 'required|in:new,translation',
            'category_id'   => 'required|integer|exists:categories,id',
            'brief_intro'   => 'required|string',
            'content'       => 'required|string',
            "file"          => "required|array",
            "file.*"        => "image|mimes:jpeg,png,jpg",
        ],[
            'title.required'        => 'Tựa đề không được để trống',
            'title.string'          => 'Tựa đề phải là chuỗi ký tự',
            'post_type.required'    => 'Loại tin không được để trống',
            'post_type.in'          => 'Loại bài viết không hợp lệ',
            'category_id.required'  => 'Bạn chưa chọn thể loại',
            'category_id.integer'   => 'Thể loại phải là số nguyên',
            'category_id.exists'    => 'Thể loại không tồn tại',
            'brief_intro.required'  => 'Mô tả ngắn không được để trống',
            'brief_intro.string'    => 'Mô tả ngắn phải là chuỗi ký tự',
            'content.required'      => 'Nội dung không được để trống',
            'content.string'        => 'Nội dung phải là chuỗi ký',
            'file.required'         => 'Ảnh không được để trống',
            'file.*.image'          => 'File không đúng định dạng ảnh',
            'file.*.mimes'          => 'File không đúng định dạng ảnh',
        ]);

        DB::beginTransaction();
        try {
            $user = auth()->user();
            $post_draft = $user->draft()->first();

            $slug = Str::slug($request->title);
            $originalSlug = $slug;
            $count = 1;
            while (Post::query()->where('slug', $slug)->exists()) {
                $slug = "{$originalSlug}-" . $count++;
            }
            
            if ($post_draft) {
                $post = $user->post()->where('id', $post_draft->post_id)->first();
                if($post)
                {
                    foreach ($post->postImages as $image) {
                        Storage::delete('public/' . $image->image);
                        $image->delete();
                    }
                    $post->update($request->all() + ['status_save_draft' => '0','slug' => $slug]);
                    $post_draft->delete();
                }
            } else {
                $post = Post::create($request->all() + ['status_save_draft' => '0', 'user_id' => auth()->id(),'slug' => $slug]);
            }
            if($request->hasFile('file')) {
                foreach ($request->file as $file) {
                    $folderName = date('Y/m');
                    $originalFileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                    $extension = $file->getClientOriginalExtension();
                    $fileName = $originalFileName . '_' . time() . '.' . $extension;
                    $file->storeAs('public/uploads/' . $folderName, $fileName);
                   
                    $post->postImages()->create(['image' => 'uploads/' . $folderName . '/' . $fileName]);
                }
            }
            DB::commit();
            return back()->with('success', 'Lưu bài viết thành công');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()->with('error', $e->getMessage());
        }
        
        
    }

    /**
     * Display the specified resource.
     */
    public function show($slug)
    {
        $user = auth()->user();

        if($user->role == '0')
        {
            $post = $user->posts()->where('slug', $slug)->first();
        }
        elseif($user->role == '1' || $user->role == '3'){
            $post = Post::where('slug', $slug)->where('status_save_draft','0')->where('send_approval','1')->first();
        }
        elseif($user->role == '2'){
            $post = Post::where('slug', $slug)->where('status_save_draft','0')->where('send_approval','1')->where('status_approval','1')->first();
        }

        if (!$post) {
            return back()->with('error', 'Không tìm thấy bài viết');
        }

        $postTypeMapping = [
            'translation' => 'Bài dịch',
            'new' => 'Bài viết mới',
        ];

        $post->post_type = $postTypeMapping[$post->post_type] ?? $post->post_type;
        $PostImages = $post->postImages;
        return view('pages.page-post')->with('post', $post)->with('PostImages', $PostImages);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($slug)
    {
        $user = auth()->user();
        $categorys = Category::all();
        $post = $user->posts()->where('slug', $slug)->first();
        if(!$post){
            return back()->with('error', 'Không tìm thấy bài viết');
        }
        return view('pages.page-edit-post')->with('post', $post)->with('categorys', $categorys);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request,$slug)
    {   
        $request->validate([
            'title' => 'required|string',
            'post_type' => 'required|in:new,translation',
            'category_id' => 'required|integer|exists:categories,id',
            'brief_intro' => 'required|string',
            'content' => 'required|string',
            "file" => "required|array",
            "file.*" => "image|mimes:jpeg,png,jpg",
        ],[
            'title.required' => 'Tựa đề không được để trống',
            'title.string' => 'Tựa đề phải là chuỗi ký tự',
            'post_type.required' => 'Loại tin không được để trống',
            'post_type.in' => 'Loại tin không hợp lệ',
            'category_id.required' => 'Bạn chưa chọn thể loại',
            'category_id.integer' => 'Thể loại phải là số nguyên',
            'category_id.exists' => 'Thể loại không tồn tại',
            'brief_intro.required' => 'Mô tả ngắn không được để trống',
            'brief_intro.string' => 'Mô tả ngắn phải là chuỗi ký tự',
            'content.required' => 'Nội dung không được để trống',
            'content.string' => 'Nội dung phải là chuỗi ký',
            'file.required' => 'Ảnh không được để trống',
            'file.*.image' => 'File không đúng định dạng ảnh',
            'file.*.mimes' => 'File không đúng định dạng ảnh',
        ]);
        $user = auth()->user();
        $post = $user->posts()->where('slug', $slug)->first();
        if(!$post){
            return back()->with('error', 'Cập nhật bài viết thành công');
        }else if($post->status_save_draft == 0 && $post->send_approval == 0 || $post->status_save_draft == 0 && $post->send_approval == 1 && $post->status_approval == 2){
            DB::beginTransaction();
            try {
                foreach ($post->postImages as $image) {
                    Storage::delete('public/' . $image->image);
                    $image->delete();
                }
                if($request->hasFile('file')) {
                    foreach ($request->file as $file) {
                        $folderName = date('Y/m');
                        $originalFileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                        $extension = $file->getClientOriginalExtension();
                        $fileName = $originalFileName . '_' . time() . '.' . $extension;
                        $file->storeAs('public/uploads/' . $folderName, $fileName);
                        $file->move(public_path('storage/uploads/' . $folderName), $fileName);
                        $post->postImages()->create(['image' => 'uploads/' . $folderName . '/' . $fileName]);
                    }
                }
                $post->update($request->all());
                DB::commit();
                return back()->with('success', 'Cập nhật bài viết thành công');
            } catch (\Exception $e) {
                DB::rollback();
                return back()->withInput()->with('error', $e->getMessage());
            }
        }
        
        return back()->with('error', 'Không thể cập nhật bài viết');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $user = auth()->user();
        $post = $user->posts()->find($id);
       
        if(!$post)
        {
            return back()->with('error', ' Thao tác xóa không đúng, thử lại sau');
        }
        else if ($post && (
            ($post->status_save_draft == '0' && $post->send_approval == '0' && $post->status_approval == '0' && $post->status_get_post == '0')
            || ($post->status_save_draft == '0' && $post->send_approval == '1' && $post->status_approval == '2' && in_array($post->status_no_approval, ['0','1']))
        )) {
            foreach ($post->postImages as $image) {
                Storage::delete('public/' . $image->image);
            }
            $post->delete();
            return back()->with('success', 'Xóa bài viết thành công');
        }        
        return back()->with('error', ' Không thể xóa bài viết');
    }

    public function recall($id){
        $user = auth()->user();
        $post = $user->posts()->find($id);
        if(!$post){
            return back()->with('error', 'Không thể thu hồi bài viết');
        }
        if($post && $post->status_save_draft == '0' && $post->send_approval == '1' && $post->status_approval == '0'){
            $post->update(['send_approval' => '0']);
            return back()->with('success', 'Thu hồi bài viết thành công');
        }
        return back()->with('error', 'Không thể thu hồi bài viết');
    }

    public function send($id){
        $user = auth()->user();
        $post = $user->posts()->find($id);
        if(!$post){
            return back()->with('error', 'Không thể chuyển duyệt bài viết, thử lại sau');
        }
        if($post && $post->status_save_draft == '0' && $post->send_approval == '0' && $post->status_approval == '0'){
            $post->update(['send_approval' => '1','send_post_at' => now()->toDateTimeString()]);
            return back()->with('success', 'Chuyển duyệt bài viết thành công');
        }
        return back()->with('error', 'Không thể chuyển duyệt bài viết, thử lại sau');
    }

    public function resend($id)
    {
        $user = auth()->user();
        $post = $user->posts()->find($id);
        if(!$post){
            return back()->with('error', 'Không thể chuyển duyệt lại bài viết, thử lại sau');
        }
        if($post && $post->status_save_draft == '0' && $post->send_approval == '1' && $post->status_approval == '2'){
            $post->update(['status_approval' => '0']);
            return back()->with('success', 'Chuyển duyệt lại bài viết thành công');
        }
        return back()->with('error', 'Không thể chuyển duyệt lại bài viết, thử lại sau');
    }

    public function allPosts(){
        $user = auth()->user();
        $list_posts = $user->posts()->where('status_save_draft', 0)->orderBy('created_at', 'desc')->paginate(20);
        return view('pages.page-posts')->with('list_posts', $list_posts);
    }

    public function classify(Request $request){
       
        $user = auth()->user();
        $classify = $request->classify;
        $status = $request->status;
        $duration = $request->duration;
        $post_classify = $user->posts()->where('status_save_draft', '0')
        ->when(isset($classify), function ($query) use ($classify) {
            if($classify == 0){
                return $query->where(function ($query) {
                $query->where('send_approval', '0')
                ->where('status_approval', '0')
                ->where('status_get_post', '0');
                })
                ->orWhere(function ($query) {
                    $query->where('send_approval', '1')
                        ->where('status_approval', '2');
                });
            }
            else if($classify == 1){
                return $query->where('send_approval', '1')->where('status_approval', '0');
            }
            else if($classify == 2){
                return $query->where('status_approval', '1')->where('status_get_post', '1');
            }
           
        })
        ->when(isset($status), function ($query) use ($status) {
            return $query->where('send_approval', '!=', '0')->where('status_approval', $status);
        })
        ->when($duration, function ($query, $duration) {
            if ($duration == 1) {
                return $query->whereMonth('created_at', now()->month)
                             ->whereYear('created_at', now()->year);
            } else {
                return $query->whereBetween('created_at', [now()->subMonths($duration), now()->subMonths($duration - 1)]);
            }
        })
        ->orderBy('created_at', 'desc')
        ->paginate(20);
      
        return response()->json([
            'data' => $post_classify->items(),
            'links' => $post_classify->links('vendor.pagination.custom')->toHtml(),
        ]);
    }

    public function approve_index(){
        $list_posts = Post::where('status_save_draft', '0')->where('send_approval', '1')->orderBy('updated_at', 'desc')->paginate(30);
        $reporters = User::where('role', '0')->get();
        return view('pages.page-approve')->with('list_posts', $list_posts)->with('reporters', $reporters);
    }

    public function approve_list(Request $request){
        
        $validator = Validator::make($request->all(), [
            'operation' => 'required|in:1,2',
            'posts' => 'required|array',
        ],[
            'operation.required' => 'Vui lòng chọn thao tác',
            'operation.in' => 'Thao tác không hợp lệ',
            'posts.required' => 'Vui lòng chọn bài viết',
            'posts.array' => 'Danh sách bài viết không hợp lệ',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors(),
            ], 400);
        }

        foreach ($request->posts as $post) {
            $post = Post::find($post);
            
            if($post->status_save_draft == '0' && $post->send_approval == '1' && $post->status_approval == '0'){
                DB::beginTransaction();
                try {
                    if($request->operation == 1){
                        $post->update(['status_approval' => '2','approval_at' => now()->toDateTimeString()]);
                    }
                    else if($request->operation == 2){
                        $post->update(['status_approval' => '1','approval_at' => now()->toDateTimeString()]);
                        $userApprove = UserApprovalPost::create(['user_id' => auth()->id(),'post_id' => $post->id]);
                    }
                    DB::commit();
                } catch (\Exception $e) {
                    DB::rollback();
                    return response()->json([
                        'error' => 'Không thể thực hiện thao tác',
                    ], 500);
                }
            }
        }
        $list_posts = Post::with('user')
        ->where('status_save_draft', '0')->where('send_approval', '1')->orderBy('created_at', 'desc')->paginate(30);

        return response()->json([
            'data'    => $list_posts->items(),
            'links' => $list_posts->links('vendor.pagination.custom')->toHtml(),
            'message' => $request->operation == 1 ? 'Không duyệt bài thành công' : 'Duyệt bài thành công',
        ],200);
    }

    public function handleApproveAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:approve,notAchieved',
            'post_slug' => 'required|string|exists:posts,slug',
        ],[
            'action.required' => 'Vui lòng chọn thao tác',
            'action.in' => 'Thao tác không hợp lệ',
            'post_slug.required' => 'Bài viết không tồn tại',
            'post_slug.string' => 'Bài viết không tồn tại',
            'post_slug.exists' => 'Bài viết không tồn tại',
        
        ]);
        $action = $request->input('action');
        $postSlug = $request->input('post_slug');

        $post = Post::where('slug', $postSlug)->first();

        if (!$post) {
            return back()->withErrors(['error' => 'Bài viết không tồn tại']);
        } 
        DB::beginTransaction();
        try {
            if ($action == 'approve' && auth()->user()->role == '1' || $action == 'approve' && auth()->user()->role == '3'){
                $post->update(['status_approval' => '1','approval_at' => now()->toDateTimeString()]);
                $userApprove = UserApprovalPost::create(['user_id' => auth()->id(),'post_id' => $post->id]);
            } elseif ($action == 'notAchieved' && auth()->user()->role == '1' || $action == 'notAchieved' && auth()->user()->role == '3') {
                $post->update(['status_approval' => '2','approval_at' => now()->toDateTimeString(),'count_no_approval' => $post->count_no_approval + 1]);
            }
            else{
                DB::rollback();
                return back()->withErrors(['error' => 'Không thể thực hiện thao tác']);
            }
        
           DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Có lỗi xảy ra trong quá trình xử lý']);
        }

        return back()->with('success', $action == 'approve' ? 'Duyệt bài viết thành công' : ($action == 'notAchieved' ? 'Hủy bài viết thành công' : 'Đăng bài viết thành công'));
    }

    public function approve_classify(Request $request){
        $list_posts = Post::with('user')
        ->where('status_save_draft', '!=', '1')
        ->where('send_approval', '!=', '0')
        ->when($request->selected_reporter, function ($query, $selected_reporter) {
            return $query->where('user_id', $selected_reporter);
        })
        ->when(isset($request->selected_status), function ($query) use ($request) {
            return $query->where('status_approval', $request->selected_status);
        })
        ->when($request->selected_time, function ($query, $selected_time) {
            if ($selected_time == 1) {
                return $query->whereMonth('created_at', now()->month)
                            ->whereYear('created_at', now()->year);
            } else {
                return $query->whereBetween('created_at', [now()->subMonths($selected_time), now()->subMonths($selected_time - 1)]);
            }
        })
        ->orderBy('created_at', 'desc')
        ->paginate(30);
       
        return response()->json([
            'data' => $list_posts->items(),
            'links' => $list_posts->links('vendor.pagination.custom')->toHtml(),
        ]);
    }

    public function getPosts(){
        $list_posts = Post::where('status_save_draft', '0')
        ->where('send_approval', '1')
        ->where('status_approval', '1')
        ->orderBy('approval_at', 'desc')
        ->paginate(20);

        foreach ($list_posts as $index => $post) {
            $post->category_name = $post->category->name;
        }

        $reporters = User::where('role', '0')->get();
        return view('pages.page-get-post')->with('list_posts', $list_posts)->with('reporters', $reporters);
    }

    public function getPost_classify(Request $request){
        $list_posts = Post::where('status_save_draft', '!=', '1')->where('send_approval', '!=', '0')->where('status_approval', '1')
        ->when($request->selected_reporter, function ($query, $selected_reporter) {
            return $query->where('user_id', $selected_reporter);
        })
        ->when($request->selected_time, function ($query, $selected_time) {
            if ($selected_time == 1) {
                return $query->whereMonth('created_at', now()->month)
                             ->whereYear('created_at', now()->year);
            } else {
                return $query->whereBetween('created_at', [now()->subMonths($selected_time), now()->subMonths($selected_time - 1)]);
            }
        })
        ->orderBy('created_at', 'desc')
        ->paginate(20);

        foreach ($list_posts as $index => $post) {
            $list_posts[$index] = $post->load('user');
        }

      

        return response()->json([
            'data' => $list_posts->items(),
            'links' => $list_posts->links('vendor.pagination.custom')->toHtml(),
        ]);
    }

    public function pushPost(Request $request)
    {
       
        $validator = Validator::make($request->all(), [
            'post_id' => 'required|integer|exists:posts,id',
            'link' => 'required|url',
        ],[
            'post_id.required' => 'Bài viết không tồn tại',
            'post_id.integer' => 'Bài viết không tồn tại',
            'post_id.exists' => 'Bài viết không tồn tại',
            'link.required' => 'Link không được để trống',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->with('error', $validator->errors()->first());
        }

        $post = Post::where('id', $request->post_id)->first();

        if($post)
        {
            try {
                if(auth()->user()->role == '2'){
                    DB::beginTransaction();

                    $post->update(['status_get_post' => '1','get_post_at' => now()->toDateTimeString(),'link' => $request->link]);
                    UserGetPost::create(['user_id' => auth()->id(),'post_id' => $request->post_id]);

                    DB::commit();

                    return back()->with('success', 'Đăng bài viết thành công');
                }
                else{
                    return back()->with('error', 'Không thể đăng bài viết');
                }
            } catch (\Exception $th) {
                DB::rollback();
                return back()->with('error', 'Không thể đăng bài viết');
            }
        }
        return back()->with('error', 'Không thể đăng bài viết');
    }

    public function saveLink(Request $request){
        
        $request->validate([
            'web_link' => 'required|url',
            'post_id' => 'required|integer|exists:posts,id',
        ],[
            'web-link.required' => 'Link không được để trống',
            'web-link.url' => 'Link không hợp lệ',
            'post_id.required' => 'Bài viết không tồn tại',
            'post_id.integer' => 'Bài viết không tồn tại',
            'post_id.exists' => 'Bài viết không tồn tại',
        ]);

        $post = Post::where('id', $request->post_id)
        ->where('status_save_draft', '0')
        ->where('send_approval', '1')
        ->where('status_approval', '1')
        ->where('status_get_post', '1')
        ->first();
        if(!$post){
            return back()->with('error', 'Không thể lưu link');
        }
      
        $updateSuccess = $post->update(['link' => $request->web_link]);

        if (!$updateSuccess) {
            return back()->with('error', 'Có lỗi xảy ra khi cố gắng cập nhật link');
        }

        return back()->with('success', 'Lưu link thành công');
    }
}
