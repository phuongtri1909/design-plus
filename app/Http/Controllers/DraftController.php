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
     * Transliterate special Unicode characters to ASCII
     */
    private function transliterate($text)
    {
        $normalized = normalizer_normalize($text, \Normalizer::FORM_KD);
        
        $transliterationMap = [
            'à' => 'a', 'á' => 'a', 'ạ' => 'a', 'ả' => 'a', 'ã' => 'a',
            'â' => 'a', 'ầ' => 'a', 'ấ' => 'a', 'ậ' => 'a', 'ẩ' => 'a', 'ẫ' => 'a',
            'ă' => 'a', 'ằ' => 'a', 'ắ' => 'a', 'ặ' => 'a', 'ẳ' => 'a', 'ẵ' => 'a',
            'è' => 'e', 'é' => 'e', 'ẹ' => 'e', 'ẻ' => 'e', 'ẽ' => 'e',
            'ê' => 'e', 'ề' => 'e', 'ế' => 'e', 'ệ' => 'e', 'ể' => 'e', 'ễ' => 'e',
            'ì' => 'i', 'í' => 'i', 'ị' => 'i', 'ỉ' => 'i', 'ĩ' => 'i',
            'ò' => 'o', 'ó' => 'o', 'ọ' => 'o', 'ỏ' => 'o', 'õ' => 'o',
            'ô' => 'o', 'ồ' => 'o', 'ố' => 'o', 'ộ' => 'o', 'ổ' => 'o', 'ỗ' => 'o',
            'ơ' => 'o', 'ờ' => 'o', 'ớ' => 'o', 'ợ' => 'o', 'ở' => 'o', 'ỡ' => 'o',
            'ù' => 'u', 'ú' => 'u', 'ụ' => 'u', 'ủ' => 'u', 'ũ' => 'u',
            'ư' => 'u', 'ừ' => 'u', 'ứ' => 'u', 'ự' => 'u', 'ử' => 'u', 'ữ' => 'u',
            'ỳ' => 'y', 'ý' => 'y', 'ỵ' => 'y', 'ỷ' => 'y', 'ỹ' => 'y',
            'đ' => 'd',
            'À' => 'A', 'Á' => 'A', 'Ạ' => 'A', 'Ả' => 'A', 'Ã' => 'A',
            'Â' => 'A', 'Ầ' => 'A', 'Ấ' => 'A', 'Ậ' => 'A', 'Ẩ' => 'A', 'Ẫ' => 'A',
            'Ă' => 'A', 'Ằ' => 'A', 'Ắ' => 'A', 'Ặ' => 'A', 'Ẳ' => 'A', 'Ẵ' => 'A',
            'È' => 'E', 'É' => 'E', 'Ẹ' => 'E', 'Ẻ' => 'E', 'Ẽ' => 'E',
            'Ê' => 'E', 'Ề' => 'E', 'Ế' => 'E', 'Ệ' => 'E', 'Ể' => 'E', 'Ễ' => 'E',
            'Ì' => 'I', 'Í' => 'I', 'Ị' => 'I', 'Ỉ' => 'I', 'Ĩ' => 'I',
            'Ò' => 'O', 'Ó' => 'O', 'Ọ' => 'O', 'Ỏ' => 'O', 'Õ' => 'O',
            'Ô' => 'O', 'Ồ' => 'O', 'Ố' => 'O', 'Ộ' => 'O', 'Ổ' => 'O', 'Ỗ' => 'O',
            'Ơ' => 'O', 'Ờ' => 'O', 'Ớ' => 'O', 'Ợ' => 'O', 'Ở' => 'O', 'Ỡ' => 'O',
            'Ù' => 'U', 'Ú' => 'U', 'Ụ' => 'U', 'Ủ' => 'U', 'Ũ' => 'U',
            'Ư' => 'U', 'Ừ' => 'U', 'Ứ' => 'U', 'Ự' => 'U', 'Ử' => 'U', 'Ữ' => 'U',
            'Ỳ' => 'Y', 'Ý' => 'Y', 'Ỵ' => 'Y', 'Ỷ' => 'Y', 'Ỹ' => 'Y',
            'Đ' => 'D',
        ];
        
        $result = strtr($normalized, $transliterationMap);
        
        $result = preg_replace('/[^\x20-\x7E]/', '', $result);
        
        return trim($result);
    }

    /**
     * Generate slug from title, handling special characters
     */
    private function generateSlug($title)
    {
        $slug = Str::slug($title);
        
        if (empty($slug)) {
            $transliterated = $this->transliterate($title);
            if (!empty($transliterated)) {
                $slug = Str::slug($transliterated);
            }
        }
        
        if (empty($slug)) {
            $cleanTitle = preg_replace('/[^\p{L}\p{N}\s\-_]/u', '', $title);
            $cleanTitle = trim($cleanTitle);
            
            if (!empty($cleanTitle)) {
                $slug = Str::slug($cleanTitle);
            }
        }
        
        return $slug;
    }

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
            'post_type'   => 'nullable|in:new,translation',
        ]);

        DB::beginTransaction();

        try {
            $post = Post::where('user_id', auth()->id())
                    ->where('status_save_draft', 1)
                    ->first();
            $slug = $this->generateSlug($request->title);
            
            if (empty($slug)) {
                $slug = 'draft-' . time();
            }
            
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
