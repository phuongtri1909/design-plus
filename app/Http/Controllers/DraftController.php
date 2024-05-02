<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Draft;
use App\Models\PostImages;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Requests\PostRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DraftController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
            'category_id' => 'required|integer|exists:categories,id',
        ]);

        DB::beginTransaction();

        try {
            $post = Post::where('user_id', auth()->id())
                    ->where('status_save_draft', 1)
                    ->first();
            $slug = Str::slug($request->title);
            $originalSlug = $slug;
            $count = 1;
            while (Post::query()->where('slug', $slug)->exists()) {
                $slug = "{$originalSlug}-" . $count++;
            }
            if ($post) {
                foreach ($post->postImages as $image) {
                    Storage::delete('public/' . $image->image);
                    $image->delete();
                }

                $post->update($request->all() + ['slug' => $slug]);
            } else {
                $post = Post::create($request->all() + ['status_save_draft' => 1, 'user_id' => auth()->id(),'slug' => $slug]);
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
            
            
            $draft = $post->draft;
            if ($draft) {
                $draft->update(['user_id' => auth()->id()]);
            } else {
                $post->draft()->create(['user_id' => auth()->id()]);
            }
            
            DB::commit();
            return back()->with('success', 'Lưu tạm bài viết thành công.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Draft $draft)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Draft $draft)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Draft $draft)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Draft $draft)
    {
        //
    }
}
