<?php

namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Category;
use Illuminate\Http\Response;

class AdminCategoriesController extends Controller
{

    private $rules = [
        'name' => 'required|min:3|max:30',
        'slug' => 'required|unique:categories,slug',
    ];

    public function index(Request $request)
    {
        $search = $request->input('search');
        $categories = Category::leftJoin('users', 'categories.user_id', 'users.id')
            ->select(['categories.name', 'categories.id', 'categories.user_id', 'categories.created_at'])
            ->when($search, function ($query) use ($search) {
                $query->where(function ($qr) use ($search) {
                    $qr->where('categories.name', 'LIKE', "%$search%")
                        ->orWhere('users.name', 'LIKE', "%$search%");
                });
            })
            ->orderBy('categories.id', 'ASC')->paginate(10);
        return view('admin_dashboard.categories.index', compact('categories', 'search'));
    }


    public function create()
    {
        return view('admin_dashboard.categories.create');
    }


    public function store(Request $request)
    {
        $validated = $request->validate($this->rules);
        $validated['user_id'] = auth()->id();
        Category::create($validated);

        return redirect()->route('admin.categories.create')->with('success', 'Thêm danh mục bài viết thành công.');
    }


    public function show(Category $category)
    {
        return view('admin_dashboard.categories.show', [
            'category' => $category
        ]);
    }


    public function edit(Category $category)
    {
        return view('admin_dashboard.categories.edit', [
            'category' => $category
        ]);
    }


    public function update(Request $request, Category $category)
    {
        $this->rules['slug'] = ['required', Rule::unique('categories')->ignore($category)];
        $validated = $request->validate($this->rules);

        $category->update($validated);

        return redirect()->route('admin.categories.edit', $category)->with('success', 'Sửa danh mục bài viết thành công.');
    }


    public function destroy(Request $request)
    {
        try {
            $categoryId = $request->input('id');
            if (empty($categoryId)) {
                return response()->json([], Response::HTTP_BAD_REQUEST);
            }
            $default_category_id = Category::where('name', 'Chưa phân loại')->first()->id;
            $category = Category::where('id', $categoryId)->first();
            if (empty($category)) {
                return response()->json([], Response::HTTP_BAD_REQUEST);
            }
            $category->posts()->update(['category_id' => $default_category_id]);
            $category->delete();
            return response()->json([], Response::HTTP_OK);
        } catch (\Throwable $th) {
            return response()->json([], Response::HTTP_BAD_REQUEST);
        }
    }

    // Hàm tạo slug tự động
    public function to_slug(Request $request)
    {
        $str = $request->name;
        $data['success'] = 1;
        $str = trim(mb_strtolower($str));
        $str = preg_replace('/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/', 'a', $str);
        $str = preg_replace('/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/', 'e', $str);
        $str = preg_replace('/(ì|í|ị|ỉ|ĩ)/', 'i', $str);
        $str = preg_replace('/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/', 'o', $str);
        $str = preg_replace('/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/', 'u', $str);
        $str = preg_replace('/(ỳ|ý|ỵ|ỷ|ỹ)/', 'y', $str);
        $str = preg_replace('/(đ)/', 'd', $str);
        $str = preg_replace('/[^a-z0-9-\s]/', '', $str);
        $str = preg_replace('/([\s]+)/', '-', $str);
        $data['message'] =  $str;
        return response()->json($data);
    }

    //delete all
    public function deleteAll(Request $request){
        $ids = $request->ids;
        Category::whereIn('id',$ids)->delete();
        return response()->json(["success" => "Đã xoá các danh mục chọn thành công"]);
    }
}
