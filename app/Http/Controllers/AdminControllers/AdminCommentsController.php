<?php

namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Comment;
use Illuminate\Http\Response;


class AdminCommentsController extends Controller
{
    private  $rules = [
        'post_id' => 'required|numeric',
        'the_comment' => 'required|min:3|max:1000',
    ];

    public function index(Request $request)
    {
        $search = $request->input('search');
        $comments = Comment::leftJoin('users', 'comments.user_id', 'users.id')
            ->select(['comments.id', 'comments.the_comment', 'comments.post_id', 'comments.user_id', 'comments.created_at'])
            ->when($search, function ($query) use ($search) {
                $query->where(function ($qr) use ($search) {
                    $qr->where('the_comment', 'LIKE', "%$search%")
                        ->orWhere('users.name', 'LIKE', "%$search%");
                });
            })
            ->orderBy('comments.id', 'DESC')->latest()->paginate(20);
        return view('admin_dashboard.comments.index', compact('comments', 'search'));
        // return view('admin_dashboard.comments.index', [
        //     'comments' => Comment::latest()->paginate(20),
        // ]);
    }


    public function create()
    {
        return view('admin_dashboard.comments.create', [
            'posts' => Post::pluck('title', 'id'),
        ]);
    }



    public function store(Request $request)
    {

        $validated = $request->validate($this->rules);
        $validated['user_id'] = auth()->id();

        Comment::create($validated);
        return redirect()->route('admin.comments.create')->with('success', 'Thêm bình luận mới thành công.');
    }


    public function edit(Comment $comment)
    {
        return view('admin_dashboard.comments.edit', [
            'posts' => Post::pluck('title', 'id'),
            'comment' => $comment
        ]);
    }


    public function update(Request $request, Comment $comment)
    {
        $validated = $request->validate($this->rules);

        $comment->update($validated);
        return redirect()->route('admin.comments.edit', $comment)->with('success', 'Sửa bình luận mới thành công.');
    }


    public function destroy(Request $request)
    {
        try {
            $commentId = $request->input('id');
            if (empty($commentId)) {
                return response()->json([1], Response::HTTP_BAD_REQUEST);
            }
            $comment = Comment::where('id', $commentId)->first();
            if (empty($comment)) {
                return response()->json([2], Response::HTTP_BAD_REQUEST);
            }
            $comment->delete();
            return response()->json([], Response::HTTP_OK);
        } catch (\Throwable $th) {
            return response()->json([3], Response::HTTP_BAD_REQUEST);
        }
        // return redirect()->route('admin.comments.index')->with('success', 'Xóa bình luận mới thành công.');
    }

    //delete all
    public function deleteAll(Request $request)
    {
        $ids = $request->ids;
        Comment::whereIn('id', $ids)->delete();
        return response()->json(["success" => "Đã xoá các bình luận chọn thành công"]);
    }
}
