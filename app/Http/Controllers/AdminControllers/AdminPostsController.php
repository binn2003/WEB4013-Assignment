<?php

namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class AdminPostsController extends Controller
{

    private $rules = [
        'title' => 'required|max:200',
        'slug' => 'required|max:200',
        'excerpt' => 'required|max:300',
        'category_id' => 'required|numeric',
        // 'thumbnail' => 'required|mimes:jpg,png,webp,svg,jpeg|dimensions:max-width:300,max-height:227',
        'body' => 'required',
    ];

    public function index(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');
        $listStatus = Post::getApproved();
        $posts = Post::leftJoin('categories', 'posts.category_id', 'categories.id')
            ->leftJoin('users', 'posts.user_id', 'users.id')
            ->select(['posts.id as post_id', 'posts.*', 'users.name as UserName'])
            ->when($search, function ($query) use ($search) {
                $query->where(function ($qr) use ($search) {
                    $qr->where('title', 'LIKE', "%$search%")
                        ->orWhere('categories.name', 'LIKE', "%$search%")
                        ->orWhere('users.name', 'LIKE', "%$search%");
                });
            })
            ->when($status !== null, function ($query) use ($status) {
                $query->where('posts.approved', $status);
            })
            ->orderBy('posts.id', 'ASC')->paginate(20);

        return view('admin_dashboard.posts.index', compact('posts', 'search', 'listStatus', 'status'));
    }

    public function create()
    {
        return view('admin_dashboard.posts.create', [
            'categories' => Category::pluck('name', 'id')
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->rules);
        $validated['user_id'] = auth()->id();
        $post = Post::create($validated);

        if ($request->has('thumbnail')) {
            $thumbnail = $request->file('thumbnail');
            $filename = $thumbnail->getClientOriginalName();
            $file_extension = $thumbnail->getClientOriginalExtension();
            $path   = $thumbnail->store('images', 'public');

            $post->image()->create([
                'name' => $filename,
                'extension' => $file_extension,
                'path' => $path
            ]);
        }

        $tags = explode(',', $request->input('tags'));
        $tags_ids = [];
        foreach ($tags as $tag) {
            $tag_ob = Tag::create(['name' => trim($tag)]);
            $tags_ids[]  = $tag_ob->id;
        }

        if (count($tags_ids) > 0)
            $post->tags()->sync($tags_ids);

        return redirect()->route('admin.posts.create')->with('success', 'Thêm bài viết thành công.');
    }

    public function show($id)
    {
        //
    }


    public function edit(Post $post)
    {
        $tags = '';
        foreach ($post->tags as $key => $tag) {
            $tags .= $tag->name;
            if ($key !== count($post->tags) - 1)
                $tags .= ', ';
        }
        $role = Auth::user()->role_id;
        $categories = Category::pluck('name', 'id', 'role');
        return view('admin_dashboard.posts.edit', compact('post', 'tags', 'role', 'categories'));
    }


    public function update(Request $request, Post $post)
    {
        $this->rules['thumbnail'] = 'nullable|file||mimes:jpg,png,webp,svg,jpeg|dimensions:max-width:800,max-height:300';
        $validated = $request->validate($this->rules);
        $validated['approved'] = $request->input('approved') !== null;
        $post->update($validated);

        if ($request->has('thumbnail')) {
            $thumbnail = $request->file('thumbnail');
            $filename = $thumbnail->getClientOriginalName();
            $file_extension = $thumbnail->getClientOriginalExtension();
            $path   = $thumbnail->store('images', 'public');

            $post->image()->update([
                'name' => $filename,
                'extension' => $file_extension,
                'path' => $path
            ]);
        }

        $tags = explode(',', $request->input('tags'));
        $tags_ids = [];
        foreach ($tags as $tag) {

            $tag_exits = $post->tags()->where('name', trim($tag))->count();
            if ($tag_exits == 0) {
                $tag_ob = Tag::create(['name' => $tag]);
                $tags_ids[]  = $tag_ob->id;
            }
        }

        if (count($tags_ids) > 0)
            $post->tags()->syncWithoutDetaching($tags_ids);

        return redirect()->route('admin.posts.edit', $post)->with('success', 'Sửa viết thành công.');
    }

    public function destroy(Request $request)
    {
        try {
            $postId = $request->input('id');
            if (empty($postId)) {
                return response()->json([1], Response::HTTP_BAD_REQUEST);
            }
            $post = Post::where('id', $postId)->first();
            if (empty($post)) {
                return response()->json([2], Response::HTTP_BAD_REQUEST);
            }
            $post->tags()->delete();
            $post->comments()->delete();
            $post->delete();
            return response()->json([], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }


    // Hàm tạo slug tự động
    public function to_slug(Request $request)
    {
        $str = $request->title;
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
    public function deleteAll(Request $request)
    {
        $ids = $request->ids;
        Post::whereIn('id', $ids)->delete();
        return response()->json(["success" => "Đã xoá các bài viết chọn thành công"]);
    }
}
