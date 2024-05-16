<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::orderBy('created_at', 'desc')->paginate(25);
        return view('admin.pages.categories.index')->with('categories', $categories);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        Return view('admin.pages.categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $slug = Str::slug($request->name);
        $this->validate($request, [
            'name' => 'required',
            'name' => 'unique:categories,name',
            'slug' => 'unique:categories,slug'
        ], [
            'name.required' => 'Vui lòng nhập tên thể loại bài viết.',
            'name.unique' => 'Tên thể loại bài viết đã tồn tại.',
            'slug.unique' => 'Slug đã tồn tại.'
        ]);

        try {
            $category = new Category;
            $category->name = $request->name;
            $category->slug = $slug;
            $category->save();

            return redirect()->route('categories.index')->with('success', 'Thêm thể loại bài viết thành công.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with(['error' => 'Có lỗi xảy ra, vui lòng thử lại.']);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $category = Category::where('id', $id)->first();
        if($category)
        {
            return view('admin.pages.categories.show')->with('category', $category);
        }
        return redirect()->route('categories.index')->with('error', 'Không tìm thấy thể loại bài viết.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $category = Category::where('id', $id)->first();
        if($category)
        {
            return view('admin.pages.categories.edit')->with('category', $category);
        }
        return redirect()->route('categories.index')->with('error', 'Không tìm thấy thể loại bài viết cần sửa.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required',
            'name' => 'unique:categories,name,'.$id,
            'slug' => 'unique:categories,slug,'.$id
        ], [
            'name.required' => 'Vui lòng nhập tên thể loại bài viết.',
            'name.unique' => 'Tên thể loại bài viết đã tồn tại.',
            'slug.unique' => 'Slug đã tồn tại.'
        ]);
        $category = Category::where('id', $id)->first();
        if($category)
        {
            try {
                $slug = Str::slug($request->name);
                $category->name = $request->name;
                $category->slug = $slug;
                $category->save();
            } catch (\Exception $e) {
                return redirect()->back()->with('error', 'Cập nhật thể loại không thành công: ' . $e->getMessage());
            }
            
            return redirect()->route('categories.index')->with('success', 'Cập nhật thể loại bài viết thành công.');
        }
        return redirect()->route('categories.index')->with('error', 'Không tìm thấy thể loại bài viết cần sửa.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $category = Category::where('id', $id)->first();
        if($category)
        {
            try {
                $category->delete();
                return redirect()->route('categories.index')->with('success', 'Xóa thể loại bài viết thành công.');
            } catch (\Exception $e) {
                return redirect()->back()->with('error', 'Xóa thể loại không thành công');
            }
           
        }
        return redirect()->route('categories.index')->with('error', 'Không tìm thấy thể loại bài viết cần xóa.');
    }

    public function search(Request $request)
    {
        $categories = Category::where('name', 'like', '%' . $request->search . '%')->orderBy('created_at', 'desc')->paginate(25);
        return response()->json([
            'data' => $categories,
            'links' => $categories->links('vendor.pagination.custom')->toHtml()
        ]);
    }
}
